<?php

use Bitrix\Main\Localization\Loc;
use Bitrix\Main\Config\Option;
use Bitrix\Main\Loader;
use Bitrix\Main\HttpApplication;

// Подключение языковых файлов
Loc::loadMessages($_SERVER["DOCUMENT_ROOT"] . BX_ROOT . "/modules/main/options.php");
Loc::loadMessages(__FILE__);

$module_id = "leadspace.gisreviewsslider";

function showLicenseErrorMessage($moduleId)
{
    if (!class_exists('\leadspace\gisreviewsslider\Agent')) {
        return;
    }

    $moduleLicenseStatus = \CModule::IncludeModuleEx($moduleId);
    $licenseErrorMessage = '';

    if ($moduleLicenseStatus === MODULE_DEMO_EXPIRED) {
        $licenseErrorMessage = 'Отзывы не загружаются, так как ваша лицензия на решение больше не активна.';
    } elseif ($moduleLicenseStatus !== MODULE_INSTALLED && $moduleLicenseStatus !== MODULE_DEMO) {
        $licenseErrorMessage = 'Отзывы не загружаются, так как лицензия на решение недоступна или не активна.';
    }

    if ($licenseErrorMessage !== '' && class_exists('CAdminMessage')) {
        CAdminMessage::ShowMessage([
            'TYPE' => 'ERROR',
            'MESSAGE' => $licenseErrorMessage,
        ]);
    }
}

// Подключаем модуль
Loader::includeModule($module_id);

// Получаем запрос
$request = HttpApplication::getInstance()->getContext()->getRequest();

// AJAX-импорт прямо из страницы настроек (без отдельного файла в /bitrix/admin или /local)
if (
    $request->isPost()
    && ($request->getPost('gis_import') === 'Y' || $request->get('gis_import') === 'Y')
    && check_bitrix_sessid()
) {
    global $USER;
    if (!is_object($USER) || !$USER->IsAuthorized() || !$USER->IsAdmin()) {
        http_response_code(403);
        echo 'Forbidden';
        die();
    }

    header('Content-Type: text/html; charset=UTF-8');
    echo \leadspace\gisreviewsslider\Agent::runImport(true);
    die();
}

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

// Показываем ошибку лицензии сразу после загрузки настроек
ob_start();
showLicenseErrorMessage($module_id);
$licenseMessageHtml = ob_get_clean();

// Создаем объект вкладок
$tabControl = new CAdminTabControl('tabControl', $aTabs);

// Начинаем вывод формы
$tabControl->Begin();
?>

<?=$licenseMessageHtml?>

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

    fetch('<?=CUtil::JSEscape($APPLICATION->GetCurPage())?>?mid=<?=CUtil::JSEscape($module_id)?>&lang=<?=CUtil::JSEscape(LANGUAGE_ID)?>&gis_import=Y', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/x-www-form-urlencoded; charset=UTF-8',
            'X-Requested-With': 'XMLHttpRequest'
        },
        body: 'sessid=<?=CUtil::JSEscape(bitrix_sessid())?>&gis_import=Y&catname=' + encodeURIComponent(catname)
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
