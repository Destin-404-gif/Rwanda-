<?php
/**
 * Admin dashboard: overview, analytics, notifications, settings, profile
 * and config-driven CRUD for every managed resource.
 */

// ============================================================ resource map
function admin_resources(): array {
    return [
        'destinations' => [
            'table' => 'destinations', 'label' => 'Destinations', 'singular' => 'Destination',
            'icon' => 'map-pin', 'order' => 'is_featured DESC, name ASC',
            'title_col' => 'name', 'sub_col' => 'region', 'image_col' => 'hero_image',
            'fields' => [
                ['name' => 'name', 'type' => 'text', 'label' => 'Name', 'required' => true],
                ['name' => 'slug', 'type' => 'slug', 'label' => 'Slug', 'from' => 'name'],
                ['name' => 'region', 'type' => 'text', 'label' => 'Region', 'required' => true],
                ['name' => 'hero_image', 'type' => 'image', 'label' => 'Hero image'],
                ['name' => 'short_description', 'type' => 'text', 'label' => 'Short description'],
                ['name' => 'description', 'type' => 'textarea', 'label' => 'Description', 'rows' => 7],
                ['name' => 'activities', 'type' => 'lines', 'label' => 'Highlights (one per line)'],
                ['name' => 'best_season', 'type' => 'text', 'label' => 'Best season'],
                ['name' => 'entry_fee', 'type' => 'decimal', 'label' => 'Entry fee (USD)'],
                ['name' => 'how_to_get_there', 'type' => 'text', 'label' => 'Getting there'],
                ['name' => 'lat', 'type' => 'decimal', 'label' => 'Latitude'],
                ['name' => 'lng', 'type' => 'decimal', 'label' => 'Longitude'],
                ['name' => 'meta_title', 'type' => 'text', 'label' => 'SEO title'],
                ['name' => 'meta_description', 'type' => 'text', 'label' => 'SEO description'],
                ['name' => 'is_featured', 'type' => 'checkbox', 'label' => 'Featured on homepage'],
                ['name' => 'status', 'type' => 'select', 'label' => 'Status',
                 'options' => ['published' => 'Published', 'draft' => 'Draft']],
            ],
        ],
        'tours' => [
            'table' => 'tours', 'label' => 'Tours', 'singular' => 'Tour',
            'icon' => 'compass', 'order' => 'is_featured DESC, title ASC',
            'title_col' => 'title', 'sub_col' => 'category', 'image_col' => 'cover_image',
            'fields' => [
                ['name' => 'title', 'type' => 'text', 'label' => 'Title', 'required' => true],
                ['name' => 'slug', 'type' => 'slug', 'label' => 'Slug', 'from' => 'title'],
                ['name' => 'category', 'type' => 'select', 'label' => 'Category', 'required' => true,
                 'options' => ['Wildlife' => 'Wildlife', 'Safari' => 'Safari', 'Adventure' => 'Adventure', 'Water' => 'Water', 'Culture' => 'Culture']],
                ['name' => 'duration', 'type' => 'text', 'label' => 'Duration (e.g. 3 days)', 'required' => true],
                ['name' => 'duration_days', 'type' => 'number', 'label' => 'Duration in days'],
                ['name' => 'destination_id', 'type' => 'relation', 'label' => 'Destination',
                 'table' => 'destinations', 'label_col' => 'name', 'nullable' => true],
                ['name' => 'cover_image', 'type' => 'image', 'label' => 'Cover image'],
                ['name' => 'price', 'type' => 'decimal', 'label' => 'Price per person', 'required' => true],
                ['name' => 'discount_pct', 'type' => 'decimal', 'label' => 'Discount %'],
                ['name' => 'difficulty', 'type' => 'select', 'label' => 'Difficulty',
                 'options' => ['easy' => 'Easy', 'moderate' => 'Moderate', 'hard' => 'Hard']],
                ['name' => 'group_size', 'type' => 'text', 'label' => 'Group size (e.g. 2-8)'],
                ['name' => 'max_capacity', 'type' => 'number', 'label' => 'Max capacity'],
                ['name' => 'description', 'type' => 'textarea', 'label' => 'Description', 'rows' => 8],
                ['name' => 'itinerary', 'type' => 'keyed_lines', 'label' => 'Itinerary (Day title | description per line)'],
                ['name' => 'included', 'type' => 'lines', 'label' => 'Included (one per line)'],
                ['name' => 'excluded', 'type' => 'lines', 'label' => 'Excluded (one per line)'],
                ['name' => 'meeting_point', 'type' => 'text', 'label' => 'Meeting point'],
                ['name' => 'rating', 'type' => 'decimal', 'label' => 'Rating (0-5)'],
                ['name' => 'is_featured', 'type' => 'checkbox', 'label' => 'Featured on homepage'],
                ['name' => 'status', 'type' => 'select', 'label' => 'Status',
                 'options' => ['published' => 'Published', 'draft' => 'Draft', 'archived' => 'Archived']],
            ],
        ],
        'hotels' => [
            'table' => 'hotels', 'label' => 'Hotels', 'singular' => 'Hotel',
            'icon' => 'building', 'order' => 'is_featured DESC, name ASC',
            'title_col' => 'name', 'sub_col' => 'city', 'image_col' => 'cover_image',
            'fields' => [
                ['name' => 'name', 'type' => 'text', 'label' => 'Name', 'required' => true],
                ['name' => 'slug', 'type' => 'slug', 'label' => 'Slug', 'from' => 'name'],
                ['name' => 'type', 'type' => 'select', 'label' => 'Type',
                 'options' => ['hotel' => 'Hotel', 'lodge' => 'Lodge', 'guesthouse' => 'Guesthouse', 'resort' => 'Resort', 'homestay' => 'Homestay']],
                ['name' => 'destination_id', 'type' => 'relation', 'label' => 'Destination',
                 'table' => 'destinations', 'label_col' => 'name', 'nullable' => true],
                ['name' => 'city', 'type' => 'text', 'label' => 'City', 'required' => true],
                ['name' => 'address', 'type' => 'text', 'label' => 'Address'],
                ['name' => 'cover_image', 'type' => 'image', 'label' => 'Cover image'],
                ['name' => 'stars', 'type' => 'number', 'label' => 'Stars (1-5)'],
                ['name' => 'base_price', 'type' => 'decimal', 'label' => 'Base price / night', 'required' => true],
                ['name' => 'currency', 'type' => 'select', 'label' => 'Currency', 'options' => ['RWF' => 'RWF', 'USD' => 'USD']],
                ['name' => 'description', 'type' => 'textarea', 'label' => 'Description', 'rows' => 7],
                ['name' => 'amenities', 'type' => 'lines', 'label' => 'Amenities (one per line)'],
                ['name' => 'phone', 'type' => 'text', 'label' => 'Phone'],
                ['name' => 'email', 'type' => 'text', 'label' => 'Email'],
                ['name' => 'check_in_time', 'type' => 'text', 'label' => 'Check-in time'],
                ['name' => 'check_out_time', 'type' => 'text', 'label' => 'Check-out time'],
                ['name' => 'policies', 'type' => 'textarea', 'label' => 'Policies', 'rows' => 4],
                ['name' => 'rating', 'type' => 'decimal', 'label' => 'Rating (0-5)'],
                ['name' => 'is_featured', 'type' => 'checkbox', 'label' => 'Featured on homepage'],
                ['name' => 'status', 'type' => 'select', 'label' => 'Status',
                 'options' => ['approved' => 'Approved', 'pending' => 'Pending', 'rejected' => 'Rejected', 'suspended' => 'Suspended']],
            ],
        ],
        'rooms' => [
            'table' => 'rooms', 'label' => 'Rooms', 'singular' => 'Room',
            'icon' => 'home', 'order' => 'hotel_id ASC, price ASC',
            'title_col' => 'name', 'sub_col' => 'room_type', 'image_col' => null,
            'fields' => [
                ['name' => 'hotel_id', 'type' => 'relation', 'label' => 'Hotel', 'table' => 'hotels', 'label_col' => 'name', 'required' => true],
                ['name' => 'name', 'type' => 'text', 'label' => 'Room name', 'required' => true],
                ['name' => 'room_type', 'type' => 'text', 'label' => 'Room type'],
                ['name' => 'capacity', 'type' => 'number', 'label' => 'Capacity'],
                ['name' => 'beds', 'type' => 'text', 'label' => 'Beds'],
                ['name' => 'size_sqm', 'type' => 'number', 'label' => 'Size (sqm)'],
                ['name' => 'price', 'type' => 'decimal', 'label' => 'Price / night', 'required' => true],
                ['name' => 'total_units', 'type' => 'number', 'label' => 'Units available'],
                ['name' => 'description', 'type' => 'textarea', 'label' => 'Description', 'rows' => 4],
                ['name' => 'features', 'type' => 'lines', 'label' => 'Features (one per line)'],
                ['name' => 'status', 'type' => 'select', 'label' => 'Status', 'options' => ['active' => 'Active', 'inactive' => 'Inactive']],
            ],
        ],
        'bookings' => [
            'table' => 'bookings', 'label' => 'Bookings', 'singular' => 'Booking',
            'icon' => 'ticket', 'order' => 'created_at DESC',
            'title_col' => 'reference', 'sub_col' => 'customer_name', 'image_col' => null,
            'readonly' => ['reference', 'customer_name', 'customer_email', 'total', 'currency'],
            'fields' => [
                ['name' => 'booking_type', 'type' => 'select', 'label' => 'Type',
                 'options' => ['hotel' => 'Hotel', 'tour' => 'Tour', 'permit' => 'Permit', 'transfer' => 'Transfer', 'car' => 'Car', 'guide' => 'Guide']],
                ['name' => 'tour_id', 'type' => 'relation', 'label' => 'Tour', 'table' => 'tours', 'label_col' => 'title', 'nullable' => true],
                ['name' => 'hotel_id', 'type' => 'relation', 'label' => 'Hotel', 'table' => 'hotels', 'label_col' => 'name', 'nullable' => true],
                ['name' => 'customer_name', 'type' => 'text', 'label' => 'Customer name'],
                ['name' => 'customer_email', 'type' => 'text', 'label' => 'Customer email'],
                ['name' => 'customer_phone', 'type' => 'text', 'label' => 'Customer phone'],
                ['name' => 'travel_date', 'type' => 'date', 'label' => 'Travel date'],
                ['name' => 'check_in', 'type' => 'date', 'label' => 'Check-in'],
                ['name' => 'check_out', 'type' => 'date', 'label' => 'Check-out'],
                ['name' => 'guests', 'type' => 'number', 'label' => 'Guests'],
                ['name' => 'rooms', 'type' => 'number', 'label' => 'Rooms'],
                ['name' => 'total', 'type' => 'decimal', 'label' => 'Total'],
                ['name' => 'currency', 'type' => 'text', 'label' => 'Currency'],
                ['name' => 'notes', 'type' => 'textarea', 'label' => 'Notes', 'rows' => 4],
                ['name' => 'status', 'type' => 'select', 'label' => 'Status',
                 'options' => ['pending' => 'Pending', 'confirmed' => 'Confirmed', 'completed' => 'Completed', 'cancelled' => 'Cancelled', 'refunded' => 'Refunded']],
                ['name' => 'payment_status', 'type' => 'select', 'label' => 'Payment',
                 'options' => ['unpaid' => 'Unpaid', 'deposit_paid' => 'Deposit paid', 'paid' => 'Paid', 'refunded' => 'Refunded']],
            ],
        ],
        'customers' => [
            'table' => 'users', 'label' => 'Customers', 'singular' => 'Customer',
            'icon' => 'users', 'order' => 'created_at DESC', 'where' => "role = 'customer'",
            'title_col' => 'name', 'sub_col' => 'email', 'image_col' => 'avatar',
            'no_create' => true,
            'fields' => [
                ['name' => 'name', 'type' => 'text', 'label' => 'Name', 'required' => true],
                ['name' => 'email', 'type' => 'text', 'label' => 'Email', 'required' => true],
                ['name' => 'phone', 'type' => 'text', 'label' => 'Phone'],
                ['name' => 'nationality', 'type' => 'text', 'label' => 'Nationality'],
                ['name' => 'loyalty_points', 'type' => 'number', 'label' => 'Loyalty points'],
                ['name' => 'status', 'type' => 'select', 'label' => 'Status',
                 'options' => ['active' => 'Active', 'pending' => 'Pending', 'suspended' => 'Suspended']],
            ],
        ],
        'payments' => [
            'table' => 'payments', 'label' => 'Payments', 'singular' => 'Payment',
            'icon' => 'credit-card', 'order' => 'created_at DESC',
            'title_col' => 'transaction_ref', 'sub_col' => 'provider', 'image_col' => null,
            'fields' => [
                ['name' => 'booking_id', 'type' => 'relation', 'label' => 'Booking', 'table' => 'bookings', 'label_col' => 'reference', 'required' => true],
                ['name' => 'provider', 'type' => 'select', 'label' => 'Provider',
                 'options' => ['mtn' => 'MTN MoMo', 'airtel' => 'Airtel Money', 'stripe' => 'Stripe', 'flutterwave' => 'Flutterwave', 'pesapal' => 'Pesapal', 'cash' => 'Cash', 'bank' => 'Bank transfer']],
                ['name' => 'method', 'type' => 'text', 'label' => 'Method'],
                ['name' => 'amount', 'type' => 'decimal', 'label' => 'Amount', 'required' => true],
                ['name' => 'currency', 'type' => 'text', 'label' => 'Currency'],
                ['name' => 'transaction_ref', 'type' => 'text', 'label' => 'Transaction ref'],
                ['name' => 'status', 'type' => 'select', 'label' => 'Status',
                 'options' => ['pending' => 'Pending', 'successful' => 'Successful', 'failed' => 'Failed', 'refunded' => 'Refunded']],
                ['name' => 'paid_at', 'type' => 'datetime', 'label' => 'Paid at'],
            ],
        ],
        'reviews' => [
            'table' => 'reviews', 'label' => 'Reviews', 'singular' => 'Review',
            'icon' => 'message-square', 'order' => 'created_at DESC',
            'title_col' => 'author_name', 'sub_col' => 'comment', 'image_col' => null,
            'fields' => [
                ['name' => 'target_type', 'type' => 'select', 'label' => 'Target type',
                 'options' => ['hotel' => 'Hotel', 'tour' => 'Tour', 'destination' => 'Destination']],
                ['name' => 'target_id', 'type' => 'number', 'label' => 'Target ID', 'required' => true],
                ['name' => 'author_name', 'type' => 'text', 'label' => 'Author'],
                ['name' => 'rating', 'type' => 'number', 'label' => 'Rating (1-5)'],
                ['name' => 'title', 'type' => 'text', 'label' => 'Title'],
                ['name' => 'comment', 'type' => 'textarea', 'label' => 'Comment', 'rows' => 5],
                ['name' => 'reply', 'type' => 'textarea', 'label' => 'Your reply', 'rows' => 3],
                ['name' => 'status', 'type' => 'select', 'label' => 'Status',
                 'options' => ['approved' => 'Approved', 'pending' => 'Pending', 'rejected' => 'Rejected']],
            ],
        ],
        'gallery' => [
            'table' => 'gallery', 'label' => 'Gallery', 'singular' => 'Photo',
            'icon' => 'image', 'order' => 'sort_order ASC, id DESC',
            'title_col' => 'title', 'sub_col' => 'category', 'image_col' => 'url',
            'fields' => [
                ['name' => 'title', 'type' => 'text', 'label' => 'Title'],
                ['name' => 'category', 'type' => 'text', 'label' => 'Category'],
                ['name' => 'url', 'type' => 'image', 'label' => 'Image', 'required' => true],
                ['name' => 'media_type', 'type' => 'select', 'label' => 'Media type', 'options' => ['image' => 'Image', 'video' => 'Video']],
                ['name' => 'sort_order', 'type' => 'number', 'label' => 'Sort order'],
                ['name' => 'status', 'type' => 'select', 'label' => 'Status', 'options' => ['active' => 'Active', 'inactive' => 'Inactive']],
            ],
        ],
        'hero-slides' => [
            'table' => 'hero_slides', 'label' => 'Hero slides', 'singular' => 'Slide',
            'icon' => 'image', 'order' => 'sort_order ASC, id ASC',
            'title_col' => 'card_title', 'sub_col' => 'subtitle', 'image_col' => 'image',
            'fields' => [
                ['name' => 'title_line1', 'type' => 'text', 'label' => 'Title line 1', 'required' => true],
                ['name' => 'title_line2', 'type' => 'text', 'label' => 'Title line 2', 'required' => true],
                ['name' => 'subtitle', 'type' => 'text', 'label' => 'Subtitle'],
                ['name' => 'image', 'type' => 'image', 'label' => 'Background image', 'required' => true],
                ['name' => 'card_title', 'type' => 'text', 'label' => 'Strip card title'],
                ['name' => 'card_desc', 'type' => 'text', 'label' => 'Strip card description'],
                ['name' => 'card_thumb', 'type' => 'image', 'label' => 'Strip card thumbnail'],
                ['name' => 'sort_order', 'type' => 'number', 'label' => 'Sort order'],
                ['name' => 'status', 'type' => 'select', 'label' => 'Status', 'options' => ['active' => 'Active', 'inactive' => 'Inactive']],
            ],
        ],
        'blog' => [
            'table' => 'blog_posts', 'label' => 'Blog posts', 'singular' => 'Post',
            'icon' => 'book-open', 'order' => 'published_at DESC',
            'title_col' => 'title', 'sub_col' => 'category', 'image_col' => 'cover_image',
            'fields' => [
                ['name' => 'title', 'type' => 'text', 'label' => 'Title', 'required' => true],
                ['name' => 'slug', 'type' => 'slug', 'label' => 'Slug', 'from' => 'title'],
                ['name' => 'category', 'type' => 'text', 'label' => 'Category'],
                ['name' => 'cover_image', 'type' => 'image', 'label' => 'Cover image'],
                ['name' => 'excerpt', 'type' => 'text', 'label' => 'Excerpt'],
                ['name' => 'content', 'type' => 'textarea', 'label' => 'Content (plain text)', 'rows' => 14],
                ['name' => 'read_minutes', 'type' => 'number', 'label' => 'Read minutes'],
                ['name' => 'tags', 'type' => 'lines', 'label' => 'Tags (one per line)'],
                ['name' => 'author_id', 'type' => 'relation', 'label' => 'Author', 'table' => 'users', 'label_col' => 'name', 'nullable' => true],
                ['name' => 'meta_title', 'type' => 'text', 'label' => 'SEO title'],
                ['name' => 'meta_description', 'type' => 'text', 'label' => 'SEO description'],
                ['name' => 'status', 'type' => 'select', 'label' => 'Status', 'options' => ['published' => 'Published', 'draft' => 'Draft']],
                ['name' => 'published_at', 'type' => 'datetime', 'label' => 'Published at'],
            ],
        ],
        'notifications' => [
            'table' => 'notifications', 'label' => 'Notifications', 'singular' => 'Notification',
            'icon' => 'bell', 'order' => 'created_at DESC',
            'title_col' => 'title', 'sub_col' => 'message', 'image_col' => null,
            'fields' => [
                ['name' => 'type', 'type' => 'text', 'label' => 'Type (booking, contact, system...)'],
                ['name' => 'title', 'type' => 'text', 'label' => 'Title', 'required' => true],
                ['name' => 'message', 'type' => 'textarea', 'label' => 'Message', 'rows' => 5],
                ['name' => 'channel', 'type' => 'select', 'label' => 'Channel', 'options' => ['app' => 'App', 'email' => 'Email', 'sms' => 'SMS']],
                ['name' => 'is_read', 'type' => 'checkbox', 'label' => 'Read'],
            ],
        ],
        'testimonials' => [
            'table' => 'testimonials', 'label' => 'Testimonials', 'singular' => 'Testimonial',
            'icon' => 'message-square', 'order' => 'id ASC',
            'title_col' => 'name', 'sub_col' => 'country', 'image_col' => 'avatar',
            'fields' => [
                ['name' => 'name', 'type' => 'text', 'label' => 'Name', 'required' => true],
                ['name' => 'country', 'type' => 'text', 'label' => 'Country'],
                ['name' => 'rating', 'type' => 'number', 'label' => 'Rating (1-5)'],
                ['name' => 'text', 'type' => 'textarea', 'label' => 'Quote', 'rows' => 5],
                ['name' => 'avatar', 'type' => 'image', 'label' => 'Avatar'],
                ['name' => 'status', 'type' => 'select', 'label' => 'Status', 'options' => ['active' => 'Active', 'inactive' => 'Inactive']],
            ],
        ],
        'guides' => [
            'table' => 'guides', 'label' => 'Guides', 'singular' => 'Guide',
            'icon' => 'users', 'order' => 'rating DESC, name ASC',
            'title_col' => 'name', 'sub_col' => 'type', 'image_col' => 'photo',
            'fields' => [
                ['name' => 'name', 'type' => 'text', 'label' => 'Name', 'required' => true],
                ['name' => 'type', 'type' => 'select', 'label' => 'Type', 'options' => ['guide' => 'Guide', 'driver' => 'Driver']],
                ['name' => 'photo', 'type' => 'image', 'label' => 'Photo'],
                ['name' => 'languages', 'type' => 'lines', 'label' => 'Languages (one per line)'],
                ['name' => 'specialties', 'type' => 'lines', 'label' => 'Specialties (one per line)'],
                ['name' => 'experience_years', 'type' => 'number', 'label' => 'Years of experience'],
                ['name' => 'daily_rate', 'type' => 'decimal', 'label' => 'Daily rate'],
                ['name' => 'bio', 'type' => 'textarea', 'label' => 'Bio', 'rows' => 5],
                ['name' => 'rating', 'type' => 'decimal', 'label' => 'Rating (0-5)'],
                ['name' => 'status', 'type' => 'select', 'label' => 'Status', 'options' => ['active' => 'Active', 'inactive' => 'Inactive']],
            ],
        ],
        'coupons' => [
            'table' => 'coupons', 'label' => 'Coupons', 'singular' => 'Coupon',
            'icon' => 'ticket', 'order' => 'created_at DESC',
            'title_col' => 'code', 'sub_col' => 'type', 'image_col' => null,
            'fields' => [
                ['name' => 'code', 'type' => 'text', 'label' => 'Code', 'required' => true],
                ['name' => 'type', 'type' => 'select', 'label' => 'Type', 'options' => ['percent' => 'Percent', 'fixed' => 'Fixed amount']],
                ['name' => 'value', 'type' => 'decimal', 'label' => 'Value', 'required' => true],
                ['name' => 'min_spend', 'type' => 'decimal', 'label' => 'Minimum spend'],
                ['name' => 'usage_limit', 'type' => 'number', 'label' => 'Usage limit'],
                ['name' => 'starts_at', 'type' => 'date', 'label' => 'Starts'],
                ['name' => 'expires_at', 'type' => 'date', 'label' => 'Expires'],
                ['name' => 'status', 'type' => 'select', 'label' => 'Status', 'options' => ['active' => 'Active', 'inactive' => 'Inactive']],
            ],
        ],
        'permits' => [
            'table' => 'permits', 'label' => 'Permits', 'singular' => 'Permit',
            'icon' => 'file-text', 'order' => 'created_at DESC',
            'title_col' => 'full_name', 'sub_col' => 'permit_type', 'image_col' => null,
            'fields' => [
                ['name' => 'permit_type', 'type' => 'select', 'label' => 'Type',
                 'options' => ['gorilla' => 'Gorilla', 'golden_monkey' => 'Golden monkey', 'chimp' => 'Chimpanzee', 'other' => 'Other']],
                ['name' => 'full_name', 'type' => 'text', 'label' => 'Full name', 'required' => true],
                ['name' => 'email', 'type' => 'text', 'label' => 'Email'],
                ['name' => 'phone', 'type' => 'text', 'label' => 'Phone'],
                ['name' => 'nationality', 'type' => 'text', 'label' => 'Nationality'],
                ['name' => 'passport_no', 'type' => 'text', 'label' => 'Passport no'],
                ['name' => 'passport_expiry', 'type' => 'date', 'label' => 'Passport expiry'],
                ['name' => 'permit_date', 'type' => 'date', 'label' => 'Permit date', 'required' => true],
                ['name' => 'num_permits', 'type' => 'number', 'label' => 'Number of permits'],
                ['name' => 'price_each', 'type' => 'decimal', 'label' => 'Price each'],
                ['name' => 'status', 'type' => 'select', 'label' => 'Status',
                 'options' => ['requested' => 'Requested', 'approved' => 'Approved', 'paid' => 'Paid', 'rejected' => 'Rejected', 'issued' => 'Issued']],
                ['name' => 'notes', 'type' => 'textarea', 'label' => 'Notes', 'rows' => 3],
            ],
        ],
        'tour-dates' => [
            'table' => 'tour_dates', 'label' => 'Departure dates', 'singular' => 'Departure',
            'icon' => 'calendar', 'order' => 'start_date ASC',
            'title_col' => 'start_date', 'sub_col' => 'status', 'image_col' => null,
            'fields' => [
                ['name' => 'tour_id', 'type' => 'relation', 'label' => 'Tour', 'table' => 'tours', 'label_col' => 'title', 'required' => true],
                ['name' => 'start_date', 'type' => 'date', 'label' => 'Start date', 'required' => true],
                ['name' => 'end_date', 'type' => 'date', 'label' => 'End date'],
                ['name' => 'capacity', 'type' => 'number', 'label' => 'Capacity'],
                ['name' => 'booked', 'type' => 'number', 'label' => 'Booked'],
                ['name' => 'seasonal_price', 'type' => 'decimal', 'label' => 'Seasonal price'],
                ['name' => 'status', 'type' => 'select', 'label' => 'Status',
                 'options' => ['open' => 'Open', 'closed' => 'Closed', 'cancelled' => 'Cancelled']],
            ],
        ],
    ];
}

