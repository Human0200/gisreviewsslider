<?php
use \Bitrix\Main\Localization\Loc;
if (!check_bitrix_sessid())
    return;
?>
<form action="<?=$APPLICATION->GetCurPage()?>">
    <!-- ќб€зательное получение сессии -->
    <?=bitrix_sessid_post()?>
    <!-- ¬ форме об€зательно должно быть поле lang, с айди €зыка, чтобы €зык не сбросилс€ -->
    <input type="hidden" name="lang" value="<?=LANGUAGE_ID?>">
    <!-- јйди модул€ дл€ удалени€ -->
    <input type="hidden" name="id" value="leadspace.gisreviewsslider">
    <!-- ќб€зательно указывать поле uninstall со значением Y, иначе просто перейдем на страницу модулей -->
    <input type="hidden" name="uninstall" value="Y">
    <!-- ќпределение шага удалени€ модул€ -->
    <input type="hidden" name="step" value="2">
    <!-- ѕредупреждение об удалении модул€ -->
    <?=CAdminMessage::ShowMessage(Loc::getMessage("MOD_UNINST_WARN"))?> <!-- MOD_UNINST_WARN - системна€ €зыкова€ переменна€ -->
    <!-- „екбокс дл€ определни€ параметра удалени€ -->
    <p><?=Loc::getMessage("MOD_UNINST_SAVE")?></p>
    <!-- MOD_UNINST_SAVE - системна€ €зыкова€ переменна€ -->
    <!-- MOD_UNINST_SAVE_TABLES - системна€ €зыкова€ переменна€ -->
    <p><input type="checkbox" name="save_data" id="save_data" value="Y" checked><label for="save_data"><?=Loc::getMessage("MOD_UNINST_SAVE_TABLES")?></label></p>
    <!-- MOD_UNINST_DEL - системна€ €зыкова€ переменна€ -->
    <input type="submit" name="" value="<?=Loc::getMessage("MOD_UNINST_DEL")?>">
</form>
