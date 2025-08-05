<?php

namespace leadspace\yandexreviewsslider;


use \Bitrix\Main\Localization\Loc;
use \Bitrix\Main\Loader;
use \Bitrix\Main\Entity\Base;
use \Bitrix\Main\Application;
use \Bitrix\Main\ModuleManager;
use \Bitrix\Main\EventManager;
use \Bitrix\Main\Config\Option;
use \Bitrix\Main\Type;

class Agent {

    static public function superAgent() {
	require_once $_SERVER["DOCUMENT_ROOT"] . "/local/leadspace/yandexreviewsslider/agent.php";

        return "\leadspace\yandexreviewsslider\Agent::superAgent();";

    }

}
