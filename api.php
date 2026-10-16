<?php
require_once __DIR__ . '/config.php';
require_once __DIR__ . '/mailer.php';

header('Content-Type: application/json; charset=utf-8');

$action = $_GET['action'] ?? '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $raw = file_get_contents('php://input');
    $input = json_decode($raw, true);
    if (!is_array($input)) $input = $_POST;
} else {
    $input = $_GET;
}

function out($arr, $code = 200) {
    http_response_code($code);
    echo json_encode($arr, JSON_UNESCAPED_UNICODE);
    exit;
}

if ($action === 'send_code') {
    $email = strtolower(clean($input['email'] ?? '', 254));
    if (!valid_email($email)) out(['error' => 'Geçersiz email'], 400);

    $code = gen_code();
    $expires = time() + 600;

    $data = read_data();
    $found = false;
    foreach ($data['users'] as $i => $u) {
        if ($u['email'] === $email) {
            $data['users'][$i]['code'] = $code;
            $data['users'][$i]['code_expires'] = $expires;
            $data['users'][$i]['verified'] = false;
            $found = true;
            break;
        }
    }
    if (!$found) {
        $data['users'][] = [
            'id' => count($data['users']) + 1,
            'email' => $email,
            'verified' => false,
            'code' => $code,
            'code_expires' => $expires,
            'created_at' => date('c')
        ];
    }
    write_data($data);

    $result = send_code_mail($email, $code);
    if (!empty($result['ok'])) out(['ok' => true]);
    out(['error' => $result['error'] ?? 'Mail gönderilemedi'], 500);
}

if ($action === 'verify_code') {
    $email = strtolower(clean($input['email'] ?? '', 254));
    $code = clean($input['code'] ?? '', 6);

    if (!valid_email($email) || !preg_match('/^\d{6}$/', $code)) {
        out(['error' => 'Geçersiz veri'], 400);
    }

    $data = read_data();
    $idx = -1;
    foreach ($data['users'] as $i => $u) {
        if ($u['email'] === $email) { $idx = $i; break; }
    }

    if ($idx < 0) out(['error' => 'Kod bulunamadı'], 400);

    $u = $data['users'][$idx];
    if (empty($u['code']) || empty($u['code_expires'])) out(['error' => 'Kod bulunamadı'], 400);
    if (time() > $u['code_expires']) out(['error' => 'Kod süresi doldu'], 400);
    if ($u['code'] !== $code) out(['error' => 'Kod yanlış'], 400);

    $data['users'][$idx]['verified'] = true;
    $data['users'][$idx]['code'] = null;
    $data['users'][$idx]['code_expires'] = null;
    write_data($data);

    $_SESSION['email'] = $email;
    out(['ok' => true]);
}

