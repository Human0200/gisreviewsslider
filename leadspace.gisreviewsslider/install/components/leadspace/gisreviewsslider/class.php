<?php
use \Bitrix\Main\Loader;
use \Bitrix\Main\Localization\Loc;
use \Bitrix\Main\Type;
use \leadspace\gisreviewsslider\GisReviewsTable;
use \leadspace\gisreviewsslider\GisCompanyTable;
class GisReviews extends CBitrixComponent {
    protected function checkModule() {
        if (!Loader::includeModule("leadspace.gisreviewsslider")) {
            ShowError(Loc::getMessage("LEADSPACE_GISREVIEWSSLIDER_NOT_INSTALLED"));
            return false;
        }
        return true;
    }
}
?>