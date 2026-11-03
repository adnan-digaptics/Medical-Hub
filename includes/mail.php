<?php

function mmh_notify_email() {
  $email = trim((string) mmh_setting('notify_email', ''));
  if ($email !== '' && filter_var($email, FILTER_VALIDATE_EMAIL)) {
    return $email;
  }
  $from = trim((string) mmh_setting('from_email', ''));
  return filter_var($from, FILTER_VALIDATE_EMAIL) ? $from : '';
}

function mmh_from_email() {
  $from = trim((string) mmh_setting('from_email', ''));
  if ($from !== '' && filter_var($from, FILTER_VALIDATE_EMAIL)) {
    return $from;
  }
  $notify = mmh_notify_email();
  if ($notify) {
    return $notify;
  }
  return 'noreply@' . (isset($_SERVER['HTTP_HOST']) ? preg_replace('/:\d+$/', '', (string) $_SERVER['HTTP_HOST']) : 'midwestmedicalhub.local');
}

function mmh_mail_log($to, $subject, $ok, $detail = '') {
  $dir = MMH_ROOT . '/uploads/mail';
  if (!is_dir($dir)) {
    mkdir($dir, 0775, true);
  }
  $line = date('Y-m-d H:i:s') . ' | ' . ($ok ? 'OK' : 'FAIL') . ' | to=' . $to . ' | ' . $subject . ($detail !== '' ? ' | ' . $detail : '') . PHP_EOL;
  file_put_contents($dir . '/mail.log', $line, FILE_APPEND | LOCK_EX);
}

function mmh_mail($to, $subject, $html, $replyTo = '') {
  $to = trim((string) $to);
  if (!filter_var($to, FILTER_VALIDATE_EMAIL)) {
    mmh_mail_log($to, $subject, false, 'invalid recipient');
    return false;
  }
  $from = mmh_from_email();
  $fromName = trim((string) mmh_setting('from_name', 'MidWest Medical Hub'));
  $text = trim(html_entity_decode(strip_tags(str_replace(array('<br>', '<br/>', '<br />', '</p>', '</div>'), "\n", $html)), ENT_QUOTES, 'UTF-8'));
  $host = trim((string) mmh_setting('smtp_host', ''));
  $ok = false;
  $detail = '';
  if ($host !== '') {
    $ok = mmh_smtp_send($to, $subject, $html, $text, $from, $fromName, $replyTo, $detail);
  } else {
    $ok = mmh_php_mail($to, $subject, $html, $text, $from, $fromName, $replyTo, $detail);
  }
  mmh_mail_log($to, $subject, $ok, $detail);
  return $ok;
}

function mmh_php_mail($to, $subject, $html, $text, $from, $fromName, $replyTo, &$detail) {
  $boundary = 'mmh' . bin2hex(random_bytes(8));
  $headers = array();
  $headers[] = 'MIME-Version: 1.0';
  $headers[] = 'From: ' . mmh_mail_address($fromName, $from);
  if ($replyTo !== '' && filter_var($replyTo, FILTER_VALIDATE_EMAIL)) {
    $headers[] = 'Reply-To: ' . $replyTo;
  }
  $headers[] = 'Content-Type: multipart/alternative; boundary="' . $boundary . '"';
  $body = mmh_mail_body($boundary, $text, $html);
  $encodedSubject = '=?UTF-8?B?' . base64_encode($subject) . '?=';
  $sent = @mail($to, $encodedSubject, $body, implode("\r\n", $headers));
  $detail = $sent ? 'php-mail' : 'php-mail-failed';
  return (bool) $sent;
}

