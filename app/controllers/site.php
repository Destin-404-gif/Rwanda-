<?php
/**
 * Public site pages.
 */

function page_home(): void {
    $slides = db_all("SELECT * FROM hero_slides WHERE status = 'active' ORDER BY sort_order ASC, id ASC");
    if (!$slides) $slides = [];
    $destinations = db_all("SELECT * FROM destinations WHERE status = 'published' ORDER BY is_featured DESC, id ASC");
    $featured = array_slice(array_values(array_filter($destinations, fn($d) => (int) $d['is_featured'] === 1)), 0, 6);
    if (!$featured) $featured = array_slice($destinations, 0, 6);
    $tours = db_all("SELECT * FROM tours WHERE status = 'published' ORDER BY is_featured DESC, rating DESC LIMIT 6");
    $hotels = db_all("SELECT * FROM hotels WHERE status = 'approved' ORDER BY is_featured DESC, rating DESC LIMIT 6");
    $gallery = db_all("SELECT * FROM gallery WHERE status = 'active' ORDER BY sort_order ASC, id ASC LIMIT 8");
    $testimonials = db_all("SELECT * FROM testimonials WHERE status = 'active' ORDER BY id ASC LIMIT 4");
    $tourCount = (int) db_val("SELECT COUNT(*) FROM tours WHERE status='published'");
    $destCount = (int) db_val("SELECT COUNT(*) FROM destinations WHERE status='published'");
    $hotelCount = (int) db_val("SELECT COUNT(*) FROM hotels WHERE status='approved'");

    render('pages/home', [
        'title' => 'Rwanda Explorer - Gorilla Trekking, Safaris & Tours in Rwanda',
        'description' => 'Discover Rwanda: gorilla trekking in Volcanoes National Park, Big Five safari in Akagera, the Nyungwe canopy walk, Lake Kivu beaches and Kigali city tours.',
        'active' => 'home',
        'slides' => $slides,
        'featured' => $featured,
        'tours' => $tours,
        'hotels' => $hotels,
        'gallery' => $gallery,
        'testimonials' => $testimonials,
        'stats' => ['destinations' => $destCount, 'tours' => $tourCount, 'hotels' => $hotelCount],
        'body_class' => 'page-home',
    ]);
}

function page_destinations(): void {
    $region = input('region');
    $sql = "SELECT * FROM destinations WHERE status = 'published'";
    $params = [];
    if ($region) { $sql .= " AND region = ?"; $params[] = $region; }
    $sql .= " ORDER BY is_featured DESC, name ASC";
    $destinations = db_all($sql, $params);
    $regions = db_all("SELECT DISTINCT region FROM destinations WHERE status='published' ORDER BY region");

    render('pages/destinations', [
        'title' => 'Destinations in Rwanda | Rwanda Explorer',
        'description' => 'Explore Rwanda\'s national parks, lakes and cities: Volcanoes, Nyungwe, Akagera, Lake Kivu and Kigali.',
        'active' => 'destinations',
        'destinations' => $destinations,
        'regions' => array_column($regions, 'region'),
        'filter' => $region,
    ]);
}

function page_destination(string $slug): void {
    $dest = db_one("SELECT * FROM destinations WHERE slug = ? AND status = 'published'", [$slug]);
    if (!$dest) page_404();

    $tours = db_all("SELECT * FROM tours WHERE destination_id = ? AND status='published' ORDER BY is_featured DESC LIMIT 6", [$dest['id']]);
    $hotels = db_all("SELECT * FROM hotels WHERE destination_id = ? AND status='approved' ORDER BY is_featured DESC, rating DESC LIMIT 6", [$dest['id']]);
    $reviews = db_all("SELECT * FROM reviews WHERE target_type='destination' AND target_id=? AND status='approved' ORDER BY created_at DESC LIMIT 6", [$dest['id']]);
    $others = db_all("SELECT * FROM destinations WHERE status='published' AND slug <> ? ORDER BY is_featured DESC LIMIT 3", [$slug]);

    render('pages/destination_detail', [
        'title' => ($dest['meta_title'] ?: $dest['name']) . ' | Rwanda Explorer',
        'description' => $dest['meta_description'] ?: (excerpt((string) $dest['description'], 150)),
        'active' => 'destinations',
        'dest' => $dest,
        'tours' => $tours,
        'hotels' => $hotels,
        'reviews' => $reviews,
        'others' => $others,
    ]);
}