function admin_resource_config(string $resource): ?array {
    $all = admin_resources();
    return $all[$resource] ?? null;
}

// ============================================================== uploads
/**
 * Save an uploaded image (works on InfinityFree where the system temp dir
 * is missing - see .user.ini upload_tmp_dir).
 * Returns ['ok'=>true,'path'=>'uploads/..'] or ['ok'=>false,'error'=>'..'].
 */
function admin_upload_image(string $field): array {
    if (empty($_FILES[$field]) || ($_FILES[$field]['error'] ?? UPLOAD_ERR_NO_FILE) === UPLOAD_ERR_NO_FILE) {
        return ['ok' => false, 'skip' => true, 'path' => null];
    }
    $file = $_FILES[$field];
    $err = (int) $file['error'];
    $tmp = (string) $file['tmp_name'];

    if ($err === UPLOAD_ERR_INI_SIZE || $err === UPLOAD_ERR_FORM_SIZE) {
        return ['ok' => false, 'error' => 'File is larger than the server allows (' . ini_get('upload_max_filesize') . ' max).'];
    }
    if ($err === UPLOAD_ERR_NO_TMP_DIR || ($err === UPLOAD_ERR_CANT_WRITE && !is_file($tmp))) {
        return ['ok' => false, 'error' => 'Server temp folder missing. Create htdocs/tmp (777) and set upload_tmp_dir in .user.ini - see DEPLOY.md.'];
    }
    if ($err !== UPLOAD_ERR_OK || !is_uploaded_file($tmp)) {
        $messages = [
            UPLOAD_ERR_PARTIAL => 'The upload was interrupted - please try again.',
            UPLOAD_ERR_NO_FILE => 'No file selected.',
            UPLOAD_ERR_CANT_WRITE => 'Could not write the file - check uploads/ folder permissions (777).',
            UPLOAD_ERR_EXTENSION => 'Upload blocked by a PHP extension.',
        ];
        return ['ok' => false, 'error' => $messages[$err] ?? 'Upload failed (code ' . $err . ').'];
    }
    if ((int) $file['size'] > 6 * 1024 * 1024) {
        return ['ok' => false, 'error' => 'Image must be 6 MB or smaller.'];
    }

    $info = @getimagesize($tmp);
    if ($info === false) {
        return ['ok' => false, 'error' => 'That file is not a valid image (JPG, PNG, WebP or GIF only).'];
    }
    $mime = $info['mime'] ?? '';
    $ext = ['image/jpeg' => 'jpg', 'image/png' => 'png', 'image/webp' => 'webp', 'image/gif' => 'gif'][$mime] ?? null;
    if ($ext === null) {
        return ['ok' => false, 'error' => 'Unsupported image type. Use JPG, PNG, WebP or GIF.'];
    }

    $sub = date('Y-m');
    $dir = APP_ROOT . '/uploads/' . $sub;
    if (!is_dir($dir) && !@mkdir($dir, 0777, true)) {
        return ['ok' => false, 'error' => 'Could not create uploads/' . $sub . ' - check folder permissions (777 on InfinityFree).'];
    }
    $name = date('Ymd-His') . '-' . bin2hex(random_bytes(4)) . '.' . $ext;
    $dest = $dir . '/' . $name;

    if (is_uploaded_file($tmp)) {
        if (!@move_uploaded_file($tmp, $dest)) {
            // Fallback copy (some hosts need this) then clear the temp file.
            if (!@copy($tmp, $dest)) {
                return ['ok' => false, 'error' => 'Could not save the uploaded file - set uploads/ and tmp/ to 777 (see DEPLOY.md).'];
            }
            @unlink($tmp);
        }
    }
    @chmod($dest, 0644);

    // Downscale huge photos with GD to keep hosting disk usage sane.
    try {
        if (function_exists('imagecreatefromstring')) {
            $src = @imagecreatefromstring((string) file_get_contents($dest));
            if ($src) {
                $w = imagesx($src); $h = imagesy($src);
                $max = 1920;
                if ($w > $max || $h > $max) {
                    $ratio = min($max / $w, $max / $h);
                    $nw = (int) round($w * $ratio); $nh = (int) round($h * $ratio);
                    $out = imagecreatetruecolor($nw, $nh);
                    if ($mime === 'image/png' || $mime === 'image/webp') imagealphablending($out, false);
                    imagecopyresampled($out, $src, 0, 0, 0, 0, $nw, $nh, $w, $h);
                    if ($ext === 'jpg') imagejpeg($out, $dest, 84);
                    elseif ($ext === 'png') imagepng($out, $dest, 6);
                    elseif ($ext === 'webp' && function_exists('imagewebp')) imagewebp($out, $dest, 84);
                    else { imagedestroy($out); $out = null; }
                    if ($out) imagedestroy($out);
                }
                imagedestroy($src);
            }
        }
    } catch (Throwable $e) {
        error_log('[upload-resize] ' . $e->getMessage()); // non-fatal
    }

    return ['ok' => true, 'path' => 'uploads/' . $sub . '/' . $name];
}

