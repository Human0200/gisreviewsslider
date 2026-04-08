<?php

namespace leadspace\gisreviewsslider;

use \Bitrix\Main\Entity;



class CompanyTable extends Entity\DataManager {

    public static function getTableName() {
        return "leadspace_gisreviews_company";
    }

    public static function getMap() {
        return Array(
            new Entity\IntegerField(
                "ID",
                Array(
                    "primary" => true,
                    "autocomplete" => true,
                )
            ),
            new Entity\StringField(
                "NAME",
                Array(
                    "required" => true,
                )
            ),
            new Entity\StringField("RATING"),
            new Entity\StringField("FULL_STARS"),
            new Entity\IntegerField("HALF_STARS"),
            new Entity\IntegerField("EMPTY_STARS"),
            new Entity\StringField("COUNTREVIEWS"),
            new Entity\StringField("COUNTMARKS"),
        );
    }

}