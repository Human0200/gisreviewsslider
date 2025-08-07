<?php
require($_SERVER["DOCUMENT_ROOT"] . "/bitrix/modules/main/include/prolog_before.php");
define("LOG_FILENAME", $_SERVER["DOCUMENT_ROOT"] . "/log1.txt");

use \Bitrix\Main\Loader;
use \Bitrix\Main\Config\Option;
use \Bitrix\Main\Application;

Loader::includeModule('leadspace.gisreviewsslider');

require_once $_SERVER["DOCUMENT_ROOT"] . "/bitrix/modules/leadspace.gisreviewsslider/lib/function.php";
require_once $_SERVER["DOCUMENT_ROOT"] . "/bitrix/modules/leadspace.gisreviewsslider/lib/reviews.php";
require_once $_SERVER["DOCUMENT_ROOT"] . "/bitrix/modules/leadspace.gisreviewsslider/lib/phpQuery-onefile.php";


$idCompany = Option::get("leadspace.gisreviewsslider", "company_id");
if (empty($idCompany)) {
    die("Не указан ID компании в настройках модуля");
}

$startSession = sendPostRequestUsingCurl("http://217.114.4.16/seleniumParser/handler.php", ["ACTION" => "START"]);


$response = sendPostRequestUsingCurl("http://217.114.4.16/seleniumParser/handler.php", [
    "ACTION" => "PARSE",
    "PAGE_URL" => $idCompany,
    "SCROLL" => true
]);


$data = json_decode($response, true);
if (json_last_error() === JSON_ERROR_NONE) {
    if (isset($data['status']) && $data['status'] === 'error') {
        die("Error from Selenium: " . $data['message']);
    }
    $source = $data['html'] ?? '';
} else {
    $source = $response;
}

if ($source === 'Error: Tried to run command without establishing a connection') {
    echo "Произошла ошибка, попробуйте ещё раз";
    die();
}

$doc = phpQuery::newDocument($source);
file_put_contents(__DIR__ . '/page_html.html', $source);

// 2. Парсим основную информацию о компании
$company = [
    "name" => trim($doc->find('h1')->text()),
    "rating" => floatval($doc->find('._y10azs')->text()),
    "countreviews" => trim($doc->find('._14lj3n7:eq(0)')->text()),
    "countmarks" => (int)str_replace([' ', 'оценки', 'оценок', 'оценка'], '', $doc->find('._jspzdm')->text()),
];


$company["stars_full"] = floor($company["rating"]);
$company["stars_half"] = ceil($company["rating"] - $company["stars_full"]);
$company["stars_empty"] = 5 - $company["stars_full"] - $company["stars_half"];

// 3. Сохраняем данные компании в БД
$connection = Application::getConnection();
global $DB;

$output = "<h2>Результат обновления данных</h2>";

try {

    $connection->truncateTable("leadspace_gisreviews_company");
    $connection->truncateTable("leadspace_gisreviewsslider");

    // Сохраняем информацию о компании
    $DB->PrepareFields("leadspace_gisreviews_company");
    $arFields = [
        "NAME" => "'" . $DB->ForSql($company["name"]) . "'",
        "RATING" => "'" . $company["rating"] . "'",
        "COUNTREVIEWS" => "'" . $DB->ForSql($company["countreviews"]) . "'",
        "COUNTMARKS" => "'" . $company["countmarks"] . "'",
        "FULL_STARS" => "'" . $company["stars_full"] . "'",
        "HALF_STARS" => "'" . $company["stars_half"] . "'",
        "EMPTY_STARS" => "'" . $company["stars_empty"] . "'",
    ];

    $companyId = $DB->Insert("leadspace_gisreviews_company", $arFields);

    $output .= "<div style='margin: 15px 0; color: green;'>Данные компании успешно сохранены</div>";
    $output .= "<div style='margin-bottom: 15px;'>";
    $output .= "<div><strong>Название компании:</strong> " . htmlspecialchars($company["name"]) . "</div>";
    $output .= "<div><strong>Рейтинг:</strong> " . $company["rating"] . "</div>";
    $output .= "<div><strong>Количество оценок:</strong> " . $company["countmarks"] . "</div>";
    $output .= "</div>";

    // 4. Парсим и сохраняем отзывы
    $output .= "<h3>Обработка отзывов:</h3>";

    $reviews = $doc->find('._1k5soqfl');
    $savedCount = 0;

    foreach ($reviews as $review) {
        $review = pq($review);
        try {
            // Парсим данные отзыва
            $userName = trim($review->find('._16s5yj36')->text());
            $userPhoto = '';
            $img = $review->find('._10bkgj3 img');
            if ($img->length) {
                $src = $img->attr('src');
                if ($src) {

                    $userPhoto = html_entity_decode($src, ENT_QUOTES, 'UTF-8');
                    $userPhoto = trim($userPhoto, " '\"");
                }
            }
            $rating = $review->find('._1fkin5c span')->length;
            $text = trim($review->find('._1wlx08h, ._1msln3t')->text());
            $dateText = trim($review->find('._1evjsdb')->text());
            $dateText = trim(substr($dateText, 0, strpos($dateText, ',')));

            $dateString = str_replace(
                ['января', 'февраля', 'марта', 'апреля', 'мая', 'июня', 'июля', 'августа', 'сентября', 'октября', 'ноября', 'декабря'],
                ['January', 'February', 'March', 'April', 'May', 'June', 'July', 'August', 'September', 'October', 'November', 'December'],
                $dateText
            );

            $date = new DateTime($dateString);
            file_put_contents(__DIR__ . '/date.txt', $date->getTimestamp());

            $timestamp = $date->getTimestamp();

            if (!$timestamp) {
                $timestamp = time();
            }

            // Сохраняем отзыв в БД
            $DB->PrepareFields("leadspace_gisreviewsslider");
            $arFields = [
                "NAME" => "'" . $DB->ForSql($userName) . "'",
                "IMAGE" => "'" . $DB->ForSql($userPhoto) . "'",
                "RATING" => "'" . $rating . "'",
                "TIME" => "'" . $timestamp . "'",
                "DATA" => "'" . $timestamp . "'",
                "DESCRIPTION" => "'" . $DB->ForSql($text) . "'",
            ];

            $reviewId = $DB->Insert("leadspace_gisreviewsslider", $arFields);
            $savedCount++;

            // $output .= "<p>Сохранен отзыв от $userName (ID: $reviewId)</p>";
        } catch (Exception $e) {
            $output .= "<p style='color:orange'>Ошибка при сохранении отзыва: " . $e->getMessage() . "</p>";
        }
    }

    $output .= "<p style='color:green'>Всего сохранено отзывов: $savedCount</p>";
} catch (Exception $e) {
    $output .= "<p style='color:red'>Ошибка: " . $e->getMessage() . "</p>";
    file_put_contents(__DIR__ . '/error_log.txt', date('Y-m-d H:i:s') . ' - ' . $e->getMessage() . PHP_EOL, FILE_APPEND);
}


echo $output;

// Закрываем сессию Selenium
//sendPostRequestUsingCurl("http://217.114.4.16/seleniumParser/closeSession.php", []);