// ============================================================= pages
function admin_layout_vars(array $extra = []): array {
    $user = auth_user();
    return array_merge([
        'title' => 'Admin',
        'active' => 'admin',
        'adminUser' => $user,
        'unreadNotifications' => (int) db_val(
            'SELECT COUNT(*) FROM notifications WHERE is_read = 0 AND (user_id IS NULL OR user_id = ?)',
            [(int) ($user['id'] ?? 0)]
        ),
    ], $extra);
}

function admin_overview(): void {
    require_admin();
    $monthStart = date('Y-m-01');

    $stats = [
        'revenue' => (float) db_val("SELECT COALESCE(SUM(total),0) FROM bookings WHERE status IN ('confirmed','completed')"),
        'revenue_month' => (float) db_val("SELECT COALESCE(SUM(total),0) FROM bookings WHERE status IN ('confirmed','completed') AND created_at >= ?", [$monthStart]),
        'bookings' => (int) db_val('SELECT COUNT(*) FROM bookings'),
        'pending' => (int) db_val("SELECT COUNT(*) FROM bookings WHERE status = 'pending'"),
        'customers' => (int) db_val("SELECT COUNT(*) FROM users WHERE role = 'customer'"),
        'tours' => (int) db_val("SELECT COUNT(*) FROM tours WHERE status = 'published'"),
        'hotels' => (int) db_val("SELECT COUNT(*) FROM hotels WHERE status = 'approved'"),
        'destinations' => (int) db_val("SELECT COUNT(*) FROM destinations WHERE status = 'published'"),
        'reviews_pending' => (int) db_val("SELECT COUNT(*) FROM reviews WHERE status = 'pending'"),
    ];

    $recent = db_all(
        "SELECT b.*, t.title AS tour_title, h.name AS hotel_name
         FROM bookings b LEFT JOIN tours t ON t.id = b.tour_id LEFT JOIN hotels h ON h.id = b.hotel_id
         ORDER BY b.created_at DESC LIMIT 8"
    );

    $chart = db_all(
        "SELECT DATE_FORMAT(created_at, '%Y-%m') AS ym, COUNT(*) AS cnt, COALESCE(SUM(total),0) AS amt
         FROM bookings WHERE created_at >= (CURDATE() - INTERVAL 6 MONTH)
         GROUP BY ym ORDER BY ym ASC"
    );

    render('admin/overview', admin_layout_vars([
        'stats' => $stats,
        'recent' => $recent,
        'chart' => $chart,
        'title' => 'Dashboard | Rwanda Explorer Admin',
    ]), 'admin');
}