if ($action === 'chat') {
    if (empty($_SESSION['email'])) out(['error' => 'Önce doğrulama yap'], 401);

    $email = $_SESSION['email'];
    $message = clean($input['message'] ?? '', 1000);
    if ($message === '') out(['error' => 'Boş mesaj'], 400);

    if (!function_exists('curl_init')) {
        out(['error' => 'Sunucuda cURL eklentisi yok. php.ini içinde extension=curl aç'], 500);
    }

    if (empty(GROQ_API_KEY) || strpos(GROQ_API_KEY, 'gsk_') !== 0) {
        out(['error' => 'Groq API anahtarı ayarlı değil (config.php)'], 500);
    }

    $data = read_data();

    $data['chats'][] = [
        'id' => count($data['chats']) + 1,
        'email' => $email,
        'role' => 'user',
        'message' => $message,
        'time' => date('c')
    ];

    $history = [];
    foreach ($data['chats'] as $c) {
        if ($c['email'] === $email) $history[] = $c;
    }
    $history = array_slice($history, -10);

    $messages = [
        ['role' => 'system', 'content' => 'Sen Tosmen adında samimi, Türkçe konuşan bir AI asistanısın. Kısa ve net cevap ver.']
    ];
    foreach ($history as $h) {
        $messages[] = ['role' => $h['role'], 'content' => $h['message']];
    }

    $payload = json_encode([
        'model' => 'openai/gpt-oss-120b',
        'messages' => $messages,
        'max_tokens' => 800,
        'temperature' => 0.7
    ], JSON_UNESCAPED_UNICODE);

    $ch = curl_init('https://api.groq.com/openai/v1/chat/completions');
    curl_setopt_array($ch, [
        CURLOPT_RETURNTRANSFER => true,
        CURLOPT_POST => true,
        CURLOPT_POSTFIELDS => $payload,
        CURLOPT_HTTPHEADER => [
            'Content-Type: application/json',
            'Authorization: Bearer ' . GROQ_API_KEY
        ],
        CURLOPT_TIMEOUT => 30,
        CURLOPT_CONNECTTIMEOUT => 10,
        CURLOPT_SSL_VERIFYPEER => false,
        CURLOPT_SSL_VERIFYHOST => false
    ]);

    $resp = curl_exec($ch);
    $http = curl_getinfo($ch, CURLINFO_HTTP_CODE);
    $curlErr = curl_error($ch);
    curl_close($ch);

    if ($resp === false || $http !== 200) {
        out([
            'error' => 'AI cevap veremedi',
            'http' => $http,
            'curl' => $curlErr,
            'detay' => is_string($resp) ? substr($resp, 0, 500) : null
        ], 500);
    }

    $json = json_decode($resp, true);
    $reply = $json['choices'][0]['message']['content'] ?? '';
    if ($reply === '') {
        out([
            'error' => 'Boş cevap',
            'detay' => substr($resp, 0, 500)
        ], 500);
    }

    $data['chats'][] = [
        'id' => count($data['chats']) + 1,
        'email' => $email,
        'role' => 'assistant',
        'message' => $reply,
        'time' => date('c')
    ];
    write_data($data);

    out(['reply' => $reply]);
}

if ($action === 'me') {
    out(['email' => $_SESSION['email'] ?? null]);
}

if ($action === 'admin_login') {
    $user = clean($input['user'] ?? '', 100);
    $pass = clean($input['pass'] ?? '', 200);

    if ($user === ADMIN_USER && $pass === ADMIN_PASS) {
        $_SESSION['admin'] = true;
        out(['ok' => true]);
    }
    out(['error' => 'Hatalı giriş'], 401);
}

if ($action === 'admin_logout') {
    session_destroy();
    out(['ok' => true]);
}

if ($action === 'admin_stats') {
    if (empty($_SESSION['admin'])) out(['error' => 'Yetkisiz'], 401);

    $data = read_data();
    $pageStats = [];
    foreach ($data['visits'] as $v) {
        $p = $v['page'];
        $pageStats[$p] = ($pageStats[$p] ?? 0) + 1;
    }
    arsort($pageStats);
    $pageArr = [];
    foreach ($pageStats as $p => $c) $pageArr[] = ['page' => $p, 'c' => $c];

    $verified = 0;
    foreach ($data['users'] as $u) if (!empty($u['verified'])) $verified++;

    out([
        'totalVisits' => count($data['visits']),
        'totalUsers' => count($data['users']),
        'verifiedUsers' => $verified,
        'totalChats' => count($data['chats']),
        'pageStats' => $pageArr
    ]);
}

if ($action === 'admin_visits') {
    if (empty($_SESSION['admin'])) out(['error' => 'Yetkisiz'], 401);
    $data = read_data();
    out(array_reverse(array_slice($data['visits'], -200)));
}

if ($action === 'admin_chats') {
    if (empty($_SESSION['admin'])) out(['error' => 'Yetkisiz'], 401);
    $data = read_data();
    out(array_reverse(array_slice($data['chats'], -200)));
}

out(['error' => 'Bilinmeyen işlem: ' . $action], 404);