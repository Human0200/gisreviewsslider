<?php

function fetchUrlUsingCurl($url, $headers = []) {
    $ch = curl_init($url);

    $defaultHeaders = [
        'Accept: text/html,application/xhtml+xml,application/xml;q=0.9,*/*;q=0.8',
        'Accept-Language: ru-RU,ru;q=0.9,en;q=0.8',
    ];
    if (!empty($headers) && is_array($headers)) {
        $defaultHeaders = array_values(array_merge($defaultHeaders, $headers));
    }

    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true);
    curl_setopt($ch, CURLOPT_MAXREDIRS, 10);
    curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 10);
    curl_setopt($ch, CURLOPT_TIMEOUT, 60);
    curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, true);
    curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, 2);
    curl_setopt($ch, CURLOPT_USERAGENT, 'Mozilla/5.0 (compatible; LeadspaceGisReviews/1.0; +https://leadspace.ru)');
    curl_setopt($ch, CURLOPT_HTTPHEADER, $defaultHeaders);

    $response = curl_exec($ch);

    if (curl_errno($ch)) {
        $error_msg = curl_error($ch);
        curl_close($ch);
        return "Ошибка cURL: " . $error_msg;
    }

    $httpCode = (int)curl_getinfo($ch, CURLINFO_HTTP_CODE);
    $finalUrl = (string)curl_getinfo($ch, CURLINFO_EFFECTIVE_URL);
    curl_close($ch);

    if ($httpCode < 200 || $httpCode >= 400) {
        return "Ошибка HTTP: " . $httpCode;
    }

    return [
        'body' => $response,
        'final_url' => $finalUrl,
    ];
}