function admin_analytics(): void {
    require_admin();
    $byTour = db_all(
        "SELECT t.title, COUNT(b.id) AS bookings, COALESCE(SUM(b.total),0) AS revenue
         FROM tours t JOIN bookings b ON b.tour_id = t.id
         GROUP BY t.id ORDER BY revenue DESC LIMIT 8"
    );
    $byHotel = db_all(
        "SELECT h.name, COUNT(b.id) AS bookings, COALESCE(SUM(b.total),0) AS revenue
         FROM hotels h JOIN bookings b ON b.hotel_id = h.id
         GROUP BY h.id ORDER BY revenue DESC LIMIT 8"
    );
    $byStatus = db_all('SELECT status, COUNT(*) AS cnt, COALESCE(SUM(total),0) AS amt FROM bookings GROUP BY status');
    $bySource = db_all('SELECT source, COUNT(*) AS cnt FROM bookings GROUP BY source');
    $topDest = db_all(
        "SELECT d.name, COUNT(b.id) AS bookings
         FROM destinations d
         LEFT JOIN tours t ON t.destination_id = d.id
         LEFT JOIN hotels h ON h.destination_id = d.id
         LEFT JOIN bookings b ON b.tour_id = t.id OR b.hotel_id = h.id
         GROUP BY d.id ORDER BY bookings DESC LIMIT 6"
    );

    render('admin/analytics', admin_layout_vars([
        'byTour' => $byTour, 'byHotel' => $byHotel,
        'byStatus' => $byStatus, 'bySource' => $bySource, 'topDest' => $topDest,
        'title' => 'Analytics | Rwanda Explorer Admin',
    ]), 'admin');
}

