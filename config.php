<?php
session_start();

define('DATA_FILE', __DIR__ . '/data.json');

define('GROQ_API_KEY', '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');

define('MAIL_USER', 'muhammedala771@gmail.com');
define('MAIL_PASS', 'luxa nukv iiel ojiq');

define('ADMIN_USER', 'toska');
define('ADMIN_PASS', 'allah');

function read_data() {
    if (!file_exists(DATA_FILE)) {
        file_put_contents(DATA_FILE, json_encode([
            'visits' => [],
            'users' => [],
            'chats' => []
        ], JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE));
        @chmod(DATA_FILE, 0600);
    }
    $raw = @file_get_contents(DATA_FILE);
    $data = json_decode($raw, true);
    if (!is_array($data)) $data = [];
    if (!isset($data['visits'])) $data['visits'] = [];
    if (!isset($data['users']))  $data['users']  = [];
    if (!isset($data['chats']))  $data['chats']  = [];
    return $data;
}

function write_data($data) {
    $tmp = DATA_FILE . '.tmp';
    file_put_contents($tmp, json_encode($data, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE), LOCK_EX);
    @chmod($tmp, 0600);
    rename($tmp, DATA_FILE);
}

function clean($str, $max = 500) {
    if (!is_string($str)) return '';
    $str = preg_replace('/[\x00-\x1F\x7F]/u', '', $str);
    $str = trim($str);
    if (function_exists('mb_substr')) return mb_substr($str, 0, $max, 'UTF-8');
    return substr($str, 0, $max);
}

function valid_email($email) {
    if (!is_string($email)) return false;
    if (strlen($email) > 254) return false;
    return (bool)filter_var($email, FILTER_VALIDATE_EMAIL);
}

function gen_code() {
    return str_pad((string)random_int(100000, 999999), 6, '0', STR_PAD_LEFT);
}

function json_out($arr, $code = 200) {
    http_response_code($code);
    header('Content-Type: application/json; charset=utf-8');
    echo json_encode($arr, JSON_UNESCAPED_UNICODE);
    exit;
}

function log_visit($page) {
    if (strpos($page, '/admin') === 0 || strpos($page, '/api') === 0) return;
    if (preg_match('/\.(css|js|png|jpg|jpeg|gif|mp3|mp4|svg|ico|woff2?|webp)$/i', $page)) return;

    $data = read_data();
    $ip = $_SERVER['HTTP_X_FORWARDED_FOR'] ?? $_SERVER['REMOTE_ADDR'] ?? 'bilinmiyor';
    $ip = explode(',', $ip)[0];
    $data['visits'][] = [
        'id' => count($data['visits']) + 1,
        'ip' => clean($ip, 50),
        'page' => clean($page, 100),
        'user_agent' => clean($_SERVER['HTTP_USER_AGENT'] ?? '', 200),
        'referer' => clean($_SERVER['HTTP_REFERER'] ?? '', 200),
        'time' => date('c')
    ];
    if (count($data['visits']) > 2000) {
        $data['visits'] = array_slice($data['visits'], -2000);
    }
    write_data($data);
}