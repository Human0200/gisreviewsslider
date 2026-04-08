<?php

use Bitrix\Main\Localization\Loc;
use Bitrix\Main\Config\Option;
use Bitrix\Main\Loader;
use Bitrix\Main\HttpApplication;

// Подключение языковых файлов
Loc::loadMessages($_SERVER["DOCUMENT_ROOT"] . BX_ROOT . "/modules/main/options.php");
Loc::loadMessages(__FILE__);

$module_id = "leadspace.gisreviewsslider";

// Подключаем модуль
Loader::includeModule($module_id);

// Получаем запрос
$request = HttpApplication::getInstance()->getContext()->getRequest();

// Массив вкладок и полей настроек модуля
$aTabs = [
    [
        "DIV"   => "edit1",
        "TAB"   => Loc::getMessage("LEADSPACE_GISREVIEWS_TAB_SETTINGS"),
        "TITLE" => Loc::getMessage("LEADSPACE_GISREVIEWS_TAB_TITLE"),
        "OPTIONS" => [
            [
                "company_id",
                Loc::getMessage("LEADSPACE_GISREVIEWS_COMPANY_TITLE"),
                "",
                ["text", 50]
            ],
            [
                "hide_logo",
                Loc::getMessage("LEADSPACE_GISREVIEWS_HIDE_LOGO_TITLE"),
                "",
                [
                    "checkbox",
                    [
                        "var1" => "var1",
                    ]
                ]
            ],
            [
                "hide_negative",
                Loc::getMessage("LEADSPACE_GISREVIEWS_NOT_NEGATIVE_REVIEWS"),
                "",
                [
                    "checkbox",
                    [
                        "var2" => "var2",
                    ]
                ]
            ],
        ],
    ],
];

// Сохранение настроек
if ($request->isPost() && $request["Update"] && check_bitrix_sessid()) {
    foreach ($aTabs as $aTab) {
        foreach ($aTab["OPTIONS"] as $arOption) {
            if (!is_array($arOption)) {
                continue;
            }

            if (isset($arOption["note"]) && $arOption["note"]) {
                continue;
            }

            $optionName = $arOption[0];
            $optionValue = $request->getPost($optionName);

            Option::set(
                $module_id,
                $optionName,
                is_array($optionValue) ? implode(",", $optionValue) : (string)$optionValue
            );
        }
    }
}

// Создаем объект вкладок
$tabControl = new CAdminTabControl('tabControl', $aTabs);

// Начинаем вывод формы
$tabControl->Begin();
?>

<form method="post"
      name="leadspace_reviews_settings"
      action="<?=$APPLICATION->GetCurPage()?>?mid=<?=htmlspecialcharsbx($request["mid"])?>&lang=<?=htmlspecialcharsbx($request["lang"])?>">
    <?php
    echo bitrix_sessid_post();

    foreach ($aTabs as $aTab):
        if (!empty($aTab["OPTIONS"])):
            $tabControl->BeginNextTab();
            __AdmSettingsDrawList($module_id, $aTab["OPTIONS"]);
        endif;
    endforeach;

    $tabControl->Buttons();
    ?>
    <input type="submit" name="Update" value="<?=GetMessage("MAIN_SAVE")?>">
    <input type="reset" name="reset" value="<?=GetMessage("MAIN_RESET")?>">
</form>

<?php
$tabControl->End();
?>

<input
    type="button"
    name="start"
    value="<?=GetMessage("LEADSPACE_GISREVIEWSSLIDER_ZAPUSTITQ_IMPORT_OTZ")?>"
    class="adm-btn-save"
    onclick="get_category('catname');"
    style="margin:30px 0;"
>

<div id="result"></div>

<script>
function get_category(catname) {
    var result = document.getElementById('result');

    result.innerHTML = '<img src="/bitrix/images/leadspace.gisreviewsslider/loader.gif" alt="">';

    fetch('/local/leadspace/gisreviewsslider/agent.php?catname=' + encodeURIComponent(catname), {
        method: 'GET',
        headers: {
            'X-Requested-With': 'XMLHttpRequest'
        }
    })
    .then(function(response) {
        if (!response.ok) {
            throw new Error('HTTP error: ' + response.status);
        }
        return response.text();
    })
    .then(function(html) {
        result.innerHTML = html;
    })
    .catch(function(error) {
        result.innerHTML = 'Ошибка загрузки';
        console.error(error);
    });

    return false;
}
</script>