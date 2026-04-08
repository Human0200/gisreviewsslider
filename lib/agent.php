<?php

namespace leadspace\gisreviewsslider;

use \Bitrix\Main\Localization\Loc;
use \Bitrix\Main\Loader;
use \Bitrix\Main\Entity\Base;
use \Bitrix\Main\Application;
use \Bitrix\Main\ModuleManager;
use \Bitrix\Main\EventManager;
use \Bitrix\Main\Config\Option;
use \Bitrix\Main\Type;

class Agent {

    static public function superGisAgent() {
        require_once $_SERVER["DOCUMENT_ROOT"] . "/local/leadspace/gisreviewsslider/agent.php";
        return "\leadspace\gisreviewsslider\Agent::superGisAgent();";
    }

}