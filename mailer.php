<?php
require_once __DIR__ . '/config.php';

function send_code_mail($to, $code) {
    $subject = 'Merhaba, Toska hesabına giriş yapma kodunuz';

    $html = '<!DOCTYPE html>
<html>
<head><meta charset="UTF-8"></head>
<body style="margin:0;padding:0;background-color:#f4f4f7;font-family:Arial,Helvetica,sans-serif;">
  <table role="presentation" width="100%" cellspacing="0" cellpadding="0" border="0" style="background-color:#f4f4f7;">
    <tr>
      <td align="center" style="padding:40px 0;">
        <table role="presentation" width="500" cellspacing="0" cellpadding="0" border="0" style="background-color:#ffffff;border-radius:12px;box-shadow:0 4px 12px rgba(0,0,0,0.08);">
          <tr>
            <td align="center" style="padding:40px 30px 20px;">
              <h1 style="margin:0;color:#2c3e50;font-size:24px;font-weight:600;">Merhaba,</h1>
              <p style="margin:10px 0 0;color:#555;font-size:16px;"><strong>Toska</strong> hesabına giriş yapma kodunuz aşağıdadır.</p>
            </td>
          </tr>
          <tr>
            <td align="center" style="padding:10px 30px 30px;">
              <div style="background-color:#f0f4ff;border:2px dashed #4a6cf7;border-radius:10px;padding:20px 0;">
                <span style="font-size:38px;font-weight:700;letter-spacing:12px;color:#2c3e50;">' . $code . '</span>
              </div>
            </td>
          </tr>
          <tr>
            <td align="center" style="padding:0 30px 20px;">
              <p style="margin:0;color:#888;font-size:14px;line-height:1.5;">Bu kod <b>10 dakika</b> geçerlidir.<br>Kodu kimseyle paylaşmayın.</p>
            </td>
          </tr>
          <tr>
            <td align="center" style="padding:0 30px 30px;">
              <p style="margin:0;color:#e67e22;font-size:13px;font-weight:600;background-color:#fff7e6;border-radius:8px;padding:10px;">📩 E-postayı gelen kutunuzda göremezseniz <b>Spam / Gereksiz</b> klasörünüzü kontrol etmeyi unutmayın.</p>
            </td>
          </tr>
          <tr>
            <td align="center" style="padding:20px 30px;background-color:#f9fafb;border-radius:0 0 12px 12px;border-top:1px solid #eee;">
              <p style="margin:0;color:#aaa;font-size:12px;">Bu e-postayı siz talep etmediyseniz, lütfen dikkate almayın.</p>
            </td>
          </tr>
        </table>
      </td>
    </tr>
  </table>
</body>
</html>';

    $boundary = '=_b_' . md5((string)time() . random_int(0, 999999));

    $headers  = 'MIME-Version: 1.0' . "\r\n";
    $headers .= 'From: "Toska" <' . MAIL_USER . '>' . "\r\n";
    $headers .= 'Reply-To: ' . MAIL_USER . "\r\n";
    $headers .= 'Content-Type: multipart/alternative; boundary="' . $boundary . '"' . "\r\n";

    $body  = '--' . $boundary . "\r\n";
    $body .= 'Content-Type: text/plain; charset=UTF-8' . "\r\n\r\n";
    $body .= "Toska doğrulama kodun: $code\r\n\r\n";
    $body .= "Bu kod 10 dakika geçerlidir.\r\n\r\n";
    $body .= "--" . $boundary . "\r\n";
    $body .= 'Content-Type: text/html; charset=UTF-8' . "\r\n";
    $body .= 'Content-Transfer-Encoding: base64' . "\r\n\r\n";
    $body .= chunk_split(base64_encode($html)) . "\r\n";
    $body .= '--' . $boundary . '--';

    $context = stream_context_create([
        'ssl' => [
            'verify_peer' => true,
            'verify_peer_name' => true,
            'allow_self_signed' => false,
            'SNI_enabled' => true
        ]
    ]);

    $smtp_host = 'ssl://smtp.gmail.com';
    $smtp_port = 465;

    $socket = @stream_socket_client(
        $smtp_host . ':' . $smtp_port,
        $errno, $errstr, 15,
        STREAM_CLIENT_CONNECT, $context
    );

    if (!$socket) {
        return ['ok' => false, 'error' => "Bağlantı hatası: $errstr"];
    }

    stream_set_timeout($socket, 15);

    $read = function() use ($socket) {
        $data = '';
        while (($line = fgets($socket, 515)) !== false) {
            $data .= $line;
            if (isset($line[3]) && $line[3] === ' ') break;
        }
        return $data;
    };

    $cmd = function($command) use ($socket, $read) {
        fwrite($socket, $command . "\r\n");
        return $read();
    };

    $read();

    $cmd('EHLO ' . ($_SERVER['SERVER_NAME'] ?? 'localhost'));

    $cmd('AUTH LOGIN');
    $cmd(base64_encode(MAIL_USER));
    $auth = $cmd(base64_encode(MAIL_PASS));

    if (strpos($auth, '235') === false) {
        fclose($socket);
        return ['ok' => false, 'error' => 'Gmail kimlik doğrulama başarısız'];
    }

    $cmd('MAIL FROM: <' . MAIL_USER . '>');
    $rcpt = $cmd('RCPT TO: <' . $to . '>');
    if (strpos($rcpt, '250') === false && strpos($rcpt, '251') === false) {
        fclose($socket);
        return ['ok' => false, 'error' => 'Alıcı reddedildi'];
    }

    $cmd('DATA');

    $message  = 'Subject: =?UTF-8?B?' . base64_encode($subject) . '?=' . "\r\n";
    $message .= 'To: <' . $to . '>' . "\r\n";
    $message .= $headers . "\r\n";
    $message .= $body . "\r\n.";
    $send = $cmd($message);

    $cmd('QUIT');
    fclose($socket);

    if (strpos($send, '250') !== false) {
        return ['ok' => true];
    }
    return ['ok' => false, 'error' => 'Gönderim başarısız: ' . $send];
}