function page_experiences(): void {
    $tours = db_all("SELECT * FROM tours WHERE status='published' ORDER BY category, rating DESC");
    $groups = [];
    foreach ($tours as $t) $groups[$t['category']][] = $t;
    $destinations = db_all("SELECT * FROM destinations WHERE status='published' ORDER BY is_featured DESC");

    render('pages/experiences', [
        'title' => 'Experiences in Rwanda | Rwanda Explorer',
        'description' => 'Wildlife, culture, adventure and water experiences across Rwanda.',
        'active' => 'experiences',
        'groups' => $groups,
        'destinations' => $destinations,
    ]);
}

function page_tours(): void {
    $category = input('category');
    $max = input('max');
    $sql = "SELECT t.*, d.name AS destination_name FROM tours t LEFT JOIN destinations d ON d.id = t.destination_id WHERE t.status = 'published'";
    $params = [];
    if ($category) { $sql .= " AND t.category = ?"; $params[] = $category; }
    if ($max !== '' && is_numeric($max)) { $sql .= " AND t.price <= ?"; $params[] = (float) $max; }
    $sql .= " ORDER BY t.is_featured DESC, t.rating DESC";
    $tours = db_all($sql, $params);
    $categories = db_all("SELECT DISTINCT category FROM tours WHERE status='published' ORDER BY category");

    render('pages/tours', [
        'title' => 'Tours & Safaris in Rwanda | Rwanda Explorer',
        'description' => 'Book gorilla trekking, safaris, city tours and cultural experiences in Rwanda.',
        'active' => 'tours',
        'tours' => $tours,
        'categories' => array_column($categories, 'category'),
        'filter' => $category,
        'max' => $max,
    ]);
}

