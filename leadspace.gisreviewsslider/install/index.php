<?php

use \Bitrix\Main\Localization\Loc;
use \Bitrix\Main\Loader;
use \Bitrix\Main\Entity\Base;
use \Bitrix\Main\Application;
use \Bitrix\Main\ModuleManager;
use \Bitrix\Main\EventManager;
use \Bitrix\Main\Config\Option;
use \Bitrix\Main\Type;

Loc::loadMessages(__FILE__);

class leadspace_gisreviewsslider extends CModule
{

	public $arResponse = [
		"STATUS" => true,
		"MESSAGE" => ""
	];

	public function setResponse($status, $message = "") {
		$this->arResponse["STATUS"] = $status;
		$this->arResponse["MESSAGE"] = $message;
	}

	function __construct() {

		$arModuleVersion = array();

		// Подключение файла версии, который содержит массив для модуля
		require(__DIR__ . "/version.php");


		$this->MODULE_ID = "leadspace.gisreviewsslider"; // Имя модуля

		// Переменная пути до папки с компонентами, для опциональной установки в папку local
		$this->COMPONENTS_PATH = $_SERVER["DOCUMENT_ROOT"] . "/local/components/";

		$this->MODULE_VERSION = $arModuleVersion["VERSION"];
		$this->MODULE_VERSION_DATE = $arModuleVersion["VERSION_DATE"];
		$this->MODULE_NAME = Loc::getMessage("LEADSPACE_GISREVIEWS_MODULE_NAME");
		$this->MODULE_DESCRIPTION = Loc::getMessage("LEADSPACE_GISREVIEWS_MODULE_DESCRIPTION");

		// Имя партнера создавшего модуль (Выводится информация в списке модулей о человеке или компании, которая создала этот модуль)
		$this->PARTNER_NAME = Loc::getMessage("LEADSPACE_GISREVIEWS_PARTNER_NAME");
		$this->PARTNER_URI = Loc::getMessage("LEADSPACE_GISREVIEWS_PARTNER_URI");

		// Если указано, то на странице прав доступа будут показаны администраторы и группы (страницу сначала нужно запрограммировать)
		$this->SHOW_SUPER_ADMIN_GROUP_RIGHTS = "Y";
		// Если указано, то на странице редактирования групп будет отображаться этот модуль
		$this->MODULE_GROUP_RIGHTS = "Y";

	}


	// Установка баз данных
	function installDB() {
		Loader::includeModule($this->MODULE_ID);
		if (!Application::getConnection(\leadspace\gisreviewsslider\ReviewsTable::getConnectionName())->isTableExists(Base::getInstance("\leadspace\gisreviewsslider\ReviewsTable")->getDBTableName()))
			Base::getInstance("\leadspace\gisreviewsslider\ReviewsTable")->createDbTable(); // Если таблицы не существует, то создаем её по ORM сущности
		if (!Application::getConnection(\leadspace\gisreviewsslider\CompanyTable::getConnectionName())->isTableExists(Base::getInstance("\leadspace\gisreviewsslider\CompanyTable")->getDBTableName()))
			Base::getInstance("\leadspace\gisreviewsslider\CompanyTable")->createDbTable(); // Если таблицы не существует, то создаем её по ORM сущности
	}


	// Копирование файлов
	function installFiles() {
		$resMsg = "";
		$res = CopyDirFiles(
			__DIR__ . "/local",
			$_SERVER["DOCUMENT_ROOT"] . "/local/",
			true,
			true
		);
		if (!$res) {
			$resMsg = Loc::getMessage("LEADSPACE_GISREVIEWS_INSTALL_ERROR_FILES_COM");
			$this->setResponse(false, $resMsg);
			return false;
		}
		$res = CopyDirFiles(
			__DIR__ . "/components",
			$this->COMPONENTS_PATH,
			true,
			true
		);
        CopyDirFiles(Application::getDocumentRoot()."/bitrix/modules/".$this->MODULE_ID."/install/assets/images", Application::getDocumentRoot()."/bitrix/images/".$this->MODULE_ID."/", true, true);
		if (!$res) {
			$resMsg = Loc::getMessage("LEADSPACE_GISREVIEWS_INSTALL_ERROR_FILES_COM");
			$this->setResponse(false, $resMsg);
			return false;
		}
		$this->setResponse(true);
		return true;
	}


	// Установка агентов
	function installAgents() {
		\CAgent::AddAgent(
			"\leadspace\gisreviewsslider\Agent::superGisAgent();",
			$this->MODULE_ID,
			"N",
			86400,
			"",
			"Y",
			"",
			1
		);
	}


	// Для удобства проверки результата
	function checkAddResult($result) {
		if ($result->isSuccess()) {
			return [true, $result->getId()];
		}
		return [false, $result->getErrorMessages()];
	}

