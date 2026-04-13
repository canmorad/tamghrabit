<?php
use Dotenv\Dotenv;

$dotenv = Dotenv::createImmutable(__DIR__ . '/../../');
$dotenv->load();

return [
    'db' => [
        'host' => $_ENV['DB_HOST'],
        'name' => $_ENV['DB_NAME'],
        'user' => $_ENV['DB_USER'],
        'pass' => $_ENV['DB_PASS'],
    ],
    'mail' => [
        'host' => $_ENV['MAIL_HOST'],
        'user' => $_ENV['MAIL_USERNAME'],
        'pass' => $_ENV['MAIL_PASSWORD'],
        'port' => $_ENV['MAIL_PORT'],
        'from_name' => $_ENV['MAIL_FROM_NAME'],
    ],
    'google' => [
        'client_id'     => $_ENV['GOOGLE_CLIENT_ID'],
        'client_secret' => $_ENV['GOOGLE_CLIENT_SECRET'],
        'redirect_uri'  => $_ENV['GOOGLE_REDIRECT_URI'],
    ]
];