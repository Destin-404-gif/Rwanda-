<?php
/**
 * Rwanda Explorer - front controller (all requests enter here).
 */
declare(strict_types=1);

require __DIR__ . '/app/bootstrap.php';
require __DIR__ . '/app/router.php';
require __DIR__ . '/app/controllers/site.php';
require __DIR__ . '/app/controllers/auth.php';
require __DIR__ . '/app/controllers/account.php';
require __DIR__ . '/app/controllers/admin.php';
require __DIR__ . '/app/controllers/api.php';

$router = new Router();

// ---------------------------------------------------------------- public
$router->get('/',                    'page_home');
$router->get('/destinations',        'page_destinations');
$router->get('/destinations/{slug}', 'page_destination');
$router->get('/experiences',         'page_experiences');
$router->get('/tours',               'page_tours');
$router->get('/tours/{slug}',        'page_tour');
$router->get('/hotels',              'page_hotels');
$router->get('/hotels/{slug}',       'page_hotel');
$router->get('/gallery',             'page_gallery');
$router->get('/about',               'page_about');
$router->get('/contact',             'page_contact');
$router->post('/contact',            'submit_contact');
$router->get('/search',              'page_search');
$router->get('/guide',               'page_guide');
$router->get('/blog',                'page_blog');
$router->get('/blog/{slug}',         'page_blog_post');

// ---------------------------------------------------------------- auth
$router->get('/login',               'login_form');
$router->post('/login',              'login_submit');
$router->get('/register',            'register_form');
$router->post('/register',           'register_submit');
$router->get('/logout',              'logout_customer');
$router->get('/forgot-password',     'forgot_form');
$router->post('/forgot-password',    'forgot_submit');
$router->get('/reset-password',      'reset_form');
$router->post('/reset-password',     'reset_submit');

// admin auth (must be registered before the generic /admin/{resource} rules)
$router->get('/admin/login',         'admin_login_form');
$router->post('/admin/login',        'admin_login_submit');
$router->get('/admin/register',      'admin_register_form');
$router->post('/admin/register',     'admin_register_submit');
$router->get('/admin/logout',        'logout_admin');
$router->get('/admin/forgot-password','admin_forgot_form');
$router->post('/admin/forgot-password','admin_forgot_submit');
$router->get('/admin/reset-password','admin_reset_form');
$router->post('/admin/reset-password','admin_reset_submit');

// ------------------------------------------------------- customer account
$router->get('/account',             'account_page');
$router->post('/account/profile',    'account_profile');
$router->post('/account/password',   'account_password');
$router->post('/booking/create',     'booking_create');
$router->post('/booking/{id}/cancel', 'booking_cancel');

// ---------------------------------------------------------------- admin
$router->get('/admin',                    'admin_overview');
$router->get('/admin/analytics',          'admin_analytics');
$router->get('/admin/notifications',      'admin_notifications');
$router->post('/admin/notifications/read','admin_notifications_read');
$router->get('/admin/requests',           'admin_requests');
$router->post('/admin/requests/{id}/{decision}', 'admin_request_decide');
$router->get('/admin/settings',           'admin_settings');
$router->post('/admin/settings',          'admin_settings_save');
$router->get('/admin/profile',            'admin_profile');
$router->post('/admin/profile',           'admin_profile_save');
$router->get('/admin/{resource}',             'admin_resource_list');
$router->get('/admin/{resource}/new',         'admin_resource_new');
$router->post('/admin/{resource}/new',        'admin_resource_new');
$router->get('/admin/{resource}/{id}/edit',   'admin_resource_edit');
$router->post('/admin/{resource}/{id}/edit',  'admin_resource_edit');
$router->post('/admin/{resource}/{id}/delete','admin_resource_delete');

// ---------------------------------------------------------------- APIs
$router->get('/api/search',                  'api_search');
$router->get('/api/notifications',           'api_notifications');
$router->post('/api/favorites/toggle',       'api_favorites_toggle');
$router->get('/api/account/bookings',        'api_account_bookings');
$router->get('/api/admin/stats',             'api_admin_stats');
$router->get('/api/admin/overview',          'api_admin_stats');
$router->get('/api/admin/bookings',          'api_admin_bookings');
$router->get('/api/admin/{resource}',        'api_admin_list');
$router->post('/api/admin/{resource}/{id}/delete', 'api_admin_delete');
$router->add('DELETE', '/api/admin/{resource}/{id}', 'api_admin_delete');

$router->dispatch(request_path(), $_SERVER['REQUEST_METHOD'] ?? 'GET');