	function DoInstall() {
		global $APPLICATION;

		$context = Application::getInstance()->getContext();
		$request = $context->getRequest();
		// Проверяем какой сейчас шаг, если он не существует или меньше 2, то выводим первый шаг установки
		if ($request["step"] < 2) {
			if (file_exists($_SERVER["DOCUMENT_ROOT"] . "/local/modules/leadspace.gisreviewsslider/install/step1.php"))
				$APPLICATION->IncludeAdminFile(Loc::getMessage("LEADSPACE_GISREVIEWS_INSTALL_TITLE"), $_SERVER["DOCUMENT_ROOT"] . "/local/modules/leadspace.gisreviewsslider/install/step1.php");
			else
				$APPLICATION->IncludeAdminFile(Loc::getMessage("LEADSPACE_GISREVIEWS_INSTALL_TITLE"), $_SERVER["DOCUMENT_ROOT"] . "/bitrix/modules/leadspace.gisreviewsslider/install/step1.php");
		} elseif ($request["step"] == 2) {

			ModuleManager::registerModule($this->MODULE_ID);
			$this->installDB();
			$this->installEvents();
			$this->installAgents();
			if (!$this->installFiles())
				$APPLICATION->ThrowException($this->arResponse["MESSAGE"]);
			if ($request["add_data"] == "Y") {
				$result = $this->addTestData();
				if ($result !== true)
					$APPLICATION->ThrowException($result);
			}
			if (file_exists($_SERVER["DOCUMENT_ROOT"] . "/local/modules/leadspace.gisreviewsslider/install/step2.php"))
				$APPLICATION->IncludeAdminFile(Loc::getMessage("LEADSPACE_GISREVIEWS_INSTALL_TITLE"), $_SERVER["DOCUMENT_ROOT"] . "/local/modules/leadspace.gisreviewsslider/install/step2.php");
			else
				$APPLICATION->IncludeAdminFile(Loc::getMessage("LEADSPACE_GISREVIEWS_INSTALL_TITLE"), $_SERVER["DOCUMENT_ROOT"] . "/bitrix/modules/leadspace.gisreviewsslider/install/step2.php");
		}
	}

	// Удаление файлов
	function unInstallFiles() {
		$res = true;
		$resMsg = "";
		\Bitrix\Main\IO\Directory::deleteDirectory($this->COMPONENTS_PATH . "leadspace/gisreviewsslider");
//		\Bitrix\Main\IO\Directory::deleteDirectory($_SERVER["DOCUMENT_ROOT"] . "/local/leadspace");

        if (is_dir($this->COMPONENTS_PATH . $this->MODULE_ID))
	        $res = DeleteDirFilesEx($this->COMPONENTS_PATH . $this->MODULE_ID);
        if (!$res)
	        $resMsg = Loc::getMessage("LEADSPACE_GISREVIEWS_UNINSTALL_ERROR_FILES_COM");
        if ($resMsg) {
	        $this->setResponse(false, $resMsg);
	        return false;
        }
        $this->setResponse(true);
        return true;
    }


	// Удаление баз данных и параметров
	function unInstallDB() {
		Loader::includeModule($this->MODULE_ID);

		Application::getConnection(\leadspace\gisreviewsslider\ReviewsTable::getConnectionName())->queryExecute('DROP TABLE IF EXISTS ' . Base::getInstance("\leadspace\gisreviewsslider\ReviewsTable")->getDBTableName());
		Application::getConnection(\leadspace\gisreviewsslider\CompanyTable::getConnectionName())->queryExecute('DROP TABLE IF EXISTS ' . Base::getInstance("\leadspace\gisreviewsslider\CompanyTable")->getDBTableName());

		Option::delete($this->MODULE_ID);
	}

	// Удаление агентов
	function unInstallAgents() {
		\CAgent::RemoveModuleAgents($this->MODULE_ID);
	}

	// Основная функция удаления
	function DoUninstall() {
		global $APPLICATION;

		$context = Application::getInstance()->getContext();
		$request = $context->getRequest();

		if ($request["step"] < 2) {
			if (file_exists($_SERVER["DOCUMENT_ROOT"] . "/local/modules/leadspace.gisreviewsslider/install/unstep1.php"))
				$APPLICATION->IncludeAdminFile(Loc::getMessage("LEADSPACE_GISREVIEWS_UNINSTALL_TITLE"), $_SERVER["DOCUMENT_ROOT"] . "/local/modules/leadspace.gisreviewsslider/install/unstep1.php");
			else
				$APPLICATION->IncludeAdminFile(Loc::getMessage("LEADSPACE_GISREVIEWS_UNINSTALL_TITLE"), $_SERVER["DOCUMENT_ROOT"] . "/bitrix/modules/leadspace.gisreviewsslider/install/unstep1.php");
		} elseif ($request["step"] == 2) {

			$this->unInstallEvents();
			$this->unInstallAgents();
			if ($request["save_data"] != "Y")
				$this->unInstallDB();

			if (!$this->unInstallFiles())
				$APPLICATION->ThrowException($this->arResponse["MESSAGE"]);
			ModuleManager::unRegisterModule($this->MODULE_ID);
			if (file_exists($_SERVER["DOCUMENT_ROOT"] . "/local/modules/leadspace.gisreviewsslider/install/unstep2.php"))
				$APPLICATION->IncludeAdminFile(Loc::getMessage("LEADSPACE_GISREVIEWS_UNINSTALL_TITLE"), $_SERVER["DOCUMENT_ROOT"] . "/local/modules/leadspace.gisreviewsslider/install/unstep2.php");
			else
				$APPLICATION->IncludeAdminFile(Loc::getMessage("LEADSPACE_GISREVIEWS_UNINSTALL_TITLE"), $_SERVER["DOCUMENT_ROOT"] . "/bitrix/modules/leadspace.gisreviewsslider/install/unstep2.php");
		}
	}


}

?>