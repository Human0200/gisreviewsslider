<?php

namespace leadspace\gisreviewsslider;

use \Bitrix\Main\Loader;
use \Bitrix\Main\Application;
use \Bitrix\Main\Config\Option;

class Agent {

    // Public web key used by 2GIS site itself for reviews API.
    private const REVIEWS_API_KEY = 'd463a78e-4d44-4198-8cd5-7fc0762032cb';
    private const REVIEWS_API_BASE = 'https://api.reviews.2gis.com/2.0/branches/';

    public static function superGisAgent() {
        self::runImport(false);
        return "\leadspace\gisreviewsslider\Agent::superGisAgent();";
    }

    public static function runImport($withOutput = true) {
        Loader::includeModule('leadspace.gisreviewsslider');

        require_once self::getModuleFilePath('lib/function.php');
        require_once self::getModuleFilePath('lib/reviews.php');
        require_once self::getModuleFilePath('lib/phpQuery-onefile.php');

        $idCompany = Option::get("leadspace.gisreviewsslider", "company_id");
        if (empty($idCompany)) {
            return $withOutput ? "Не указан ID компании в настройках модуля" : "";
        }

        $page = self::fetchCompanyPage($idCompany);
        if ($page['error'] !== '') {
            $msg = "Не удалось получить страницу компании 2ГИС. " . htmlspecialchars($page['error']);
            return $withOutput ? "<p style='color:red'>{$msg}</p>" : "";
        }

        $doc = \phpQuery::newDocument($page['html']);
        $company = self::parseCompany($doc);
        $firmId = $page['firm_id'];

        $reviewsData = [];
        if ($firmId !== '') {
            $reviewsData = self::fetchReviewsFromApi($firmId);
        }

        // Fallback to HTML only if API failed.
        if (empty($reviewsData)) {
            $reviewsData = self::parseReviewsFromHtml($doc);
        }

        if ($company['name'] === '' && empty($reviewsData)) {
            $msg = "Не найдены данные компании и отзывы. База не изменена.";
            return $withOutput ? "<p style='color:red'>" . htmlspecialchars($msg) . "</p>" : "";
        }

        $connection = Application::getConnection();
        global $DB;

        $output = "<h2>Результат обновления данных</h2>";

        try {
            $connection->truncateTable("leadspace_gisreviews_company");
            $connection->truncateTable("leadspace_gisreviewsslider");

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

            $DB->Insert("leadspace_gisreviews_company", $arFields);

            $output .= "<div style='margin: 15px 0; color: green;'>Данные компании успешно сохранены</div>";
            $output .= "<div style='margin-bottom: 15px;'>";
            $output .= "<div><strong>Название компании:</strong> " . htmlspecialchars($company["name"]) . "</div>";
            $output .= "<div><strong>Рейтинг:</strong> " . $company["rating"] . "</div>";
            $output .= "<div><strong>Количество оценок:</strong> " . $company["countmarks"] . "</div>";
            $output .= "</div>";
            $output .= "<h3>Обработка отзывов:</h3>";

            $savedCount = 0;
            foreach ($reviewsData as $item) {
                try {
                    $DB->PrepareFields("leadspace_gisreviewsslider");
                    $arFields = [
                        "NAME" => "'" . $DB->ForSql($item["name"]) . "'",
                        "IMAGE" => "'" . $DB->ForSql($item["image"]) . "'",
                        "RATING" => "'" . $item["rating"] . "'",
                        "TIME" => "'" . $item["timestamp"] . "'",
                        "DATA" => "'" . $item["timestamp"] . "'",
                        "DESCRIPTION" => "'" . $DB->ForSql($item["text"]) . "'",
                    ];
                    $DB->Insert("leadspace_gisreviewsslider", $arFields);
                    $savedCount++;
                } catch (\Exception $e) {
                    $output .= "<p style='color:red'>Ошибка при сохранении отзыва: " . htmlspecialchars($e->getMessage()) . "</p>";
                }
            }

            $output .= "<p style='color:green'>Всего сохранено отзывов: $savedCount</p>";
        } catch (\Exception $e) {
            $output .= "<p style='color:red'>Ошибка: " . htmlspecialchars($e->getMessage()) . "</p>";
            AddMessage2Log(
                date('Y-m-d H:i:s') . ' - ' . $e->getMessage(),
                'leadspace.gisreviewsslider'
            );
        }

        return $withOutput ? $output : "";
    }

