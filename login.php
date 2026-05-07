<?php
declare(strict_types=1);

require_once __DIR__ . '/api.php';
require_once __DIR__ . '/auth.php';

apply_security_headers();
require_post();

$data = request_data();
$account = clean_text($data['account'] ?? '', use_supabase() ? 254 : 32);
$password = (string) ($data['password'] ?? '');

if (!account_is_valid_for_current_database($account) || $password === '' || strlen($password) > 72) {
    json_response(['success' => false, 'message' => '帳號或密碼錯誤。'], 401);
}

try {
    $result = login_user($account, $password);
    json_response($result, $result['status'] ?? 200);
} catch (Throwable $e) {
    error_log($e->getMessage());
    json_response(['success' => false, 'message' => '伺服器暫時無法登入，請稍後再試。'], 500);
}
