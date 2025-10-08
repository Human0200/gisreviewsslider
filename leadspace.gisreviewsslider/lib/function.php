<?php

function sendPostRequestUsingCurl($url, $params = []) {
    // Инициализируем cURL-сессию
    $ch = curl_init($url);

    // Устанавливаем параметры cURL для POST-запроса
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);  // Получать ответ в виде строки
    curl_setopt($ch, CURLOPT_POST, true);  // Устанавливаем метод POST
    curl_setopt($ch, CURLOPT_POSTFIELDS, http_build_query($params));  // Отправляем параметры

    // Устанавливаем таймауты
    curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 10);  // Таймаут для подключения
    curl_setopt($ch, CURLOPT_TIMEOUT, 120);  // Таймаут для выполнения запроса

    // Отправляем запрос
    $response = curl_exec($ch);

    // Проверяем на ошибки
    if (curl_errno($ch)) {
        $error_msg = curl_error($ch);
        curl_close($ch);
        return "Ошибка cURL: " . $error_msg;
    }

    // Закрываем cURL-сессию
    curl_close($ch);

    return $response;
}
function debugPrint($variable) {
    echo '<pre>';
    print_r($variable);
    echo '</pre>';
}