function admin_notifications(): void {
    require_admin();
    $filter = (string) input('filter', '');
    $sql = 'SELECT * FROM notifications';
    if ($filter === 'unread') $sql .= ' WHERE is_read = 0';
    $sql .= ' ORDER BY created_at DESC LIMIT 200';
    $items = db_all($sql);

    render('admin/notifications', admin_layout_vars([
        'items' => $items, 'filter' => $filter,
        'title' => 'Notifications | Rwanda Explorer Admin',
    ]), 'admin');
}

function admin_notifications_read(): void {
    csrf_verify();
    require_admin();
    $ids = (array) ($_POST['ids'] ?? []);
    try {
        if ($ids) {
            $in = implode(',', array_fill(0, count($ids), '?'));
            db()->prepare("UPDATE notifications SET is_read = 1 WHERE id IN ($in)")
                ->execute(array_map('intval', $ids));
        } else {
            db()->query('UPDATE notifications SET is_read = 1 WHERE is_read = 0');
        }
    } catch (Throwable $e) {
        error_log('[notifications] ' . $e->getMessage());
    }
    redirect('/admin/notifications');
}

function admin_settings(): void {
    require_admin();
    render('admin/settings', admin_layout_vars([
        'settings' => settings(),
        'title' => 'Settings | Rwanda Explorer Admin',
    ]), 'admin');
}

