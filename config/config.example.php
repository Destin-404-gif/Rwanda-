<?php
/**
 * Rwanda Explorer - LIVE configuration template (InfinityFree).
 * Copy this file to config/config.php on the server, then review every value.
 * config/config.php is git-ignored and must never be committed.
 */

return [
    // ---- Database: InfinityFree ---------------------------------------
    'DB_HOST' => 'sql308.infinityfree.com',
    'DB_PORT' => '3306',
    'DB_NAME' => 'if0_43229793_Rwanda',
    'DB_USER' => 'if0_43229793',
    'DB_PASS' => 'Rwanda2026',

    // ---- Site ---------------------------------------------------------
    'SITE_URL'  => 'https://rwanda-explorer.wuaze.com', // no trailing slash
    'BASE_PATH' => '',                                   // domain root
    'APP_ENV'   => 'production',                         // hides PHP errors

    // ---- Security -----------------------------------------------------
    'SESSION_NAME'   => 'rwanda_explorer_session',
    'SESSION_SECURE' => true,   // HTTPS is active on InfinityFree
    'BCRYPT_COST'    => 10,

    // ---- Uploads ------------------------------------------------------
    'UPLOAD_DIR'    => __DIR__ . '/../uploads',
    'UPLOAD_URL'    => '/uploads',
    'MAX_UPLOAD_MB' => 4,
];
