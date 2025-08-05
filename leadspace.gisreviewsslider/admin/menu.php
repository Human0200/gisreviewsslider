<?php
if($APPLICATION->GetGroupRight("leadspace.gisreviewsslider")>"D") {

    $aMenu = array(
        "parent_menu" => "global_menu_services",
        "sort"        => 100,
        "url"         => "/bitrix/admin/settings.php?lang=ru&mid=leadspace.gisreviewsslider&mid_menu=1&lang=".LANGUAGE_ID,
        "more_url"    => "",
        "text"        => GetMessage("LEADSPACE_GISREVIEWSSLIDER_MODULE_NAME"),
        "title"       => GetMessage("LEADSPACE_GISREVIEWSSLIDER_MODULE_NAME"),
        "icon"        => "form_menu_icon",
        "page_icon"   => "form_page_icon",
        "module_id"   => "leadspace.gisreviewsslider",
        "dynamic"     => false,
        "items_id"    => "leadspace.gisreviewsslider",
        "items"       => array(),
    );
    return $aMenu;
}
return false;
?>