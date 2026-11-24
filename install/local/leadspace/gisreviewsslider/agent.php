<?php
require($_SERVER["DOCUMENT_ROOT"] . "/bitrix/modules/main/include/prolog_before.php");

use Bitrix\Main\Loader;

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405);
    die('Method Not Allowed');
}

if (!check_bitrix_sessid()) {
    http_response_code(403);
    die('Forbidden');
}

global $USER;
if (!is_object($USER) || !$USER->IsAuthorized() || !$USER->IsAdmin()) {
    http_response_code(403);
    die('Forbidden');
}

Loader::includeModule('leadspace.gisreviewsslider');

echo \leadspace\gisreviewsslider\Agent::runImport(true);
