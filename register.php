<?php
declare(strict_types=1);

require_once __DIR__ . '/api.php';
require_once __DIR__ . '/auth.php';

apply_security_headers();
require_post();

$data = request_data();
$account = clean_text($data['account'] ?? '', use_supabase() ? 254 : 32);
$password = (string) ($data['password'] ?? '');
$name = clean_text($data['name'] ?? '', 40);

if (!account_is_valid_for_current_database($account)) {
    json_response(['success' => false, 'message' => current_account_rule_message()], 422);
}

if (!valid_password($password)) {
    json_response(['success' => false, 'message' => '密碼需為 8-72 個字元。'], 422);
}

if ($name === '') {
    json_response(['success' => false, 'message' => '請輸入使用者名稱。'], 422);
}

try {
    $result = register_user($account, $password, $name);
    json_response($result, $result['status'] ?? 200);
} catch (Throwable $e) {
    error_log($e->getMessage());
    json_response(['success' => false, 'message' => '伺服器暫時無法註冊，請稍後再試。'], 500);
}
