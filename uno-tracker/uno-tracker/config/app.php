<?php

return [
    'name'            => 'UNO Tracker',
    'version'         => '1.0.0',
    'debug'           => getenv('APP_DEBUG') ?: true,
    'log_enabled'     => getenv('APP_LOG_ENABLED') ?: true, // 🆕 کلید جدید
    'url'             => getenv('APP_URL') ?: 'http://localhost:8000',
    'timezone'        => 'Asia/Tehran',
    'super_admin_id'  => 1, // شناسه مدیر ارشد (محافظت شده)
    'session_lifetime' => 3600 * 24, // 24 ساعت
    'upload_path'     => dirname(__DIR__) . '/storage/uploads/',
    'log_path'        => dirname(__DIR__) . '/storage/logs/',
];
