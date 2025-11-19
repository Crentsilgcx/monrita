<?php

namespace App\Utils;

class Response
{
    public static function json(array $data, int $status = 200, ?int $cacheTtl = null, array $headers = []): void
    {
        $body = json_encode($data);
        $etag = '"' . hash('sha256', $body) . '"';
        $cacheHeader = $cacheTtl !== null && $cacheTtl > 0 ? 'public, max-age=' . $cacheTtl : 'no-store';
        $executionMs = defined('APP_START') ? (int) ((microtime(true) - APP_START) * 1000) : null;
        $clientTag = trim($_SERVER['HTTP_IF_NONE_MATCH'] ?? '');

        if ($clientTag && $clientTag === $etag && $status === 200) {
            http_response_code(304);
            header('Content-Type: application/json');
            if ($executionMs !== null) {
                header('X-Response-Time: ' . $executionMs . 'ms');
            }
            header('Cache-Control: ' . $cacheHeader);
            header('ETag: ' . $etag);
            foreach ($headers as $key => $value) {
                header($key . ': ' . $value);
            }
            return;
        }

        http_response_code($status);
        header('Content-Type: application/json');
        if ($executionMs !== null) {
            header('X-Response-Time: ' . $executionMs . 'ms');
        }
        header('Cache-Control: ' . $cacheHeader);
        header('ETag: ' . $etag);
        foreach ($headers as $key => $value) {
            header($key . ': ' . $value);
        }
        header('Content-Length: ' . strlen($body));
        echo $body;
    }
}
