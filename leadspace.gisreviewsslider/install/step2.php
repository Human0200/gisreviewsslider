<?php
use \Bitrix\Main\Localization\Loc;
if (!check_bitrix_sessid())
    return;
// ѕровер€ем была ли выброшена ошибка при установке, если да, то записываем еЄ в переменную $ex
if ($ex = $APPLICATION->GetException())
    // ¬ыводим ошибку
    echo CAdminMessage::ShowMessage(array(
        "TYPE" => "ERROR",
        "MESSAGE" => Loc::getMessage("MOD_INST_ERR"), // (MOD_INST_ERR - системна€ €зыкова€ переменна€)
        "DETAILS" => $ex->GetString(),
        "HTML" => true,
    ));
else
    // ≈сли ошибки не было, то выводим сообщение об установке модул€ (MOD_INST_OK - системна€ €зыкова€ переменна€)
    echo CAdminMessage::ShowNote(Loc::getMessage("MOD_INST_OK"));
?>
<!-- ¬ыводим кнопку дл€ перехода на страницу модулей (мы и так находимс€ на этой странице но с выведенным файлом, значит просто получаем текущую директорию дл€ перенаправлени€ -->
<form action="<?=$APPLICATION->GetCurPage()?>">
    <!-- ¬ форме об€зательно должно быть поле lang, с айди €зыка, чтобы €зык не сбросилс€ -->
    <input type="hidden" name="lang" value="<?=LANGUAGE_ID?>">
    <!-- MOD_BACK - системна€ €зыкова€ переменна€ дл€ возврата -->
    <input type="submit" name="" value="<?=Loc::getMessage("MOD_BACK")?>">
</form>