    private static function fetchCompanyPage($companyUrl) {
        $companyUrl = trim((string)$companyUrl);
        $result = [
            'html' => '',
            'firm_id' => '',
            'final_url' => '',
            'error' => '',
        ];

        if ($companyUrl === '') {
            $result['error'] = 'Пустая ссылка на компанию';
            return $result;
        }

        $direct = fetchUrlUsingCurl($companyUrl);
        if (!is_array($direct) || empty($direct['body'])) {
            $result['error'] = is_string($direct) ? $direct : 'Пустой ответ';
            return $result;
        }

        $finalUrl = $direct['final_url'] ?: $companyUrl;
        $result['final_url'] = $finalUrl;
        $result['firm_id'] = self::extractFirmId($finalUrl);
        $result['html'] = $direct['body'];

        $reviewsUrl = self::toReviewsTabUrl($finalUrl);
        if ($reviewsUrl !== $finalUrl) {
            $directReviews = fetchUrlUsingCurl($reviewsUrl);
            if (is_array($directReviews) && !empty($directReviews['body'])) {
                $result['html'] = $directReviews['body'];
                $result['final_url'] = $directReviews['final_url'] ?: $reviewsUrl;
                if ($result['firm_id'] === '') {
                    $result['firm_id'] = self::extractFirmId($result['final_url']);
                }
            }
        }

        if ($result['firm_id'] === '') {
            $result['firm_id'] = self::extractFirmId($companyUrl);
        }

        return $result;
    }

    private static function fetchReviewsFromApi($firmId) {
        $firmId = preg_replace('/\D+/', '', (string)$firmId);
        if ($firmId === '') {
            return [];
        }

        $url = self::REVIEWS_API_BASE . rawurlencode($firmId)
            . '/reviews?limit=50&sort_by=date_created&key=' . rawurlencode(self::REVIEWS_API_KEY);

        $response = fetchUrlUsingCurl($url, ['Accept: application/json']);
        if (!is_array($response) || empty($response['body'])) {
            return [];
        }

        $data = json_decode($response['body'], true);
        if (!is_array($data) || empty($data['reviews']) || !is_array($data['reviews'])) {
            return [];
        }

        $result = [];
        foreach ($data['reviews'] as $review) {
            if (!is_array($review)) {
                continue;
            }

            $user = is_array($review['user'] ?? null) ? $review['user'] : [];
            $name = trim((string)($user['name'] ?? ($user['first_name'] ?? '')));
            $text = trim((string)($review['text'] ?? ''));
            $rating = (int)($review['rating'] ?? 0);

            $photos = is_array($user['photo_preview_urls'] ?? null) ? $user['photo_preview_urls'] : [];
            $image = '';
            foreach (['64x64', 'url', '320x', '640x'] as $key) {
                if (!empty($photos[$key]) && is_string($photos[$key])) {
                    $image = $photos[$key];
                    break;
                }
            }

            $dateCreated = (string)($review['date_created'] ?? '');
            $timestamp = self::parseIsoDate($dateCreated);

            if ($name === '' && $text === '' && $rating === 0) {
                continue;
            }

            $result[] = [
                'name' => $name,
                'image' => $image,
                'rating' => $rating,
                'text' => $text,
                'timestamp' => $timestamp,
            ];
        }

        return $result;
    }

    private static function extractFirmId($url) {
        if (preg_match('#/firm/(\d+)#', (string)$url, $m)) {
            return $m[1];
        }
        return '';
    }

    private static function toReviewsTabUrl($url) {
        if (preg_match('#(https?://[^?\s]+/firm/\d+)#i', $url, $m)) {
            if (stripos($url, '/tab/reviews') === false) {
                return $m[1] . '/tab/reviews';
            }
        }
        return $url;
    }

