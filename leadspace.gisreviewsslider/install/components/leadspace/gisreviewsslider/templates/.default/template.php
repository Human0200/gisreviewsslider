<? if (!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED !== true) die();

use \Bitrix\Main\Config\Option;

$idCompany = Option::get("leadspace.gisreviewsslider", "company_id");
$hide_logo = Option::get("leadspace.gisreviewsslider", "hide_logo");
if ($arParams["NO_JQUERY"] != "Y") {
    CJSCore::Init(array("jquery"));
}
if (empty($arParams["SLIDE_DESCKTOP_COUNT"])) {
    $arParams["SLIDE_DESCKTOP_COUNT"] = 2;
}
if (empty($arParams["SLIDE_MOBILE_COUNT"])) {
    $arParams["SLIDE_MOBILE_COUNT"] = 1;
}


$APPLICATION->AddHeadScript($templateFolder . "/slick/slick.js");
$APPLICATION->AddHeadScript($templateFolder . "/js/readmore.js");

?>

<style>
    .gisreviews .ls_review-bottom .ls_button-next:hover, .ls_review-bottom .ls_button-prev:hover {
        background-color: <?=$arParams["COLOR_BUTTONS"] ?  $arParams["COLOR_BUTTONS"]: "#007aff";?> !important;
        color: #ffffff !important;
    }
    .ls_app-body.gisreviews .slick-dots li.slick-active button {
        background-color: <?=$arParams["COLOR_BUTTONS"] ?  $arParams["COLOR_BUTTONS"]: "#007aff";?> !important;
    }

    .gisreviews .slick-dots li.slick-active button {
        background: <?=$arParams["COLOR_BUTTONS"] ?  $arParams["COLOR_BUTTONS"]: "#007aff";?>;
    }

    .ls_app-body.gisreviews a.ls_reviews-btn {

        color: <?=$arParams["COLOR_BUTTON_TEXT"] ? $arParams["COLOR_BUTTON_TEXT"]: "#212121";?> !important;
        background-color: <?=$arParams["COLOR_BUTTON"] ? $arParams["COLOR_BUTTON"]: "#ffffff";?>;
    }

