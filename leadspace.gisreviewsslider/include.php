<?php
use Bitrix\Main\Loader;
use Bitrix\Main\ModuleManager;


if (ModuleManager::isModuleInstalled('leadspace.gisreviewsslider') && 
    Loader::IncludeModule('leadspace.gisreviewsslider')) {
    
    
    Loader::registerAutoLoadClasses('leadspace.gisreviewsslider', [
        "leadspace\gisreviewsslider\GisReviewsTable" => "lib/reviews.php",
    ]);
}
?>
