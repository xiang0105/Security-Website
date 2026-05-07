<?php
declare(strict_types=1);

function apply_security_headers(): void
{
    header('Content-Type: application/json; charset=utf-8');
    header('X-Content-Type-Options: nosniff');
    header('Referrer-Policy: same-origin');
    header('X-Frame-Options: SAMEORIGIN');
    header('Cache-Control: no-store');
}

function require_post(): void
{
    if (($_SERVER['REQUEST_METHOD'] ?? 'GET') !== 'POST') {
        json_response(['success' => false, 'message' => 'Only POST requests are allowed.'], 405);
    }
}

function request_data(): array
{
    $contentType = $_SERVER['CONTENT_TYPE'] ?? '';

    if (stripos($contentType, 'application/json') !== false) {
        $rawBody = file_get_contents('php://input');
        $data = json_decode($rawBody ?: '', true);

        return is_array($data) ? $data : [];
    }

    return $_POST;
}

function clean_text($value, int $maxLength): string
{
    $text = trim((string) $value);
    $text = preg_replace('/[\x00-\x1F\x7F]/u', '', $text) ?? '';

    if (function_exists('mb_substr')) {
        return mb_substr($text, 0, $maxLength, 'UTF-8');
    }

    return substr($text, 0, $maxLength);
}

function valid_account(string $account): bool
{
    return (bool) preg_match('/^[A-Za-z0-9_.-]{4,32}$/', $account);
}

function valid_password(string $password): bool
{
    return strlen($password) >= 8 && strlen($password) <= 72;
}

function json_response(array $payload, int $status = 200): void
{
    http_response_code($status);
    echo json_encode($payload, JSON_UNESCAPED_UNICODE);
    exit;
}