function mmh_smtp_send($to, $subject, $html, $text, $from, $fromName, $replyTo, &$detail) {
  $host = trim((string) mmh_setting('smtp_host', ''));
  $port = (int) mmh_setting('smtp_port', '587');
  if ($port <= 0) {
    $port = 587;
  }
  $user = trim((string) mmh_setting('smtp_user', ''));
  $pass = (string) mmh_setting('smtp_pass', '');
  $secure = strtolower(trim((string) mmh_setting('smtp_secure', 'tls')));
  $remote = ($secure === 'ssl' ? 'ssl://' : 'tcp://') . $host . ':' . $port;
  $errno = 0;
  $errstr = '';
  $fp = @stream_socket_client($remote, $errno, $errstr, 20, STREAM_CLIENT_CONNECT);
  if (!$fp) {
    $detail = 'connect: ' . $errstr;
    return false;
  }
  stream_set_timeout($fp, 20);
  $read = function () use ($fp) {
    $data = '';
    while (!feof($fp)) {
      $line = fgets($fp, 2048);
      if ($line === false) {
        break;
      }
      $data .= $line;
      if (isset($line[3]) && $line[3] === ' ') {
        break;
      }
    }
    return $data;
  };
  $cmd = function ($command, $expect) use ($fp, $read) {
    fwrite($fp, $command . "\r\n");
    $resp = $read();
    $code = (int) substr($resp, 0, 3);
    if ($code !== $expect) {
      throw new RuntimeException(trim($resp));
    }
    return $resp;
  };
  try {
    $read();
    $ehloHost = preg_replace('/:\d+$/', '', (string) ($from !== '' ? substr(strrchr($from, '@'), 1) : 'localhost'));
    $cmd('EHLO ' . ($ehloHost !== '' ? $ehloHost : 'localhost'), 250);
    if ($secure === 'tls') {
      $cmd('STARTTLS', 220);
      if (!stream_socket_enable_crypto($fp, true, STREAM_CRYPTO_METHOD_TLS_CLIENT)) {
        throw new RuntimeException('TLS handshake failed');
      }
      $cmd('EHLO ' . ($ehloHost !== '' ? $ehloHost : 'localhost'), 250);
    }
    if ($user !== '') {
      $cmd('AUTH LOGIN', 334);
      $cmd(base64_encode($user), 334);
      $cmd(base64_encode($pass), 235);
    }
    $cmd('MAIL FROM:<' . $from . '>', 250);
    $cmd('RCPT TO:<' . $to . '>', 250);
    $cmd('DATA', 354);
    $boundary = 'mmh' . bin2hex(random_bytes(8));
    $headers = array(
      'Date: ' . date('r'),
      'From: ' . mmh_mail_address($fromName, $from),
      'To: <' . $to . '>',
      'Subject: =?UTF-8?B?' . base64_encode($subject) . '?=',
      'MIME-Version: 1.0',
      'Content-Type: multipart/alternative; boundary="' . $boundary . '"',
    );
    if ($replyTo !== '' && filter_var($replyTo, FILTER_VALIDATE_EMAIL)) {
      $headers[] = 'Reply-To: ' . $replyTo;
    }
    $payload = implode("\r\n", $headers) . "\r\n\r\n" . mmh_mail_body($boundary, $text, $html) . "\r\n.";
    fwrite($fp, $payload . "\r\n");
    $resp = $read();
    if ((int) substr($resp, 0, 3) !== 250) {
      throw new RuntimeException(trim($resp));
    }
    $cmd('QUIT', 221);
    fclose($fp);
    $detail = 'smtp:' . $host;
    return true;
  } catch (Throwable $e) {
    $detail = $e->getMessage();
    fclose($fp);
    return false;
  }
}

function mmh_mail_address($name, $email) {
  $name = trim(str_replace(array("\r", "\n"), '', $name));
  if ($name === '') {
    return $email;
  }
  return '=?UTF-8?B?' . base64_encode($name) . '?= <' . $email . '>';
}

function mmh_mail_body($boundary, $text, $html) {
  $parts = array(
    '--' . $boundary,
    'Content-Type: text/plain; charset=UTF-8',
    'Content-Transfer-Encoding: base64',
    '',
    chunk_split(base64_encode($text !== '' ? $text : strip_tags($html))),
    '--' . $boundary,
    'Content-Type: text/html; charset=UTF-8',
    'Content-Transfer-Encoding: base64',
    '',
    chunk_split(base64_encode($html)),
    '--' . $boundary . '--',
    '',
  );
  return implode("\r\n", $parts);
}

