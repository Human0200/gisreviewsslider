<?php if (!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED !== true) die();

$arComponentParameters = array(

    "GROUPS" => array(
        "SETTINGS" => array(
            "NAME" => GetMessage("LEADSPACE_SETTINGS"),
        ),
        "PARAMS" => array(
            "NAME" => GetMessage("LEADSPACE_PARAMETRS"),
        ),
    ),
    "PARAMETERS" => array(

        "PAGER_COUNT" => array(
            "PARENT" => "BASE",
            "NAME" => GetMessage("LEADSPACE_COUNT_REVIEWS"),
            "TYPE" => "STRING",
            "DEFAULT" => 10,
        ),
        "NEWS_SORT" => array(
            "PARENT" => "BASE",
            "NAME" => GetMessage("LEADSPACE_SORT"),
            "TYPE" => "LIST",
            "VALUES" => [
                "DEFAULT" => GetMessage("LEADSPACE_SORT_DEFAULT"),
                "BY_DATE" => GetMessage("LEADSPACE_SORT_NEW")
            ],
        ),
        "NO_JQUERY" => array(
            "PARENT" => "BASE",
            "NAME" => GetMessage("LEADSPACE_NO_JQUERY"),
            "TYPE" => "CHECKBOX",
            "DEFAULT" => "",
        ),
        "SLIDE_DESCKTOP_COUNT" => array(
            "PARENT" => "VISUAL",
            "NAME" => GetMessage("LEADSPACE_COUNT_DESCKTOP"),
            "TYPE" => "STRING",
            "DEFAULT" => 2,
        ),
        "SLIDE_MOBILE_COUNT" => array(
            "PARENT" => "VISUAL",
            "NAME" => GetMessage("LEADSPACE_COUNT_MOBILE"),
            "TYPE" => "STRING",
            "DEFAULT" => 1,
        ),
        "SHOW_COUNT" => array(
            "PARENT" => "VISUAL",
            "NAME" => GetMessage("LEADSPACE_SHOW_COUNT"),
            "TYPE" => "LIST",
            "VALUES" => [
                "DEFAULT" => GetMessage("LEADSPACE_NOT_SHOW"),
                "SHOW_COUNT_REVIEWS" => GetMessage("LEADSPACE_SHOW_COUNT_REVIEWS"),
                "SHOW_COUNT_MARKS" => GetMessage("LEADSPACE_SHOW_COUNT_MARKS")
            ],
        ),

        "COLOR_BUTTONS" => array(
            "PARENT" => "VISUAL",
            "NAME" => GetMessage("LEADSPACE_COLOR_BUTTONS"),
            "TYPE" => "COLORPICKER",
            "DEFAULT" => "#007aff",
        ),
        "COLOR_BUTTON" => array(
            "PARENT" => "VISUAL",
            "NAME" => GetMessage("LEADSPACE_COLOR_BUTTON"),
            "TYPE" => "COLORPICKER",
            "DEFAULT" => "#ffffff",
        ),
        "COLOR_BUTTON_TEXT" => array(
            "PARENT" => "VISUAL",
            "NAME" => GetMessage("LEADSPACE_COLOR_BUTTON_TEXT"),
            "TYPE" => "COLORPICKER",
            "DEFAULT" => "#212121",
        ),
        "AUTOPLAY" => array(
            "PARENT" => "BASE",
            "NAME" => GetMessage("LEADSPACE_GISREVIEWSSLIDER_VKLUCITQ_AVTOMATICES"),
            "TYPE" => "LIST",
            "VALUES" => [
                "N" => GetMessage("LEADSPACE_GISREVIEWSSLIDER_NE_VKLUCATQ"),
                "Y" => GetMessage("LEADSPACE_GISREVIEWSSLIDER_VKLUCITQ"),
            ],
        ),
        "AUTOPLAY_SPEED" => array(
            "PARENT" => "BASE",
            "NAME" => GetMessage("LEADSPACE_GISREVIEWSSLIDER_SKOROSTQ_AVTOMATICES"),
            "TYPE" => "STRING",
            "DEFAULT" => 2000,
        ),
        "GIS_BUTTON" => array(
            "PARENT" => "VISUAL",
            "NAME" => GetMessage("LEADSPACE_GIS_BUTTON"),
            "TYPE" => "STRING",
            "DEFAULT" => GetMessage("LEADSPACE_GIS_BUTTON_TEXT"),
        ),
        "CACHE_TIME" => array(),

    ),
);