function admin_settings_save(): void {
    csrf_verify();
    require_admin();
    $allowed = [
        'site_name', 'tagline', 'phone', 'email', 'address', 'hours',
        'currency', 'facebook', 'instagram', 'twitter', 'youtube', 'footer_note',
    ];
    $count = 0;
    try {
        foreach ($allowed as $key) {
            if (!isset($_POST[$key])) continue;
            $value = mb_substr(trim((string) $_POST[$key]), 0, 4000);
            db()->prepare(
                'INSERT INTO settings (key_name, value) VALUES (?, ?)
                 ON DUPLICATE KEY UPDATE value = VALUES(value)'
            )->execute([$key, $value]);
            $count++;
        }
    } catch (Throwable $e) {
        error_log('[settings] ' . $e->getMessage());
        flash_set('error', 'Could not save settings. Please try again.');
        redirect('/admin/settings');
    }
    flash_set('success', "Settings saved ($count keys).");
    redirect('/admin/settings');
}

// ======================================================= admin requests
function admin_requests(): void {
    require_admin();
    $pending = db_all(
        "SELECT id, name, email, phone, status, created_at FROM users
         WHERE role = 'admin' AND status = 'pending' ORDER BY created_at ASC"
    );
    $rejected = db_all(
        "SELECT id, name, email, phone, status, created_at FROM users
         WHERE role = 'admin' AND status = 'rejected' ORDER BY created_at DESC LIMIT 25"
    );

    render('admin/requests', admin_layout_vars([
        'pending' => $pending, 'rejected' => $rejected,
        'title' => 'Admin requests | Rwanda Explorer Admin',
    ]), 'admin');
}

function admin_request_decide(string $id, string $decision = ''): void {
    // Called from /admin/requests/{id}/{decision} (route param) or the form field.
    if ($decision === '') $decision = (string) post('decision');
    csrf_verify();
    require_admin();
    if (!in_array($decision, ['approve', 'reject'], true)) {
        flash_set('error', 'Invalid decision.');
        redirect('/admin/requests');
    }
    $newStatus = $decision === 'approve' ? 'active' : 'rejected';
    try {
        $row = db_one("SELECT id, name, email FROM users WHERE id = ? AND role = 'admin' AND status = 'pending'", [(int) $id]);
        if (!$row) {
            flash_set('error', 'That request no longer exists (already handled or not found).');
            redirect('/admin/requests');
        }
        db()->prepare('UPDATE users SET status = ? WHERE id = ?')->execute([$newStatus, (int) $row['id']]);
        db_insert(
            "INSERT INTO notifications (user_id, type, title, message, channel) VALUES (?, 'admin_request', ?, ?, 'app')",
            [
                (int) $row['id'],
                $decision === 'approve' ? 'Your admin account was approved' : 'Your admin request was rejected',
                $decision === 'approve'
                    ? 'An existing admin approved your account. You can sign in at the admin login now.'
                    : 'An existing admin declined your admin registration request.',
            ]
        );
        flash_set('success', $decision === 'approve'
            ? "Approved {$row['name']}. They can sign in now."
            : "Rejected {$row['name']}'s request.");
    } catch (Throwable $e) {
        error_log('[admin-requests] ' . $e->getMessage());
        flash_set('error', 'Could not update the request. Please try again.');
    }
    redirect('/admin/requests');
}