function mmh_email_wrap($title, $intro, $rows, $footer = '') {
  $items = '';
  foreach ($rows as $label => $value) {
    $items .= '<tr><td style="padding:8px 0;border-bottom:1px solid #e6eef6;color:#5b6b7c;width:160px;vertical-align:top">' . mmh_h($label) . '</td><td style="padding:8px 0;border-bottom:1px solid #e6eef6;color:#1c2a3a">' . nl2br(mmh_h((string) $value)) . '</td></tr>';
  }
  if ($footer === '') {
    $footer = 'MidWest Medical Hub';
  }
  return '<div style="font-family:Arial,Helvetica,sans-serif;background:#f4f7fb;padding:24px"><div style="max-width:640px;margin:0 auto;background:#fff;border:1px solid #d8e3ef;border-radius:12px;padding:28px"><h1 style="margin:0 0 12px;font-size:22px;color:#002261">' . mmh_h($title) . '</h1><p style="color:#1c2a3a;line-height:1.6">' . mmh_h($intro) . '</p><table style="width:100%;border-collapse:collapse;margin-top:16px">' . $items . '</table><p style="margin:24px 0 0;color:#5b6b7c;font-size:13px">' . mmh_h($footer) . '</p></div></div>';
}

function mmh_email_appointment($row, $kind = 'new') {
  $rows = array(
    'Name' => $row['name'] ?? '',
    'Email' => $row['email'] ?? '',
    'Address' => $row['address'] ?? '',
    'Service' => $row['service'] ?? '',
    'Preferred date & time' => mmh_format_preferred($row['preferred_at'] ?? ''),
    'Notes' => $row['notes'] ?? '',
    'Status' => $row['status'] ?? 'new',
  );
  if ($kind === 'new') {
    $staffIntro = 'A new appointment request was submitted on the website.';
    $userIntro = 'We received your appointment request. Our care team will follow up.';
    $staffSubject = 'New appointment request from ' . ($row['name'] ?? 'a visitor');
    $userSubject = 'We received your MidWest Medical Hub appointment request';
  } else {
    $staffIntro = 'An appointment record was updated in the admin panel.';
    $userIntro = 'Your appointment request at MidWest Medical Hub was updated.';
    $staffSubject = 'Appointment updated: ' . ($row['name'] ?? '');
    $userSubject = 'Your MidWest Medical Hub appointment was updated';
  }
  $staffHtml = mmh_email_wrap('Appointment request', $staffIntro, $rows);
  $userHtml = mmh_email_wrap('Appointment request', $userIntro, $rows, 'Reply to this email if you need to change anything.');
  $staffOk = true;
  $notify = mmh_notify_email();
  if ($notify) {
    $staffOk = mmh_mail($notify, $staffSubject, $staffHtml, $row['email'] ?? '');
  }
  $userOk = mmh_mail($row['email'] ?? '', $userSubject, $userHtml, $notify);
  return $staffOk && $userOk;
}

function mmh_email_contact($row, $kind = 'new') {
  $rows = array(
    'Name' => $row['name'] ?? '',
    'Email' => $row['email'] ?? '',
    'Phone' => $row['phone'] ?? '',
    'Subject' => $row['subject'] ?? '',
    'Message' => $row['message'] ?? '',
    'Status' => $row['status'] ?? 'new',
  );
  if (!empty($row['admin_notes'])) {
    $rows['Team note'] = $row['admin_notes'];
  }
  if ($kind === 'new') {
    $staffIntro = 'A new contact message was submitted on the website.';
    $userIntro = 'We received your message. Our care team will follow up.';
    $staffSubject = 'New contact message from ' . ($row['name'] ?? 'a visitor');
    $userSubject = 'We received your message to MidWest Medical Hub';
  } else {
    $staffIntro = 'A contact message was updated in the admin panel.';
    $userIntro = 'Your message to MidWest Medical Hub was updated.';
    $staffSubject = 'Contact updated: ' . ($row['name'] ?? '');
    $userSubject = 'Your MidWest Medical Hub message was updated';
  }
  $staffHtml = mmh_email_wrap('Contact message', $staffIntro, $rows);
  $userHtml = mmh_email_wrap('Contact message', $userIntro, $rows, 'Reply to this email if you need to add anything.');
  $staffOk = true;
  $notify = mmh_notify_email();
  if ($notify) {
    $staffOk = mmh_mail($notify, $staffSubject, $staffHtml, $row['email'] ?? '');
  }
  $userOk = mmh_mail($row['email'] ?? '', $userSubject, $userHtml, $notify);
  return $staffOk && $userOk;
}