function page_tour(string $slug): void {
    $tour = db_one("SELECT t.*, d.name AS destination_name, d.slug AS destination_slug FROM tours t
        LEFT JOIN destinations d ON d.id = t.destination_id WHERE t.slug = ? AND t.status='published'", [$slug]);
    if (!$tour) page_404();

    $dates = db_all("SELECT * FROM tour_dates WHERE tour_id = ? AND start_date >= CURDATE() AND status='open' ORDER BY start_date ASC LIMIT 6", [$tour['id']]);
    $reviews = db_all("SELECT * FROM reviews WHERE target_type='tour' AND target_id=? AND status='approved' ORDER BY created_at DESC LIMIT 6", [$tour['id']]);
    $related = db_all("SELECT * FROM tours WHERE status='published' AND slug <> ? ORDER BY is_featured DESC, rating DESC LIMIT 3", [$slug]);

    render('pages/tour_detail', [
        'title' => $tour['title'] . ' | Rwanda Explorer',
        'description' => excerpt((string) $tour['description'], 155),
        'active' => 'tours',
        'tour' => $tour,
        'dates' => $dates,
        'reviews' => $reviews,
        'related' => $related,
    ]);
}

function page_hotels(): void {
    $city = input('city');
    $max = input('max');
    $sql = "SELECT h.*, d.name AS destination_name FROM hotels h LEFT JOIN destinations d ON d.id = h.destination_id WHERE h.status = 'approved'";
    $params = [];
    if ($city) { $sql .= " AND h.city = ?"; $params[] = $city; }
    if ($max !== '' && is_numeric($max)) { $sql .= " AND h.base_price <= ?"; $params[] = (float) $max; }
    $sql .= " ORDER BY h.is_featured DESC, h.rating DESC";
    $hotels = db_all($sql, $params);
    $cities = db_all("SELECT DISTINCT city FROM hotels WHERE status='approved' ORDER BY city");

    render('pages/hotels', [
        'title' => 'Hotels & Lodges in Rwanda | Rwanda Explorer',
        'description' => 'Beachfront hotels, volcano lodges and city stays across Rwanda.',
        'active' => 'hotels',
        'hotels' => $hotels,
        'cities' => array_column($cities, 'city'),
        'filter' => $city,
        'max' => $max,
    ]);
}

function page_hotel(string $slug): void {
    $hotel = db_one("SELECT h.*, d.name AS destination_name, d.slug AS destination_slug FROM hotels h
        LEFT JOIN destinations d ON d.id = h.destination_id WHERE h.slug = ? AND h.status='approved'", [$slug]);
    if (!$hotel) page_404();

    $rooms = db_all("SELECT * FROM rooms WHERE hotel_id = ? AND status='active' ORDER BY price ASC", [$hotel['id']]);
    $reviews = db_all("SELECT * FROM reviews WHERE target_type='hotel' AND target_id=? AND status='approved' ORDER BY created_at DESC LIMIT 6", [$hotel['id']]);
    $related = db_all("SELECT * FROM hotels WHERE status='approved' AND slug <> ? ORDER BY is_featured DESC, rating DESC LIMIT 3", [$slug]);

    render('pages/hotel_detail', [
        'title' => $hotel['name'] . ' | Rwanda Explorer',
        'description' => excerpt((string) $hotel['description'], 155),
        'active' => 'hotels',
        'hotel' => $hotel,
        'rooms' => $rooms,
        'reviews' => $reviews,
        'related' => $related,
    ]);
}

function page_gallery(): void {
    $category = input('category');
    $sql = "SELECT * FROM gallery WHERE status='active'";
    $params = [];
    if ($category) { $sql .= " AND category = ?"; $params[] = $category; }
    $sql .= " ORDER BY sort_order ASC, id ASC";
    $items = db_all($sql, $params);
    $cats = db_all("SELECT DISTINCT category FROM gallery WHERE status='active' ORDER BY category");

    render('pages/gallery', [
        'title' => 'Gallery | Rwanda Explorer',
        'description' => 'Photographs from Rwanda\'s parks, lakes, cities and cultures.',
        'active' => 'gallery',
        'items' => $items,
        'categories' => array_column($cats, 'category'),
        'filter' => $category,
    ]);
}

function page_about(): void {
    render('pages/about', [
        'title' => 'About Rwanda Explorer | Our Story & Team',
        'description' => 'Rwanda Explorer is a Kigali-based travel company creating gorilla trekking, safari and cultural journeys across Rwanda.',
        'active' => 'about',
        'stats' => ['destinations' => (int) db_val("SELECT COUNT(*) FROM destinations WHERE status='published'"),
                    'tours' => (int) db_val("SELECT COUNT(*) FROM tours WHERE status='published'"),
                    'hotels' => (int) db_val("SELECT COUNT(*) FROM hotels WHERE status='approved'"),
                    'bookings' => (int) db_val("SELECT COUNT(*) FROM bookings")],
    ]);
}

function page_contact(): void {
    render('pages/contact', [
        'title' => 'Contact Us | Rwanda Explorer',
        'description' => 'Talk to our Kigali-based team about tours, permits, hotels and custom itineraries.',
        'active' => 'contact',
    ]);
}

function submit_contact(): void {
    csrf_verify();
    $name = post('name');
    $email = post('email');
    $subject = post('subject');
    $message = post('message');
    $errors = [];
    if (mb_strlen($name) < 2) $errors[] = 'Please enter your name.';
    if (!filter_var($email, FILTER_VALIDATE_EMAIL)) $errors[] = 'Please enter a valid email address.';
    if (mb_strlen($message) < 10) $errors[] = 'Please write a message of at least 10 characters.';
    if ($errors) {
        flash_set('error', implode(' ', $errors));
        redirect('/contact');
    }
    try {
        db_insert('INSERT INTO notifications (user_id, type, title, message, channel) VALUES (NULL, ?, ?, ?, ?)',
            ['contact', 'Contact form: ' . ($subject ?: 'Enquiry'),
             "From $name <$email>: " . mb_substr($message, 0, 1500), 'app']);
    } catch (Throwable $e) {
        error_log('[contact] ' . $e->getMessage());
    }
    flash_set('success', 'Thank you - your message has been sent. We usually reply within one working day.');
    redirect('/contact');
}

function page_search(): void {
    $q = input('q');
    $type = input('type');
    $results = [];
    if (mb_strlen($q) >= 2) {
        $like = '%' . $q . '%';
        if ($type !== 'tours' && $type !== 'hotels') {
            $results['destinations'] = db_all(
                "SELECT slug, name, region AS meta, hero_image AS image FROM destinations
                 WHERE status='published' AND (name LIKE ? OR region LIKE ? OR description LIKE ?) LIMIT 8",
                [$like, $like, $like]);
        }
        if ($type !== 'destinations' && $type !== 'hotels') {
            $results['tours'] = db_all(
                "SELECT slug, title AS name, CONCAT(category, ' - ', duration) AS meta, cover_image AS image FROM tours
                 WHERE status='published' AND (title LIKE ? OR description LIKE ? OR category LIKE ?) LIMIT 8",
                [$like, $like, $like]);
        }
        if ($type !== 'destinations' && $type !== 'tours') {
            $results['hotels'] = db_all(
                "SELECT slug, name, CONCAT(city, ' - ', stars, ' star') AS meta, cover_image AS image FROM hotels
                 WHERE status='approved' AND (name LIKE ? OR city LIKE ? OR description LIKE ?) LIMIT 8",
                [$like, $like, $like]);
        }
        $results['blog'] = db_all(
            "SELECT slug, title AS name, category AS meta, cover_image AS image FROM blog_posts
             WHERE status='published' AND (title LIKE ? OR excerpt LIKE ?) LIMIT 5",
            [$like, $like]);
    }
    $total = array_sum(array_map('count', $results));

    render('pages/search', [
        'title' => ($q ? 'Search: ' . $q : 'Search') . ' | Rwanda Explorer',
        'description' => 'Search destinations, tours, hotels and travel articles.',
        'active' => 'search',
        'q' => $q,
        'type' => $type,
        'results' => $results,
        'total' => $total,
    ]);
}

function page_guide(): void {
    render('pages/guide', [
        'title' => 'Rwanda Travel Guide | Visas, Best Time, Getting Around',
        'description' => 'Practical Rwanda travel advice: visas, seasons, health, money, packing and getting around.',
        'active' => 'guide',
    ]);
}

function page_blog(): void {
    $posts = db_all("SELECT * FROM blog_posts WHERE status='published' ORDER BY published_at DESC");
    render('pages/blog', [
        'title' => 'Travel Blog | Rwanda Explorer',
        'description' => 'Guides, tips and stories from Rwanda.',
        'active' => 'blog',
        'posts' => $posts,
    ]);
}

function page_blog_post(string $slug): void {
    $post = db_one("SELECT b.*, u.name AS author FROM blog_posts b LEFT JOIN users u ON u.id = b.author_id
        WHERE b.slug = ? AND b.status='published'", [$slug]);
    if (!$post) page_404();
    $others = db_all("SELECT * FROM blog_posts WHERE status='published' AND slug <> ? ORDER BY published_at DESC LIMIT 3", [$slug]);
    render('pages/blog_post', [
        'title' => ($post['meta_title'] ?: $post['title']) . ' | Rwanda Explorer',
        'description' => $post['meta_description'] ?: $post['excerpt'],
        'active' => 'blog',
        'post' => $post,
        'others' => $others,
    ]);
}

function page_404(): void {
    http_response_code(404);
    render('pages/error', [
        'code' => 404,
        'title' => 'Page not found',
        'message' => 'The page you are looking for does not exist or has been moved.',
        'active' => '',
    ], 'site');
    exit;
}
