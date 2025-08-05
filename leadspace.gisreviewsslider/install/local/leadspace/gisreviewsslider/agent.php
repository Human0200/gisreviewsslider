<?php
require($_SERVER["DOCUMENT_ROOT"]."/bitrix/modules/main/include/prolog_before.php");
 define("LOG_FILENAME", $_SERVER["DOCUMENT_ROOT"]."/log.txt");
use \Bitrix\Main\Localization\Loc;
use \Bitrix\Main\Loader;
use \Bitrix\Main\Entity\Base;
use \Bitrix\Main\Application;
use \Bitrix\Main\ModuleManager;
use \Bitrix\Main\EventManager;
use \Bitrix\Main\Config\Option;
use \Bitrix\Main\Type;
require_once $_SERVER["DOCUMENT_ROOT"] . "/bitrix/modules/leadspace.gisreviewsslider/lib/function.php";
require_once $_SERVER["DOCUMENT_ROOT"] . "/bitrix/modules/leadspace.gisreviewsslider/lib/reviews.php";
require_once $_SERVER["DOCUMENT_ROOT"] . "/bitrix/modules/leadspace.gisreviewsslider/lib/phpQuery-onefile.php";

$connection = Bitrix\Main\Application::getConnection();
$idCompany = Option::get("leadspace.gisreviewsslider", "company_id");

sendPostRequestUsingCurl("http://217.114.4.16/seleniumParser/startSession.php", []);
 $source = sendPostRequestUsingCurl("http://217.114.4.16/seleniumParser/getSiteHTML.php", ["PAGE_URL" => $idCompany]);
        $doc = phpQuery::newDocument($source);


//$doc = phpQuery::newDocument(file_get_contents($idCompany));

$company = array();
$company["name"] = $doc->find('h1')->text();
$company["rating"] = floatval($doc->find('._y10azs')->text());
$company["countreviews"] = $doc->find('._14lj3n7:eq(0)')->text();
$company["countmarks"] = (int)$doc->find('._jspzdm')->text();
$company["stars_full"] = floor($company["rating"]);
$company["stars_half"] = ceil($company["rating"] - $company["stars_full"]);
$company["stars_empty"] = 5 - $company["stars_full"] - $company["stars_half"];


global $DB;

if($company["name"] != ""){
$connection->truncateTable("leadspace_gisreviews_company");
$connection->truncateTable("leadspace_gisreviewsslider");

$DB->PrepareFields("leadspace_gisreviews_company");
$arFields = array(

    "NAME"           => "'".$company["name"]."'",
    "RATING"         => "'".$company["rating"]."'",
    "COUNTREVIEWS" 	 => "'".$company["countreviews"]."'",
    "COUNTMARKS" 	 => "'".$company["countmarks"]."'",
    "FULL_STARS"    => "'".$company["stars_full"]."'",
    "HALF_STARS"    => "'".$company["stars_half"]."'",
    "EMPTY_STARS"    => "'".$company["stars_empty"]."'",
);
//echo "<pre>";print_r($arFields);echo "<pre>";
$ID = $DB->Insert("leadspace_gisreviews_company", $arFields, $err_mess.__LINE__);
$result = array();


$reviews = $doc->find('script:contains("var __customcfg =")')->text();

$reviews1 = explode("JSON.parse", $reviews);




$data = substr($reviews1[2], 2);
$data = substr($data, 0, -41);
$data = str_replace('\\\\', '\\', $data);
$art = json_decode($data, true);

foreach ($art["data"]["review"] as $key => $value) {
    $timestamp = strtotime($value["data"]["date_created"]);


    $DB->PrepareFields("leadspace_gisreviewsslider");
    $arFields = array(

        "NAME"           => "'".$value['data']['user']['name']."'",
        "IMAGE"    	 => "'".$value['data']['user']['photo_preview_urls']['64x64']."'",
        "RATING"         => "'".$value['data']['rating']."'",
        "TIME" 	  	 => "'".$timestamp."'",
        "DATA" 	  	 => "'".$timestamp."'",
        "DESCRIPTION"    => "'".$value['data']['text']."'",
    );

    $ID = $DB->Insert("leadspace_gisreviewsslider", $arFields, $err_mess.__LINE__);
}
}
sendPostRequestUsingCurl("http://217.114.4.16/seleniumParser/closeSession.php", []);

?>