    private static function parseCompany($doc) {
        $name = trim($doc->find('h1')->text());
        $name = trim(preg_replace('/\s+/u', ' ', $name));

        $ratingText = trim($doc->find('._y10azs')->text());
        $rating = (float)str_replace(',', '.', $ratingText);

        $countreviews = trim($doc->find('._14lj3n7:eq(0)')->text());
        if ($countreviews === '') {
            $countreviews = trim($doc->find('._46hyo9:eq(0)')->text());
        }

        $countmarksRaw = trim($doc->find('._jspzdm')->text());
        $countmarks = (int)preg_replace('/\D+/', '', $countmarksRaw);

        $starsFull = (int)floor($rating);
        $starsHalf = (int)ceil($rating - $starsFull);
        if ($starsFull + $starsHalf > 5) {
            $starsHalf = 5 - $starsFull;
        }
        $starsEmpty = 5 - $starsFull - $starsHalf;

        return [
            "name" => $name,
            "rating" => $rating,
            "countreviews" => $countreviews,
            "countmarks" => $countmarks,
            "stars_full" => $starsFull,
            "stars_half" => $starsHalf,
            "stars_empty" => $starsEmpty,
        ];
    }

    private static function parseReviewsFromHtml($doc) {
        $reviews = $doc->find('._1rowqpjv');
        $result = [];

        foreach ($reviews as $review) {
            $review = pq($review);

            $userName = trim($review->find('._19h0cqe')->text());
            $userPhoto = '';
            $photoBlock = $review->find('._1x9sazk [style*="background-image"]')->eq(0);
            if ($photoBlock->length) {
                $style = (string)$photoBlock->attr('style');
                if (preg_match('/background-image:\s*url\((["\']?)(.*?)\1\)/i', $style, $matches)) {
                    $userPhoto = html_entity_decode($matches[2], ENT_QUOTES, 'UTF-8');
                    $userPhoto = trim($userPhoto, " '\"");
                }
            }

            $rating = $review->find('._1fkin5c span')->length;
            $text = trim($review->find('._83kmcy a')->eq(0)->text());
            if ($text === '') {
                $text = trim($review->find('._co8kyiw')->eq(0)->text());
            }

            // Dates are often absent in SSR HTML; keep parser for rare cases.
            $dateText = trim($review->find('._10c0hgu')->eq(0)->text());
            if ($dateText === '') {
                $dateText = trim($review->find('._1evjsdb')->eq(0)->text());
            }
            $dateText = preg_replace('/\s*[,\-].*$/u', '', $dateText);
            $timestamp = self::parseReviewDate($dateText);

            if ($userName === '' && $text === '' && $rating === 0) {
                continue;
            }

            $result[] = [
                'name' => $userName,
                'image' => $userPhoto,
                'rating' => $rating,
                'text' => $text,
                'timestamp' => $timestamp,
            ];
        }

        return $result;
    }

    private static function parseIsoDate($dateText) {
        $dateText = trim((string)$dateText);
        if ($dateText === '') {
            return time();
        }

        try {
            $date = new \DateTime($dateText);
            return $date->getTimestamp();
        } catch (\Exception $e) {
            $timestamp = strtotime($dateText);
            return $timestamp === false ? time() : $timestamp;
        }
    }

    private static function parseReviewDate($dateText) {
        $dateText = trim((string)$dateText);
        if ($dateText === '') {
            return time();
        }

        $dateString = str_replace(
            ['января', 'февраля', 'марта', 'апреля', 'мая', 'июня', 'июля', 'августа', 'сентября', 'октября', 'ноября', 'декабря'],
            ['January', 'February', 'March', 'April', 'May', 'June', 'July', 'August', 'September', 'October', 'November', 'December'],
            $dateText
        );

        $date = \DateTime::createFromFormat('d F Y', $dateString);
        if ($date) {
            return $date->getTimestamp();
        }

        $timestamp = strtotime($dateString);
        return $timestamp === false ? time() : $timestamp;
    }

    private static function getModuleFilePath($relativePath) {
        $localPath = $_SERVER["DOCUMENT_ROOT"] . "/local/modules/leadspace.gisreviewsslider/" . $relativePath;
        if (file_exists($localPath)) {
            return $localPath;
        }

        return $_SERVER["DOCUMENT_ROOT"] . "/bitrix/modules/leadspace.gisreviewsslider/" . $relativePath;
    }
}
