<?php
use Bitrix\Main\Loader;
use Bitrix\Main\ModuleManager;


if (ModuleManager::isModuleInstalled('leadspace.gisreviewsslider') && 
    Loader::IncludeModule('leadspace.gisreviewsslider')) {

    
    Loader::registerAutoLoadClasses('leadspace.gisreviewsslider', [
        "leadspace\gisreviewsslider\GisReviewsTable" => "lib/reviews.php",
        "leadspace\gisreviewsslider\Event" => "lib/event.php",
        "leadspace\gisreviewsslider\Agent" => "lib/agent.php",
    ]);
}
?>
