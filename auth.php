<?php
declare(strict_types=1);

require_once __DIR__ . '/api.php';
require_once __DIR__ . '/database.php';

function use_supabase(): bool
{
    return supabase_url() !== '' && supabase_key() !== '';
}

function supabase_url(): string
{
    return rtrim((string) (getenv('SUPABASE_URL') ?: ''), '/');
}

function supabase_key(): string
{
    return (string) (
        getenv('SUPABASE_KEY')
        ?: getenv('SUPABASE_ANON_KEY')
        ?: getenv('SUPABASE_PUBLISHABLE_KEY')
        ?: ''
    );
}

function account_is_valid_for_current_database(string $account): bool
{
    if (use_supabase()) {
        return filter_var($account, FILTER_VALIDATE_EMAIL) !== false && strlen($account) <= 254;
    }

    return valid_account($account);
}

function current_account_rule_message(): string
{
    if (use_supabase()) {
        return 'Supabase 模式請使用 Email 作為帳號。';
    }

    return '帳號需為 4-32 個英數字、底線、連字號或小數點。';
}

function login_user(string $account, string $password): array
{
    if (use_supabase()) {
        return login_user_with_supabase($account, $password);
    }

    return login_user_with_xampp($account, $password);
}

function register_user(string $account, string $password, string $name): array
{
    if (use_supabase()) {
        return register_user_with_supabase($account, $password, $name);
    }

    return register_user_with_xampp($account, $password, $name);
}

function login_user_with_xampp(string $account, string $password): array
{
    $pdo = get_pdo();
    $stmt = $pdo->prepare('SELECT name, password FROM `user` WHERE account = :account LIMIT 1');
    $stmt->execute(['account' => $account]);
    $user = $stmt->fetch();

    $passwordHash = $user['password'] ?? '';
    $passwordAlgo = is_string($passwordHash) ? (password_get_info($passwordHash)['algo'] ?? 0) : 0;
    $isModernHash = $passwordAlgo !== 0 && $passwordAlgo !== null;
    $isValidPassword = $user && (
        ($isModernHash && password_verify($password, $passwordHash)) ||
        (!$isModernHash && hash_equals((string) $passwordHash, $password))
    );

    if (!$isValidPassword) {
        return ['success' => false, 'status' => 401, 'message' => '帳號或密碼錯誤。'];
    }

    if (!$isModernHash) {
        $upgrade = $pdo->prepare('UPDATE `user` SET password = :password WHERE account = :account');
        $upgrade->execute([
            'password' => password_hash($password, PASSWORD_DEFAULT),
            'account' => $account,
        ]);
    }

    return [
        'success' => true,
        'status' => 200,
        'name' => clean_text($user['name'] ?? '', 40),
    ];
}

function register_user_with_xampp(string $account, string $password, string $name): array
{
    try {
        $pdo = get_pdo();
        $stmt = $pdo->prepare(
            'INSERT INTO `user` (account, password, name) VALUES (:account, :password, :name)'
        );
        $stmt->execute([
            'account' => $account,
            'password' => password_hash($password, PASSWORD_DEFAULT),
            'name' => $name,
        ]);

        return ['success' => true, 'status' => 201, 'message' => '註冊成功，請使用新帳號登入。'];
    } catch (PDOException $e) {
        if ($e->getCode() === '23000') {
            return ['success' => false, 'status' => 409, 'message' => '此帳號已被使用。'];
        }

        throw $e;
    }
}

function login_user_with_supabase(string $account, string $password): array
{
    $response = supabase_request(
        '/auth/v1/token?grant_type=password',
        [
            'email' => $account,
            'password' => $password,
        ]
    );

    if (!$response['ok']) {
        return ['success' => false, 'status' => 401, 'message' => '帳號或密碼錯誤。'];
    }

    $user = $response['data']['user'] ?? [];
    $metadata = is_array($user['user_metadata'] ?? null) ? $user['user_metadata'] : [];
    $name = clean_text($metadata['name'] ?? $account, 40);

    return [
        'success' => true,
        'status' => 200,
        'name' => $name,
    ];
}

function register_user_with_supabase(string $account, string $password, string $name): array
{
    $response = supabase_request(
        '/auth/v1/signup',
        [
            'email' => $account,
            'password' => $password,
            'data' => [
                'name' => $name,
            ],
        ]
    );

    if (!$response['ok']) {
        $message = supabase_error_message($response['data']);
        $status = $response['status'] === 422 ? 422 : 409;

        return ['success' => false, 'status' => $status, 'message' => $message];
    }

    return ['success' => true, 'status' => 201, 'message' => '註冊成功，請使用新帳號登入。'];
}

function supabase_request(string $path, array $payload): array
{
    $url = supabase_url() . $path;
    $body = json_encode($payload, JSON_UNESCAPED_UNICODE);
    $headers = [
        'Content-Type: application/json',
        'apikey: ' . supabase_key(),
        'Authorization: Bearer ' . supabase_key(),
    ];

    if (function_exists('curl_init')) {
        $ch = curl_init($url);
        curl_setopt_array($ch, [
            CURLOPT_POST => true,
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_HTTPHEADER => $headers,
            CURLOPT_POSTFIELDS => $body,
            CURLOPT_TIMEOUT => 15,
        ]);

        $raw = curl_exec($ch);
        $status = (int) curl_getinfo($ch, CURLINFO_RESPONSE_CODE);

        if ($raw === false) {
            $error = curl_error($ch);
            curl_close($ch);
            throw new RuntimeException($error);
        }

        curl_close($ch);
    } else {
        $context = stream_context_create([
            'http' => [
                'method' => 'POST',
                'header' => implode("\r\n", $headers),
                'content' => $body,
                'ignore_errors' => true,
                'timeout' => 15,
            ],
        ]);
        $raw = file_get_contents($url, false, $context);
        $status = supabase_stream_status($http_response_header ?? []);
    }

    $data = json_decode((string) $raw, true);

    return [
        'ok' => $status >= 200 && $status < 300,
        'status' => $status,
        'data' => is_array($data) ? $data : [],
    ];
}

function supabase_stream_status(array $headers): int
{
    $statusLine = $headers[0] ?? '';

    if (preg_match('/\s(\d{3})\s/', $statusLine, $matches)) {
        return (int) $matches[1];
    }

    return 0;
}

function supabase_error_message(array $data): string
{
    $message = (string) ($data['msg'] ?? $data['message'] ?? $data['error_description'] ?? '');

    if ($message === '') {
        return 'Supabase 註冊失敗，請確認帳號或稍後再試。';
    }

    if (stripos($message, 'already') !== false || stripos($message, 'registered') !== false) {
        return '此 Email 已被使用。';
    }

    if (stripos($message, 'password') !== false) {
        return '密碼不符合 Supabase 專案規則。';
    }

    return 'Supabase 註冊失敗，請確認帳號或稍後再試。';
}
