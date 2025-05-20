<?php
require 'vendor/autoload.php';
use Dotenv\Dotenv;

$dotenv = Dotenv::createImmutable(__DIR__, '.env');
$dotenv->safeLoad();

var_dump(getenv('SMTP_HOST'));