function admin_profile(): void {
    require_admin();
    render('admin/profile', admin_layout_vars([
        'title' => 'My profile | Admin',
    ]), 'admin');
}

function admin_profile_save(): void {
    csrf_verify();
    require_admin();
    $user = auth_user();

    // --- change password (second form on the same page) ---
    if (($_POST['do'] ?? '') === 'password') {
        $current = (string) ($_POST['current_password'] ?? '');
        $new = (string) ($_POST['password'] ?? '');
        $confirm = (string) ($_POST['password_confirm'] ?? '');
        if (!password_verify($current, $user['password_hash'])) {
            flash_set('error', 'Your current password is incorrect.');
            redirect('/admin/profile');
        }
        if ($errs = password_errors($new)) {
            flash_set('error', implode(' ', $errs));
            redirect('/admin/profile');
        }
        if ($new !== $confirm) {
            flash_set('error', 'New passwords do not match.');
            redirect('/admin/profile');
        }
        try {
            $hash = password_hash($new, PASSWORD_BCRYPT, ['cost' => (int) cfg('BCRYPT_COST', 10)]);
            db()->prepare('UPDATE users SET password_hash = ? WHERE id = ?')->execute([$hash, (int) $user['id']]);
        } catch (Throwable $e) {
            error_log('[admin-password] ' . $e->getMessage());
            flash_set('error', 'Could not update the password.');
            redirect('/admin/profile');
        }
        flash_set('success', 'Password changed.');
        redirect('/admin/profile');
    }

    // --- update profile ---
    $name = post('name');
    $phone = post('phone');
    if (mb_strlen($name) < 2) {
        flash_set('error', 'Name must be at least 2 characters.');
        redirect('/admin/profile');
    }
    try {
        db()->prepare('UPDATE users SET name = ?, phone = ? WHERE id = ?')
            ->execute([$name, $phone ?: null, (int) $user['id']]);
    } catch (Throwable $e) {
        error_log('[admin-profile] ' . $e->getMessage());
        flash_set('error', 'Could not save the profile.');
        redirect('/admin/profile');
    }
    flash_set('success', 'Profile updated.');
    redirect('/admin/profile');
}

// ================================================================ CRUD
function admin_filter_sql(array $cfg, array &$params): string {
    $sql = '';
    if (!empty($cfg['where'])) $sql .= ' WHERE ' . $cfg['where'];
    $q = (string) input('q', '');
    if ($q !== '') {
        $cols = array_filter([
            $cfg['title_col'] ?? null,
            $cfg['sub_col'] ?? null,
            $cfg['image_col'] ?? null,
        ]);
        $likes = [];
        foreach ($cols as $c) { $likes[] = "$c LIKE ?"; $params[] = '%' . $q . '%'; }
        $sql .= ($sql ? ' AND ' : ' WHERE ') . '(' . implode(' OR ', $likes) . ')';
    }
    return $sql;
}

function admin_resource_list(string $resource): void {
    require_admin();
    $cfg = admin_resource_config($resource);
    if (!$cfg) page_404();

    $params = [];
    $where = admin_filter_sql($cfg, $params);
    $page = max(1, (int) input('page', 1));
    $perPage = 20;
    $total = (int) db_val('SELECT COUNT(*) FROM ' . $cfg['table'] . $where, $params);
    $pages = max(1, (int) ceil($total / $perPage));
    $page = min($page, $pages);

    $rows = db_all(
        'SELECT * FROM ' . $cfg['table'] . $where .
        ' ORDER BY ' . $cfg['order'] . ' LIMIT ' . (int) $perPage . ' OFFSET ' . (($page - 1) * $perPage),
        $params
    );

    render('admin/resource_list', admin_layout_vars([
        'cfg' => $cfg, 'resource' => $resource, 'rows' => $rows,
        'total' => $total, 'page' => $page, 'pages' => $pages,
        'q' => (string) input('q', ''),
        'title' => $cfg['label'] . ' | Rwanda Explorer Admin',
    ]), 'admin');
}

/** Collect validated POST values for the configured fields. */
function admin_collect_fields(array $cfg, ?array $existing, array &$errors): array {
    $data = [];
    $readonly = array_merge($cfg['readonly'] ?? [], $cfg['no_edit'] ?? []);

    foreach ($cfg['fields'] as $f) {
        $name = $f['name'];
        if (in_array($name, $readonly, true)) continue;
        $type = $f['type'];
        $label = $f['label'] ?? $name;
        $raw = $_POST[$name] ?? null;

        switch ($type) {
            case 'checkbox':
                $data[$name] = !empty($raw) ? 1 : 0;
                break;
            case 'lines':
                $lines = [];
                if (is_string($raw)) {
                    foreach (preg_split('/\r\n|\r|\n/', $raw) ?: [] as $line) {
                        $line = trim($line);
                        if ($line !== '') $lines[] = $line;
                    }
                }
                $data[$name] = json_encode($lines, JSON_UNESCAPED_UNICODE);
                break;
            case 'keyed_lines':
                $items = [];
                if (is_string($raw)) {
                    foreach (preg_split('/\r\n|\r|\n/', $raw) ?: [] as $line) {
                        $line = trim($line);
                        if ($line === '') continue;
                        $parts = array_map('trim', explode('|', $line, 2));
                        $items[] = ['title' => $parts[0], 'text' => $parts[1] ?? ''];
                    }
                }
                $data[$name] = json_encode($items, JSON_UNESCAPED_UNICODE);
                break;
            case 'number':
                $data[$name] = ($raw === null || $raw === '') ? ($f['default'] ?? 0) : (int) $raw;
                if ($f['required'] ?? false && !$raw) $errors[] = "$label is required.";
                break;
            case 'decimal':
                $data[$name] = ($raw === null || $raw === '') ? ($f['default'] ?? 0) : (float) $raw;
                break;
            case 'image':
                $upload = admin_upload_image($name);
                if (!$upload['ok'] && !empty($upload['error'])) $errors[] = $upload['error'];
                if (!empty($upload['skip'])) {
                    // Keep the existing value when no new file was chosen.
                    if ($existing !== null) $data[$name] = $existing[$name] ?? null;
                } elseif ($upload['ok']) {
                    $data[$name] = $upload['path'];
                    if ($existing && !empty($existing[$name])) admin_delete_file((string) $existing[$name]);
                }
                break;
            case 'slug':
                $from = $f['from'] ?? null;
                $source = is_string($_POST[$from] ?? null) ? $_POST[$from] : '';
                $data[$name] = slugify($source !== '' ? $source : (string) $raw);
                if ($existing && $data[$name] !== ($existing[$name] ?? '')) {
                    $dup = db_val(
                        'SELECT COUNT(*) FROM ' . $cfg['table'] . ' WHERE slug = ? AND id <> ?',
                        [$data[$name], (int) ($existing['id'] ?? 0)]
                    );
                    if ((int) $dup > 0) $errors[] = "Slug \"$data[$name]\" is already used - change the title slightly.";
                }
                break;
            case 'relation':
                $val = ($raw === '' || $raw === null) ? null : (int) $raw;
                if ($val === null && ($f['required'] ?? false)) $errors[] = "$label is required.";
                $data[$name] = $val;
                break;
            default: // text, textarea, select, date, datetime
                $val = is_string($raw) ? trim($raw) : '';
                if (($f['required'] ?? false) && $val === '') $errors[] = "$label is required.";
                if (isset($f['max']) && mb_strlen($val) > (int) $f['max']) {
                    $errors[] = "$label must be " . (int) $f['max'] . " characters or fewer.";
                }
                if ($type === 'date' && $val !== '' && !preg_match('/^\d{4}-\d{2}-\d{2}$/', $val)) {
                    $errors[] = "$label must be a valid date.";
                }
                if ($type === 'select' && $val !== '' && isset($f['options']) && !array_key_exists($val, $f['options'])) {
                    $errors[] = "$label has an invalid value.";
                }
                $data[$name] = $val === '' ? null : $val;
                if ($val === '' && in_array($type, ['text', 'textarea'], true)) $data[$name] = null;
                if ($type === 'select' && $val === '') $data[$name] = $f['default'] ?? null;
                break;
        }
    }

    // Auto-generate slug if the config has one and nothing was posted.
    foreach ($cfg['fields'] as $f) {
        if ($f['type'] === 'slug' && empty($data[$f['name']])) {
            $from = $f['from'] ?? null;
            $data[$f['name']] = slugify((string) ($_POST[$from] ?? ($existing[$from] ?? 'item')));
        }
    }
    return $data;
}