</style>
<div class="ls_app-body gisreviews">
    <div class="ls_review-box ls_review-unselectable">
        <div class="ls_business-summary-rating-badge-view__rating"><?= $arResult["COMPANY"][0]["NAME"] ?>
            <br> <?= $arResult["COMPANY"][0]["RATING"] ?> <?= GetMessage("LEADSPACE_GISREVIEWSSLIDER_IZ") ?>
        </div>
        <div class="ls_business-rating-badge-view__stars">

            <?
            if ($arResult["COMPANY"][0]["FULL_STARS"] > 0) {
                for ($n = 1; $n <= $arResult["COMPANY"][0]["FULL_STARS"]; $n++) {
                    echo '<span class="inline-image _loaded icon ls_business-rating-badge-view__star _full" aria-hidden="true" role="button" tabindex="-1" style="font-size: 0px; line-height: 0;"><svg width="22" height="22" viewBox="0 0 16 16" xmlns="http://www.w3.org/2000/svg"><path fill-rule="evenodd" clip-rule="evenodd" d="M7.985 11.65l-3.707 2.265a.546.546 0 0 1-.814-.598l1.075-4.282L1.42 6.609a.546.546 0 0 1 .29-.976l4.08-.336 1.7-3.966a.546.546 0 0 1 1.004.001l1.687 3.965 4.107.337c.496.04.684.67.29.976l-3.131 2.425 1.073 4.285a.546.546 0 0 1-.814.598L7.985 11.65z" fill="currentColor"></path></svg></span>';
                }
            }

            if ($arResult["COMPANY"][0]["HALF_STARS"] > 0) {
                for ($n = 1; $n <= $arResult["COMPANY"][0]["HALF_STARS"]; $n++) {
                    echo '<span class="inline-image _loaded icon ls_business-rating-badge-view__star _half" aria-hidden="true" role="button" tabindex="-1" style="font-size: 0px; line-height: 0;"><svg width="22" height="22" viewBox="0 0 16 16" xmlns="http://www.w3.org/2000/svg"><path fill-rule="evenodd" clip-rule="evenodd" d="M7.985 11.65l-3.707 2.266a.546.546 0 0 1-.814-.598l1.075-4.282L1.42 6.609a.546.546 0 0 1 .29-.975l4.08-.336 1.7-3.966a.546.546 0 0 1 1.004.001l1.687 3.965 4.107.337c.496.04.684.67.29.975l-3.131 2.426 1.073 4.284a.546.546 0 0 1-.814.6l-3.722-2.27z" fill="#CCC"></path><path fill-rule="evenodd" clip-rule="evenodd" d="M4.278 13.915l3.707-2.266V1a.538.538 0 0 0-.494.33l-1.7 3.967-4.08.336a.546.546 0 0 0-.29.975l3.118 2.427-1.075 4.282a.546.546 0 0 0 .814.598z" fill="#FC0"></path></svg></span>';
                }
            }
            if ($arResult["COMPANY"][0]["EMPTY_STARS"] > 0) {
                for ($n = 1; $n <= $arResult["COMPANY"][0]["EMPTY_STARS"]; $n++) {
                    echo '<span class="inline-image _loaded icon ls_business-rating-badge-view__star _empty" aria-hidden="true" role="button" tabindex="-1" style="font-size: 0px; line-height: 0;"><svg width="22" height="22" viewBox="0 0 16 16" xmlns="http://www.w3.org/2000/svg"><path fill-rule="evenodd" clip-rule="evenodd" d="M7.985 11.65l-3.707 2.265a.546.546 0 0 1-.814-.598l1.075-4.282L1.42 6.609a.546.546 0 0 1 .29-.976l4.08-.336 1.7-3.966a.546.546 0 0 1 1.004.001l1.687 3.965 4.107.337c.496.04.684.67.29.976l-3.131 2.425 1.073 4.285a.546.546 0 0 1-.814.598L7.985 11.65z" fill="currentColor"></path></svg></span>';
                }
            } ?>
        </div>

        <a target="_blank" href="<?= $idCompany ?>"><img
                    src="<?= $templateFolder ?>/logo_2gis.svg" style="width:100px;"></a>


        <? if ($arParams["SHOW_COUNT"] == "SHOW_COUNT_REVIEWS") { ?>
            <div class="ls_reviews-count"><a target="_blank"
                                             href="<?= $idCompany ?>"><?= GetMessage("LEADSPACE_GISREVIEWSSLIDER_NA") ?><?= $arResult["COMPANY"][0]["COUNTREVIEWS"] ?> <?= GetMessage("LEADSPACE_GISREVIEWSSLIDER_REVIEWS") ?></a>
            </div>
        <? } ?>

        <? if ($arParams["SHOW_COUNT"] == "SHOW_COUNT_MARKS") { ?>
            <div class="ls_reviews-count"><a target="_blank"
                                             href="<?= $idCompany ?>"><?= GetMessage("LEADSPACE_GISREVIEWSSLIDER_NA") ?><?= $arResult["COMPANY"][0]["COUNTMARKS"] ?> <?= GetMessage("LEADSPACE_GISREVIEWSSLIDER_MARKS") ?></a>
            </div>
        <? } ?>
        <? if($arParams["GIS_BUTTON"]){?>
        <a target="_blank" href="<?= $idCompany ?>?writeReview"
           class="ls_reviews-btn ls_reviews-btn-form"
           rel="nofollow noreferrer noopener"><?= $arParams["GIS_BUTTON"]?></a>
        <?}?>
    </div>
    <div style="min-width: 0;width: 100%;">
        <div class="ls_review-list">
            <div class="ls_gis_regular">
                <?
                foreach ($arResult["ITEMS"] as $value) {
                    ?>
                    <div class="ls_slide">
                        <div class="ls_review-item">

                            <div class="ls_business-review-view__info">
                                <div class="ls_business-review-view__author-container">
                                    <div class="ls_business-review-view__author-image">
                                        <? if (!$value["IMAGE"]) {
                                            $value["IMAGE"] = "data:image/svg+xml,%3Csvg width='100%25' height='100%25' version='1.1' viewBox='0 0 123.96 124.31' xmlns='http://www.w3.org/2000/svg'%3E%3Cg transform='translate(-54.068 -77.525)' fill='%23b3b3b3'%3E%3Ccircle cx='116.18' cy='112.13' r='34.607'/%3E%3Cpath d='m54.068 201.84h123.96v-7.6073c-3.3235-20.13-29.184-36.953-61.047-37.012-37.638 0.90944-59.352 17.834-62.914 37.012z'/%3E%3C/g%3E%3C/svg%3E%0A";
                                        } ?>
                                        <img class="ls-avatar" loading="lazy" src="<?= $value["IMAGE"] ?>"
                                             alt="<?= $value["NAME"] ?>">


                                    </div>
                                    <div class="ls_business-review-view__author-info">
                                        <div class="ls_business-review-view__author-name"><?= $value["NAME"] ?>
                                        </div>
                                    </div>
                                </div>
                                <div class="ls_business-review-view__header">
                                    <div class="ls_business-review-view__rating">
                                        <div class="ls_business-rating-badge-view _size_m _weight_medium">
                                            <div class="ls_business-rating-badge-view__stars">
                                                <? for ($n = 1; $n <= 5; $n++) {
                                                    if ($n <= $value["RATING"]) {
                                                        echo '<span class="inline-image _loaded icon ls_business-rating-badge-view__star _full" aria-hidden="true" role="button" tabindex="-1" style="font-size: 0px; line-height: 0;"><svg width="16" height="16" viewBox="0 0 16 16" xmlns="http://www.w3.org/2000/svg"><path fill-rule="evenodd" clip-rule="evenodd" d="M7.985 11.65l-3.707 2.265a.546.546 0 0 1-.814-.598l1.075-4.282L1.42 6.609a.546.546 0 0 1 .29-.976l4.08-.336 1.7-3.966a.546.546 0 0 1 1.004.001l1.687 3.965 4.107.337c.496.04.684.67.29.976l-3.131 2.425 1.073 4.285a.546.546 0 0 1-.814.598L7.985 11.65z" fill="currentColor"></path></svg></span>';
                                                    } else {
                                                        echo '<span class="inline-image _loaded icon ls_business-rating-badge-view__star _empty" aria-hidden="true" role="button" tabindex="-1" style="font-size: 0px; line-height: 0;"><svg width="16" height="16" viewBox="0 0 16 16" xmlns="http://www.w3.org/2000/svg"><path fill-rule="evenodd" clip-rule="evenodd" d="M7.985 11.65l-3.707 2.265a.546.546 0 0 1-.814-.598l1.075-4.282L1.42 6.609a.546.546 0 0 1 .29-.976l4.08-.336 1.7-3.966a.546.546 0 0 1 1.004.001l1.687 3.965 4.107.337c.496.04.684.67.29.976l-3.131 2.425 1.073 4.285a.546.546 0 0 1-.814.598L7.985 11.65z" fill="currentColor"></path></svg></span>';
                                                    }
                                                } ?>

                                            </div>
                                        </div>
                                    </div>
                                    <span class="ls_business-review-view__date"> <?
                                        $dateFormatted = FormatDate('d F Y', $value["TIME"], CSite::GetDateFormat());


                                        echo $dateFormatted;
                                        ?>
		</span>
                                </div>
                                <div dir="auto" class="ls_business-review-view__body">
                                    <div class="ls_items-text">
                                        <?= $value["DESCRIPTION"] ?>
                                    </div>
                                    <a target="_blank" href="<?= $idCompany ?>"
                                       class="ls_review-source-link"><?= GetMessage("LEADSPACE_GIS_MAP_REVIEW") ?></a>
                                </div>
                            </div>
                        </div>
                    </div>
                    <?
                }
                ?>
            </div>


        </div>
        <div class="ls_review-bottom">
            <div class="ls_buttons">
                <div class="ls_button-prev" tabindex="0" role="button" aria-label="Previous slide">
                    <svg viewBox="0 0 100 100" xmlns="http://www.w3.org/2000/svg" style="height: 12px">
                        <path d="m70.143 97.5-44.71-44.711a3.943 3.943 0 0 1 0-5.578l44.71-44.711 5.579 5.579-41.922 41.921 41.922 41.922z"/>
                    </svg>
                </div>
                <div class="ls_button-next" tabindex="0" role="button" aria-label="Next slide">
                    <svg viewBox="0 0 100 100" xmlns="http://www.w3.org/2000/svg"
                         style="height: 12px;transform: rotate(180deg)">
                        <path d="m70.143 97.5-44.71-44.711a3.943 3.943 0 0 1 0-5.578l44.71-44.711 5.579 5.579-41.922 41.921 41.922 41.922z"/>
                    </svg>
                </div>
            </div>
            <div class="ls_pagination ls_pagination-clickable ls_pagination-bullets ls_pagination-horizontal">
            </div>
            <? if ($hide_logo != "Y") { ?>
                <div class="ls_business-review-view_copy">
                <div><?= GetMessage("LEADSPACE_GISREVIEWSSLIDER_RAZRABOTANO") ?></div>
                <a href="https://lead-space.ru/?utm_source=marketplace.1c-bitrix" target="_blank"><img
                            src="<?= $templateFolder ?>/logo.svg" style="width:70px"></a></div><? } ?>
        </div>
    </div>
</div>
</div>
<script>
    $('.ls_items-text').readmore({
        collapsedHeight: 40
    });
    $(".gisreviews .ls_gis_regular").slick({
        dots: true,
        infinite: true,
        slidesToShow: <?=$arParams["SLIDE_DESCKTOP_COUNT"]?>,
        prevArrow: $('.gisreviews .ls_button-prev'),
        nextArrow: $('.gisreviews .ls_button-next'),
        appendDots: $('.gisreviews .ls_pagination'),
        responsive: [

            {
                breakpoint: 800,
                settings: {
                    slidesToShow: <?=$arParams["SLIDE_MOBILE_COUNT"]?>,
                    dots: false,
                }
            }

        ],
        <?if($arParams["AUTOPLAY"] == "Y"){?>
        autoplay: true,
        autoplaySpeed: <?=$arParams["AUTOPLAY_SPEED"]?>,
        <?}?>
    });
</script>
