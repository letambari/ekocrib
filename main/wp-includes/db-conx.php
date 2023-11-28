<?php


require_once __DIR__ . '/../../vendor/autoload.php'; // Load Composer's autoloader

// Load the environment variables from the .env file
$dotenv = Dotenv\Dotenv::createImmutable(__DIR__ . '/../..');
$dotenv->load();

// Access environment variables
$db_host = $_ENV['DB_HOST'];
$db_user = $_ENV['DB_USER'];
$db_pass = $_ENV['DB_PASS'];
$db_name = $_ENV['DB_NAME'];
$now_api_key = $_ENV['NOW_API_KEY'];
$site_api_key = $_ENV['API_KEY'];
$now_web_url = $_ENV['NOW_WEB_URL'];
$web_url = $_ENV['WEB_URL'];
$email_port = $_ENV['EMAIL_SERVER_PORT'];
$server_host = $_ENV['EMAIL_SERVER_HOST'];
$server_password = $_ENV['EMAIL_SERVER_PASSWORD'];
$server_username = $_ENV['EMAIL_SERVER_USERNAME'];
$server_ssl = $_ENV['EMAIL_SERVER_SMTPSecure'];
$jwt_key = $_ENV['JWT_KEY'];
// $data = array($web_url);
// echo json_encode($data);