function admin_delete_file(string $path): void {
    if (!preg_match('#^uploads/[0-9]{4}-[0-9]{2}/[\w.\-]+$#', $path)) return;
    $abs = APP_ROOT . '/' . $path;
    if (is_file($abs)) @unlink($abs);
}

function admin_resource_new(string $resource): void {
    require_admin();
    $cfg = admin_resource_config($resource);
    if (!$cfg) page_404();
    if (!empty($cfg['no_create'])) {
        flash_set('error', $cfg['label'] . ' cannot be created here.');
        redirect('/admin/' . $resource);
    }

    $errors = [];
    if (is_post()) {
        csrf_verify();
        $data = admin_collect_fields($cfg, null, $errors);
        if (!$errors) {
            try {
                $id = db_insert('INSERT INTO ' . $cfg['table'] . ' (' .
                    implode(', ', array_keys($data)) . ') VALUES (' .
                    implode(', ', array_fill(0, count($data), '?')) . ')', array_values($data));
                // Bookings created in the dashboard start as 'dashboard' source.
                if ($cfg['table'] === 'bookings') {
                    db()->prepare("UPDATE bookings SET source = 'dashboard' WHERE id = ?")->execute([(int) $id]);
                }
                flash_set('success', $cfg['singular'] . ' created.');
                redirect('/admin/' . $resource);
            } catch (PDOException $e) {
                if ((string) $e->getCode() === '23000') {
                    $errors[] = 'A record with the same unique value (slug, code, email...) already exists.';
                } else {
                    error_log('[admin-create] ' . $e->getMessage());
                    $errors[] = 'Database error: could not save the ' . strtolower($cfg['singular']) . '.';
                }
            }
        }
        if ($errors) {
            flash_set('error', implode(' ', $errors));
            redirect('/admin/' . $resource . '/new');
        }
    }

    render('admin/resource_form', admin_layout_vars([
        'cfg' => $cfg, 'resource' => $resource, 'row' => null, 'isEdit' => false,
        'title' => 'New ' . $cfg['singular'] . ' | Admin',
    ]), 'admin');
}

function admin_resource_edit(string $resource, string $id): void {
    require_admin();
    $cfg = admin_resource_config($resource);
    if (!$cfg) page_404();

    $row = db_one('SELECT * FROM ' . $cfg['table'] . ' WHERE id = ?', [(int) $id]);
    if (!$row) page_404();

    if (is_post()) {
        csrf_verify();
        $errors = [];
        $data = admin_collect_fields($cfg, $row, $errors);
        if (!$errors) {
            try {
                if ($data) {
                    $set = implode(', ', array_map(fn($c) => "$c = ?", array_keys($data)));
                    db()->prepare('UPDATE ' . $cfg['table'] . " SET $set WHERE id = ?")
                        ->execute(array_merge(array_values($data), [(int) $id]));
                }
                flash_set('success', $cfg['singular'] . ' updated.');
                redirect('/admin/' . $resource);
            } catch (PDOException $e) {
                if ((string) $e->getCode() === '23000') {
                    $errors[] = 'A record with the same unique value already exists.';
                } else {
                    error_log('[admin-update] ' . $e->getMessage());
                    $errors[] = 'Database error: could not save changes.';
                }
            }
        }
        if ($errors) {
            flash_set('error', implode(' ', $errors));
            redirect('/admin/' . $resource . '/' . (int) $id . '/edit');
        }
    }

    render('admin/resource_form', admin_layout_vars([
        'cfg' => $cfg, 'resource' => $resource, 'row' => $row, 'isEdit' => true,
        'title' => 'Edit ' . $cfg['singular'] . ' | Admin',
    ]), 'admin');
}

function admin_resource_delete(string $resource, string $id): void {
    csrf_verify();
    require_admin();
    $cfg = admin_resource_config($resource);
    if (!$cfg) page_404();

    $row = db_one('SELECT * FROM ' . $cfg['table'] . ' WHERE id = ?', [(int) $id]);
    if (!$row) {
        flash_set('error', 'Record not found.');
        redirect('/admin/' . $resource);
    }

    try {
        db()->prepare('DELETE FROM ' . $cfg['table'] . ' WHERE id = ?')->execute([(int) $id]);
        if ($cfg['image_col'] ?? null) admin_delete_file((string) ($row[$cfg['image_col']] ?? ''));
        flash_set('success', $cfg['singular'] . ' deleted.');
    } catch (PDOException $e) {
        if ((string) $e->getCode() === '23000') {
            flash_set('error', 'Cannot delete: other records still reference this ' . strtolower($cfg['singular']) . '.');
        } else {
            error_log('[admin-delete] ' . $e->getMessage());
            flash_set('error', 'Could not delete this record.');
        }
    }
    redirect('/admin/' . $resource);
}
