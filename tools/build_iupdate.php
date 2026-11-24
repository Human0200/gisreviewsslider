<?php
// CLI-эмуляция update_create.php: сборка iupdate-пакета 1.0.13 из установленного модуля.
$_SERVER['DOCUMENT_ROOT'] = '/home/l/lsleadqn/yandexreviews.leadspace-tech.ru/public_html';
define('NO_KEEP_STATS', true);
define('NO_AGENT_CHECK', true);
define('NOT_CHECK_PERMISSIONS', true);
require($_SERVER['DOCUMENT_ROOT'] . '/bitrix/modules/main/include/prolog_before.php');

$arSettings = [
    'MODULE_ID' => 'leadspace.gisreviewsslider',
    'U_PATH' => '/iupdate/',
    'MODULE_PATH' => $_SERVER['DOCUMENT_ROOT'] . '/bitrix/modules/leadspace.gisreviewsslider/',
    'UPDATE_PATH' => $_SERVER['DOCUMENT_ROOT'] . '/iupdate/',
];
$VERSION = '1.0.13';
$DESCRIPTION = <<< 'TXT'
Обновление модуля до версии 1.0.13.
Исправление импорта отзывов с 2ГИС.

- Импорт без Selenium: прямая загрузка HTML страницы отзывов 2ГИС
- Поддержка коротких ссылок go.2gis.com и вкладки /tab/reviews
- Если HTML пустой — таблицы отзывов больше не очищаются
- Актуальные CSS-селекторы 2ГИС (счётчик отзывов, даты)
- Импорт запускается прямо со страницы настроек модуля
- Безопасность: проверка sessid и прав администратора
TXT
;

$UPD = [
    'VERSION' => $VERSION,
    'DESCRIPTION' => $DESCRIPTION,
    'FOLDERS' => ['admin/', 'install/', 'lang/', 'lib/'],
    'FILES' => ['include.php', 'options.php', 'default_option.php'],
    'ARCHIVE' => 'Y',
];

$dest = $arSettings['UPDATE_PATH'] . $UPD['VERSION'] . '/';
if (is_dir($dest)) {
    DeleteDirFilesEx($arSettings['U_PATH'] . $UPD['VERSION'] . '/');
    @rmdir($dest);
}

foreach ($UPD['FOLDERS'] as $folder) {
    CopyDirFiles($arSettings['MODULE_PATH'] . $folder, $dest . $folder, true, true);
}
foreach ($UPD['FILES'] as $file) {
    CopyDirFiles($arSettings['MODULE_PATH'] . $file, $dest . $file, true, true);
}

if (!is_dir($dest . 'install/')) {
    mkdir($dest . 'install/', 0775, true);
}
file_put_contents($dest . 'install/version.php', "<?php\n\$arModuleVersion = array(\n\t\"VERSION\" => \"{$UPD['VERSION']}\",\n\t\"VERSION_DATE\" => \"" . date('Y-m-d H:i:s') . "\"\n);\n");

file_put_contents($dest . 'description.ru', $UPD['DESCRIPTION']);

$arUpdater = [];
foreach ([
    'install/js' => 'js/main/core',
    'install/components' => 'components',
    'install/local' => '../local',
    'install/admin' => 'admin',
] as $cFrom => $cTo) {
    if (file_exists($dest . $cFrom)) {
        $arUpdater[] = '$updater->CopyFiles("' . $cFrom . '", "' . $cTo . '");';
    }
}
if (!empty($arUpdater)) {
    file_put_contents($dest . 'updater.php', "<?php\n" . implode("\n", $arUpdater) . "\n");
}

$tempFile = $arSettings['UPDATE_PATH'] . '/' . $UPD['VERSION'] . '.tar.gz';
@unlink($tempFile);
require_once($_SERVER['DOCUMENT_ROOT'] . '/bitrix/modules/main/classes/general/tar_gz.php');
$oArchiver = new CArchiver($tempFile, true);
$ok = $oArchiver->add($dest, false, $dest);

echo $ok ? "ARCHIVE OK: {$tempFile}\n" : "ARCHIVE FAIL\n";
echo 'files: ', count(iterator_to_array(new RecursiveIteratorIterator(new RecursiveDirectoryIterator($dest, FilesystemIterator::SKIP_DOTS)))), PHP_EOL;
