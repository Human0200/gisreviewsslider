<?php

// Страница настроек модуля

use Bitrix\Main\Localization\Loc;
use Bitrix\Main\Config\Option;
use Bitrix\Main\Loader;

// Подключение языковых файлов
Loc::loadMessages($_SERVER["DOCUMENT_ROOT"] . BX_ROOT . "/modules/main/options.php");
Loc::loadMessages(__FILE__);
CJSCore::Init(array("jquery"));

$module_id = "leadspace.gisreviewsslider";


// Подключаем наш модуль
Loader::includeModule($module_id);

// Получение запроса из контекста для обработки данных, которые придут с форм
$request = \Bitrix\Main\HttpApplication::getInstance()->getContext()->getRequest();

// Массив вкладок и полей настроек модуля
$aTabs = Array(
    array(
        "DIV"   => "edit1", // Идентификатор вкладки (используется для javascript)
        "TAB"   => Loc::getMessage("LEADSPACE_GISREVIEWS_TAB_SETTINGS"), // Название вкладки
        "TITLE" => Loc::getMessage("LEADSPACE_GISREVIEWS_TAB_TITLE"),    // Заголовок и всплывающее сообщение вкладки
        // Массив настроек опций для вкладки
        "OPTIONS" => Array(
           
            Array(
                "company_id", 
                Loc::getMessage("LEADSPACE_GISREVIEWS_COMPANY_TITLE"),
                "",
                Array(
                    "text", 
                    50 // Ширина
                )
            ),
           
          Array(
                "hide_logo", // Имя поля для хранения в бд
                Loc::getMessage("LEADSPACE_GISREVIEWS_HIDE_LOGO_TITLE"),
                "", 
                Array(
                    "checkbox", 
                    Array(
                        "var1" => "var1", 
                       
                    )
                )
            ),
            Array(
                "hide_negative", // Имя поля для хранения в бд
                Loc::getMessage("LEADSPACE_GISREVIEWS_NOT_NEGATIVE_REVIEWS"),
                "",
                Array(
                    "checkbox",
                    Array(
                        "var2" => "var2",

                    )
                )
            ),
        ),
    ),

);

// Если пришел запрос на обновление и сессия активна, то обходим массив созданных полей
if ($request->isPost() && $request["Update"] && check_bitrix_sessid()) {
    foreach ($aTabs as $aTab) {
        foreach ($aTab["OPTIONS"] as $arOption) {

            // Существуют строки с подстветкой, которые не нужно обрабатывать, поэтому пропускаем их
            if (!is_array($arOption))
                continue;
            if ($arOption["note"])
                continue;

            // Имя настройки
            $optionName = $arOption[0];
            // Значение настройки, которое пришло в запросе
            $optionValue = $request->getPost($optionName);
            // Установка значения по айди модуля и имени настройки
            // Хранить можем только текст, значит если приходит массив, то разбиваем его через запятую
            Option::set($module_id, $optionName, is_array($optionValue) ? implode(",", $optionValue) : $optionValue);
        }
    }
}

// Создаем объект класса AdminTabControl
$tabControl = new CAdminTabControl('tabControl', $aTabs);

// Начинаем формирование формы
$tabControl->Begin();

?>
<form method="post" name="leadspace_reviews_settings" action="<?=$APPLICATION->GetCurPage()?>?mid=<?=htmlspecialcharsbx($request["mid"])?>&lang=<?=$request["lang"]?>">
    <?
    echo bitrix_sessid_post();
    foreach ($aTabs as $aTab):
        if ($aTab["OPTIONS"]):
            // Указываем начало формирования первой вкладки
            $tabControl->BeginNextTab();
            // Отрисовываем поля по заданному массиву (автоматически подставляет значения, если они были заданы)
            __AdmSettingsDrawList($module_id, $aTab["OPTIONS"]);
        endif;
    endforeach;

    // Т.к. цикл не затрагивает вкладку прав (у неё нет опций), то вызовем её отдельно
    // Если в install/index.php не определены свои параметры прав, то выведутся значения по умолчанию
    /*$tabControl->BeginNextTab();
    require_once $_SERVER["DOCUMENT_ROOT"] . "/bitrix/modules/main/admin/group_rights.php"; // Именно в этом вызове используется $module_id
**/
    // Отрисуем кнопки
    $tabControl->Buttons();
    ?>
    <input type="submit" name="Update" value="<?=GetMessage("MAIN_SAVE")?>">
    <input type="reset" name="reset" value="<?=GetMessage("MAIN_RESET")?>">
</form>

<?php

// Заканчиваем формирование формы
$tabControl->End();


?>

<?php if($_SERVER['HTTP_X_REQUESTED_WITH'] == 'XMLHttpRequest'){
    if (isset($_REQUEST['catname'])){
        $catname = $_REQUEST['catname'];
        echo select_cat($catname);
    }
}
function select_cat($select_cat){
    echo $select_cat;
}
?>

<input type="submit" name="start" value="<?=GetMessage("LEADSPACE_GISREVIEWSSLIDER_ZAPUSTITQ_IMPORT_OTZ")?>" class="adm-btn-save" onclick="get_category('catname');" style="margin:30px 0;">

<div id="result"></div>

<script>
BX.ready(function(){
  window.get_category = function(catname){
    var resultNode = BX('result');
    BX.html(resultNode, '<img src="/bitrix/images/leadspace.gisreviewsslider/loader.gif">');

    BX.ajax({
      url: '/local/leadspace/gisreviewsslider/agent.php',
      method: 'POST',
      data: { catname: catname },
      onsuccess: function(response){
        // если agent.php отдает HTML — просто вставляем:
        BX.html(resultNode, response);
        // если отдает JSON — сначала JSON.parse(response) и рендери как нужно
      },
      onfailure: function(){
        BX.html(resultNode, 'Ошибка загрузки. Попробуйте позже.');
      }
    });
  };
});
</script>