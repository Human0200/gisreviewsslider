<?php
use \Bitrix\Main\Localization\Loc;
if (!check_bitrix_sessid())
    return;
?>
<!-- Выводим кнопку для перехода на страницу модулей (мы и так находимся на этой странице но с выведенным файлом, значит просто получаем текущую директорию для перенаправления -->
<form action="<?=$APPLICATION->GetCurPage()?>">
    <!-- Обязательное получение сессии -->
    <?=bitrix_sessid_post()?>
    <!-- В форме обязательно должно быть поле lang, с айди языка, чтобы язык не сбросился -->
    <input type="hidden" name="lang" value="<?=LANGUAGE_ID?>">
    <!-- Айди модуля для установки -->
    <input type="hidden" name="id" value="leadspace.gisreviewsslider">
    <!-- Обязательно указывать поле install со значением Y, иначе просто перейдем на страницу модулей -->
    <input type="hidden" name="install" value="Y">
    <!-- Определение шага установки модуля -->
    <input type="hidden" name="step" value="2">
    <!-- Чекбокс для определния параметра добавления тестовых данных -->

    <!-- MOD_INSTALL - системная языковая переменная для кнопки установки -->
    <input type="submit" name="" value="<?=Loc::getMessage("MOD_INSTALL")?>">
</form>
