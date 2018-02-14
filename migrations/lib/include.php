<?
$is_console = PHP_SAPI == 'cli' || (!isset($_SERVER['DOCUMENT_ROOT']) && !isset($_SERVER['REQUEST_URI']));

if (!$is_console) {
    die('ERROR: Run script in command line');
}

$_SERVER['DOCUMENT_ROOT'] = __DIR__ . '/../../../../../../';
require __DIR__ . '/../../../../../../bitrix/modules/main/include/prolog_before.php';
require __DIR__ . '/Migration.php';
require __DIR__ . '/IblockMigration.php';
require __DIR__ . '/PropertyMigration.php';
require __DIR__ . '/SectionMigration.php';
require __DIR__ . '/ElementMigration.php';
require __DIR__ . '/UserField.php';