<?php
/**
 * Customer account: profile, bookings, favourites, password.
 */

function account_page(): void {
    $user = auth_user();
    if (!$user) { $_SESSION['intended'] = '/account'; redirect('/login'); }

    $tab = (string) input('tab', 'overview');
    if (!in_array($tab, ['overview', 'bookings', 'favorites', 'profile'], true)) $tab = 'overview';

    $bookings = db_all(
        "SELECT b.*, t.title AS tour_title, h.name AS hotel_name
         FROM bookings b
         LEFT JOIN tours t ON t.id = b.tour_id
         LEFT JOIN hotels h ON h.id = b.hotel_id
         WHERE b.user_id = ?
         ORDER BY b.created_at DESC",
        [(int) $user['id']]
    );

    $favs = db_all(
        "SELECT w.id AS wish_id, w.target_type, w.target_id,
                t.title AS t_title, t.slug AS t_slug, t.cover_image AS t_img, t.price AS t_price,
                h.name AS h_title, h.slug AS h_slug, h.cover_image AS h_img, h.base_price AS h_price, h.currency AS h_cur
         FROM wishlists w
         LEFT JOIN tours t ON w.target_type = 'tour' AND t.id = w.target_id
         LEFT JOIN hotels h ON w.target_type = 'hotel' AND h.id = w.target_id
         WHERE w.user_id = ?
         ORDER BY w.created_at DESC",
        [(int) $user['id']]
    );

    $notifications = db_all(
        "SELECT * FROM notifications WHERE user_id = ? ORDER BY created_at DESC LIMIT 15",
        [(int) $user['id']]
    );

    $stats = [
        'bookings' => count($bookings),
        'upcoming' => count(array_filter($bookings, fn($b) => in_array($b['status'], ['pending', 'confirmed'], true))),
        'favourites' => count($favs),
        'points' => (int) $user['loyalty_points'],
    ];

    render('account/page', [
        'title' => 'My Account | Rwanda Explorer',
        'description' => 'Manage your bookings, profile and favourite trips.',
        'active' => 'account',
        'tab' => $tab,
        'user' => $user,
        'bookings' => $bookings,
        'favs' => $favs,
        'notifications' => $notifications,
        'stats' => $stats,
    ]);
}

function account_profile(): void {
    csrf_verify();
    $user = auth_user();
    if (!$user) redirect('/login');

    $name = post('name');
    $phone = post('phone');
    $nationality = post('nationality');
    $errors = [];
    if (mb_strlen($name) < 2) $errors[] = 'Name must be at least 2 characters.';
    if ($phone !== '' && !preg_match('/^[0-9+\-\s()]{7,20}$/', $phone)) $errors[] = 'Invalid phone number.';
    if (mb_strlen($nationality) > 80) $errors[] = 'Nationality is too long.';
    if ($errors) {
        flash_set('error', implode(' ', $errors));
        redirect('/account?tab=profile');
    }
    try {
        db()->prepare('UPDATE users SET name = ?, phone = ?, nationality = ? WHERE id = ?')
            ->execute([$name, $phone ?: null, $nationality ?: null, (int) $user['id']]);
    } catch (Throwable $e) {
        error_log('[profile] ' . $e->getMessage());
        flash_set('error', 'Could not save your profile. Please try again.');
        redirect('/account?tab=profile');
    }
    flash_set('success', 'Profile updated.');
    redirect('/account?tab=profile');
}

function account_password(): void {
    csrf_verify();
    $user = auth_user();
    if (!$user) redirect('/login');

    $current = (string) ($_POST['current_password'] ?? '');
    $new = (string) ($_POST['password'] ?? '');
    $confirm = (string) ($_POST['password_confirm'] ?? '');

    if (!password_verify($current, $user['password_hash'])) {
        flash_set('error', 'Your current password is incorrect.');
        redirect('/account?tab=profile');
    }
    if ($errs = password_errors($new)) {
        flash_set('error', implode(' ', $errs));
        redirect('/account?tab=profile');
    }
    if ($new !== $confirm) {
        flash_set('error', 'New passwords do not match.');
        redirect('/account?tab=profile');
    }
    try {
        $hash = password_hash($new, PASSWORD_BCRYPT, ['cost' => (int) cfg('BCRYPT_COST', 10)]);
        db()->prepare('UPDATE users SET password_hash = ? WHERE id = ?')->execute([$hash, (int) $user['id']]);
    } catch (Throwable $e) {
        error_log('[password] ' . $e->getMessage());
        flash_set('error', 'Could not update the password.');
        redirect('/account?tab=profile');
    }
    flash_set('success', 'Password changed.');
    redirect('/account?tab=profile');
}

