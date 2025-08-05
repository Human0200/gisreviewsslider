<?php
use \Bitrix\Main\Localization\Loc;
if (!check_bitrix_sessid())
    return;
?>
<!-- ¬ыводим кнопку дл€ перехода на страницу модулей (мы и так находимс€ на этой странице но с выведенным файлом, значит просто получаем текущую директорию дл€ перенаправлени€ -->
<form action="<?=$APPLICATION->GetCurPage()?>">
    <!-- ќб€зательное получение сессии -->
    <?=bitrix_sessid_post()?>
    <!-- ¬ форме об€зательно должно быть поле lang, с айди €зыка, чтобы €зык не сбросилс€ -->
    <input type="hidden" name="lang" value="<?=LANGUAGE_ID?>">
    <!-- јйди модул€ дл€ установки -->
    <input type="hidden" name="id" value="leadspace.gisreviewsslider">
    <!-- ќб€зательно указывать поле install со значением Y, иначе просто перейдем на страницу модулей -->
    <input type="hidden" name="install" value="Y">
    <!-- ќпределение шага установки модул€ -->
    <input type="hidden" name="step" value="2">
    <!-- „екбокс дл€ определни€ параметра добавлени€ тестовых данных -->

    <!-- MOD_INSTALL - системна€ €зыкова€ переменна€ дл€ кнопки установки -->
    <input type="submit" name="" value="<?=Loc::getMessage("MOD_INSTALL")?>">
</form>
