<?php

namespace leadspace\gisreviewsslider;

// Создадим ORM-сущность для хранения информации о книгах, свяжем её со второй таблицей, где будет храниться информация об авторе

use \Bitrix\Main\Entity;
use \Bitrix\Main\Type;

// Имя должно обязательно содержать Table, название файла не обязательно
class ReviewsTable extends Entity\DataManager {


    public static function getTableName() {
        return "leadspace_gisreviewsslider";
    }

    // Если не указывать, то будет использовано значение по умолчанию подключения к бд из файла .settings.php
    // Если указать, то можно выбрать подключение, которое может быть описано в .setting.php
    public static function getConnectionName() {
        return "default";
    }

    // Метод возвращающий структуру ORM-сущности
    public static function getMap() {
        /*
         * Типы полей:
         * DatetimeField
         * DateField
         * BooleanField
         * IntegerField
         * FloatField
         * EnumField
         * TextField
         * StringField
         */
        return Array(
            new Entity\IntegerField(
                "ID",
                Array(
                    // Указываем, что это первичный ключ
                    "primary" => true,
                    // AUTO INCREMENT
                    "autocomplete" => true,
                )
            ),

            new Entity\StringField("NAME"),
            new Entity\StringField("IMAGE"),
            new Entity\IntegerField("RATING"),
            new Entity\IntegerField("DATA"),
            new Entity\StringField("TIME"),
            new Entity\TextField("DESCRIPTION"),

        );
    }


    public static function onAfterAdd(Entity\Event $event) {
        RewiewsTable::clearCache();
    }

    public static function onAfterUpdate(Entity\Event $event) {
        RewiewsTable::clearCache();
    }

    public static function onAfterDelete(Entity\Event $event) {
        RewiewsTable::clearCache();
    }

    public function clearCache() {
        global $CACHE_MANAGER;
        $CACHE_MANAGER->clearByTag('reviews_tag');
    }

}