function booking_reference(): string {
    do {
        $ref = 'RWE-' . strtoupper(substr(md5((string) random_bytes(8)), 0, 6));
        $exists = db_one('SELECT id FROM bookings WHERE reference = ?', [$ref]);
    } while ($exists);
    return $ref;
}

function booking_create(): void {
    csrf_verify();
    $user = auth_user();
    if (!$user) redirect('/login');

    $tourId = (int) post('tour_id');
    $hotelId = (int) post('hotel_id');
    $roomId = (int) post('room_id');
    $notes = mb_substr(post('notes'), 0, 2000);
    $guests = max(1, min(20, (int) post('guests', 1)));
    $rooms = max(1, min(5, (int) post('rooms', 1)));

    if ($tourId) {
        $item = db_one("SELECT id, title, price, duration FROM tours WHERE id = ? AND status='published'", [$tourId]);
        if (!$item) { flash_set('error', 'That tour is no longer available.'); redirect('/tours'); }

        $travelDate = post('travel_date');
        if (!$travelDate || !preg_match('/^\d{4}-\d{2}-\d{2}$/', $travelDate) || $travelDate < date('Y-m-d')) {
            flash_set('error', 'Please choose a valid travel date in the future.');
            redirect('/tours/' . db_val('SELECT slug FROM tours WHERE id = ?', [$tourId]));
        }
        $currency = ((float) $item['price'] > 5000) ? 'RWF' : 'USD';
        $total = round((float) $item['price'] * $guests, 2);

        try {
            $bookingId = db_insert(
                "INSERT INTO bookings (reference, user_id, booking_type, tour_id, travel_date, guests,
                     subtotal, total, currency, status, payment_status, notes, source,
                     customer_name, customer_email, customer_phone)
                 VALUES (?, ?, 'tour', ?, ?, ?, ?, ?, ?, 'pending', 'unpaid', ?, 'website', ?, ?, ?)",
                [booking_reference(), (int) $user['id'], $tourId, $travelDate, $guests,
                 $total, $total, $currency, $notes,
                 $user['name'], $user['email'], $user['phone']]
            );
            db_insert(
                "INSERT INTO booking_items (booking_id, item_type, item_id, name, qty, unit_price, line_total)
                 VALUES (?, 'tour', ?, ?, ?, ?, ?)",
                [$bookingId, $tourId, $item['title'], $guests, (float) $item['price'], $total]
            );
            db_insert(
                'INSERT INTO notifications (user_id, type, title, message) VALUES (?, ?, ?, ?)',
                [(int) $user['id'], 'booking', 'Booking request received',
                 'We are checking availability for "' . $item['title'] . '" on ' . date('d M Y', strtotime($travelDate)) . '.']
            );
        } catch (Throwable $e) {
            error_log('[booking] ' . $e->getMessage());
            flash_set('error', 'Could not create the booking. Please try again or contact us.');
            redirect('/tours/' . db_val('SELECT slug FROM tours WHERE id = ?', [$tourId]) ?: '/tours');
        }

        flash_set('success', 'Booking created - reference ' . booking_hint($bookingId) . '. We confirm within 24 hours.');
        redirect('/account?tab=bookings');
    }

    if ($hotelId) {
        $hotel = db_one("SELECT id, name, base_price, currency FROM hotels WHERE id = ? AND status='approved'", [$hotelId]);
        if (!$hotel) { flash_set('error', 'That hotel is no longer available.'); redirect('/hotels'); }

        $checkin = post('checkin');
        $checkout = post('checkout');
        if (!$checkin || !$checkout || !preg_match('/^\d{4}-\d{2}-\d{2}$/', $checkin)
            || !preg_match('/^\d{4}-\d{2}-\d{2}$/', $checkout)
            || $checkin < date('Y-m-d') || $checkout <= $checkin) {
            flash_set('error', 'Please choose valid check-in and check-out dates.');
            redirect('/hotels/' . db_val('SELECT slug FROM hotels WHERE id = ?', [$hotelId]));
        }

        $unit = (float) $hotel['base_price'];
        $roomName = 'Room';
        if ($roomId) {
            $room = db_one('SELECT id, name, price FROM rooms WHERE id = ? AND hotel_id = ? AND status = ?',
                [$roomId, $hotelId, 'active']);
            if ($room) { $unit = (float) $room['price']; $roomName = $room['name']; }
        }
        $nights = max(1, (int) ((strtotime($checkout) - strtotime($checkin)) / 86400));
        $total = round($unit * $nights * $rooms, 2);
        $currency = (string) ($hotel['currency'] ?: 'RWF');

        try {
            $bookingId = db_insert(
                "INSERT INTO bookings (reference, user_id, booking_type, hotel_id, check_in, check_out,
                     guests, rooms, subtotal, total, currency, status, payment_status, notes, source,
                     customer_name, customer_email, customer_phone)
                 VALUES (?, ?, 'hotel', ?, ?, ?, ?, ?, ?, ?, ?, 'pending', 'unpaid', ?, 'website', ?, ?, ?)",
                [booking_reference(), (int) $user['id'], $hotelId, $checkin, $checkout,
                 $guests, $rooms, $total, $total, $currency, $notes,
                 $user['name'], $user['email'], $user['phone']]
            );
            db_insert(
                "INSERT INTO booking_items (booking_id, item_type, item_id, name, qty, unit_price, line_total)
                 VALUES (?, 'hotel', ?, ?, ?, ?, ?)",
                [$bookingId, $hotelId, $hotel['name'] . ' - ' . $roomName, $nights * $rooms, $unit, $total]
            );
            db_insert(
                'INSERT INTO notifications (user_id, type, title, message) VALUES (?, ?, ?, ?)',
                [(int) $user['id'], 'booking', 'Booking request received',
                 'We are confirming "' . $hotel['name'] . '" for ' . date('d M Y', strtotime($checkin)) . '.']
            );
        } catch (Throwable $e) {
            error_log('[booking] ' . $e->getMessage());
            flash_set('error', 'Could not create the booking. Please try again or contact us.');
            redirect('/hotels/' . db_val('SELECT slug FROM hotels WHERE id = ?', [$hotelId]) ?: '/hotels');
        }

        flash_set('success', 'Booking created - reference ' . booking_hint($bookingId) . '. We confirm within 24 hours.');
        redirect('/account?tab=bookings');
    }

    flash_set('error', 'Missing booking details.');
    redirect('/account?tab=bookings');
}

