<?php
if (!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED !== true) die();

use \Bitrix\Main\Config\Option;

$hide_negative = Option::get("leadspace.gisreviewsslider", "hide_negative");
if ($this->checkModule()) {


    $elementsCount = ($arParams["PAGER_COUNT"]) ? $arParams["PAGER_COUNT"] : 5;
    $nav = new \Bitrix\Main\UI\PageNavigation("page");
    $nav->allowAllRecords(true)
        ->setPageSize($elementsCount)
        ->initFromUri();
    if ($arParams["NEWS_SORT"] == "BY_DATE") {
        $arOrder = ["DATA" => "DESC"];
    } else {
        $arOrder = ["ID" => "DESC"];
    }
    if ($hide_negative == "Y") {
        $arFilter = ["RATING" => array(4, 5)];
    } else {
        $arFilter = ["RATING" => array(1, 2, 3, 4, 5)];
    }


    $ttl = $arParams["CACHE_TIME"];
    $bUSER_HAVE_ACCESS = $arParams["USE_PERMISSIONS"] ?? "";

    $cacheKey = array(($arParams["CACHE_GROUPS"] === "N" ? false : $USER->GetGroups()), $bUSER_HAVE_ACCESS, $nav, $arOrder);
    $cachePath = "/" . SITE_ID . $this->GetRelativePath();
    if ($this->StartResultCache($ttl, $cacheKey, $cachePath)) {
        $result = \leadspace\gisreviewsslider\ReviewsTable::getList(array(
            "filter" => $arFilter,
            "order" => $arOrder,
            "limit" => $nav->getLimit(),
            "offset" => $nav->getOffset(),
        ));
        $nav->setRecordCount(\leadspace\gisreviewsslider\ReviewsTable::getList()->getSelectedRowsCount());
        $arResult["ITEMS"] = $result->fetchAll();

        $arOrder = array("ID" => "DESC");

		$result = \leadspace\gisreviewsslider\CompanyTable::getList(array(
			"order" => $arOrder,
			"limit" => $nav->getLimit(),
			"offset" => $nav->getOffset(),
		));
		$arResult["COMPANY"] = $result->fetchAll();
		$arResult["NAV"] = $nav;
		$this->IncludeComponentTemplate();
	}

}

?>