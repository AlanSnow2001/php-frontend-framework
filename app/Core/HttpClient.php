<?php

declare(strict_types=1);

namespace App\Core;

use RuntimeException;

class HttpClient
{
    /**
     * Realiza una petición GET.
     */
    public static function get(string $url, array $headers = []): string
    {
        return self::request('GET', $url, null, $headers);
    }

    /**
     * Realiza una petición POST.
     */
    public static function post(string $url, array|string $data, array $headers = []): string
    {
        return self::request('POST', $url, $data, $headers);
    }

    /**
     * Realiza una petición PUT.
     */
    public static function put(string $url, array|string $data, array $headers = []): string
    {
        return self::request('PUT', $url, $data, $headers);
    }

    /**
     * Realiza una petición DELETE.
     */
    public static function delete(string $url, array $headers = []): string
    {
        return self::request('DELETE', $url, null, $headers);
    }

    /**
     * Método base para ejecutar peticiones cURL.
     */
    private static function request(string $method, string $url, array|string|null $data = null, array $headers = []): string
    {
        $ch = curl_init();

        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_CUSTOMREQUEST, $method);

        // Si enviamos datos (POST o PUT)
        if ($data !== null) {
            $postFields = is_array($data) ? json_encode($data) : $data;
            curl_setopt($ch, CURLOPT_POSTFIELDS, $postFields);
            
            // Si no se incluyó un Content-Type y pasamos un array, asumimos JSON
            $hasContentType = false;
            foreach ($headers as $header) {
                if (stripos($header, 'Content-Type') !== false) {
                    $hasContentType = true;
                    break;
                }
            }
            if (!$hasContentType && is_array($data)) {
                $headers[] = 'Content-Type: application/json';
            }
        }

        if (!empty($headers)) {
            curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
        }

        $response = curl_exec($ch);

        if (curl_errno($ch)) {
            $error = curl_error($ch);
            curl_close($ch);
            throw new RuntimeException("cURL Error: $error");
        }

        curl_close($ch);

        return $response;
    }
}