/** Helper used above: fetch the generated reference for a fresh booking. */
function booking_hint(int $bookingId): string {
    return (string) db_val('SELECT reference FROM bookings WHERE id = ?', [$bookingId]);
}

function booking_cancel(string $id): void {
    csrf_verify();
    $user = auth_user();
    if (!$user) redirect('/login');

    $booking = db_one('SELECT id, user_id, status FROM bookings WHERE id = ?', [(int) $id]);
    if (!$booking || (int) $booking['user_id'] !== (int) $user['id']) {
        flash_set('error', 'Booking not found.');
        redirect('/account?tab=bookings');
    }
    if (!in_array($booking['status'], ['pending', 'confirmed'], true)) {
        flash_set('error', 'This booking can no longer be cancelled.');
        redirect('/account?tab=bookings');
    }
    try {
        db()->prepare("UPDATE bookings SET status = 'cancelled' WHERE id = ?")->execute([(int) $id]);
        db()->prepare('INSERT INTO notifications (user_id, type, title, message) VALUES (?, ?, ?, ?)')
            ->execute([(int) $user['id'], 'booking', 'Booking cancelled',
                'Booking #' . (int) $id . ' was cancelled by you.']);
    } catch (Throwable $e) {
        error_log('[cancel] ' . $e->getMessage());
        flash_set('error', 'Could not cancel the booking.');
        redirect('/account?tab=bookings');
    }
    flash_set('success', 'Booking cancelled.');
    redirect('/account?tab=bookings');
}
