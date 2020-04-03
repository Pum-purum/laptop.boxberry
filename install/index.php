<?php

use Bitrix\Main\Application;
use Bitrix\Main\Loader;
use Bitrix\Main\Entity\Base;
use Bitrix\Main\Localization\Loc;
use Bitrix\Main\ModuleManager;
use Bitrix\Main\EventManager;
use Bitrix\Main\Config\Option;

Loc::loadMessages(__FILE__);

class laptop_boxberry extends CModule {
    public function __construct() {
        $arModuleVersion = array();

        include __DIR__ . '/version.php';

        if (is_array($arModuleVersion) && array_key_exists('VERSION', $arModuleVersion)) {
            $this->MODULE_VERSION = $arModuleVersion['VERSION'];
            $this->MODULE_VERSION_DATE = $arModuleVersion['VERSION_DATE'];
        }
        $this->MODULE_ID = 'laptop.boxberry';
        $this->MODULE_NAME = Loc::getMessage('LAPTOP_BOXBERRY_MODULE_NAME');
        $this->MODULE_DESCRIPTION = Loc::getMessage('LAPTOP_BOXBERRY_MODULE_DESCRIPTION');
        $this->MODULE_GROUP_RIGHTS = 'Y';
        $this->PARTNER_NAME = Loc::getMessage('LAPTOP_BOXBERRY_MODULE_PARTNER_NAME');
        $this->PARTNER_URI = 'https://laptop.ru/';
        $this->NEED_MODULES = array('main', 'sale', 'up.boxberrydelivery');
    }

    public function doInstall() {
        global $APPLICATION, $moduleErrors;

        if (is_array($this->NEED_MODULES) && !empty($this->NEED_MODULES)) {
            foreach ($this->NEED_MODULES as $module) {
                if (!\Bitrix\Main\ModuleManager::isModuleInstalled($module)) {
                    $moduleErrors[] = Loc::getMessage('LAPTOP_BOXBERRY_NEED_MODULES', array('#MODULE#' => $module));
                }
            }
        }
        if (empty($moduleErrors))
            ModuleManager::registerModule($this->MODULE_ID);
        if (Loader::includeModule($this->MODULE_ID)) {
            $this->installFiles();
            $this->installDB();
            $this->installEvents();
            $this->addAgents();
        }

        $APPLICATION->IncludeAdminFile(Loc::getMessage("LAPTOP_BOXBERRY_MODULE_INSTALL"),
            dirname(__FILE__) . "/message.php");
    }

    function installFiles($arParams = array()) {
        return true;
    }

    public function installDB() {
        Loader::includeModule($this->MODULE_ID);
        if(!Application::getConnection(\Laptop\DeliveryOptionsTable::getConnectionName())->isTableExists(Base::getInstance('\Laptop\DeliveryOptionsTable')->getDbTableName())) {
            Base::getInstance('\Laptop\DeliveryOptionsTable')->createDbTable();
        }
    }

    function installEvents() {
        $eventManager = \Bitrix\Main\EventManager::getInstance();
        $eventManager->registerEventHandler("main", "OnAdminTabControlBegin", $this->MODULE_ID, "\Laptop\Delivery", "addTab");
        $eventManager->registerEventHandler("sale","\\Bitrix\Sale\Delivery\Services\::OnAfterUpdate",  $this->MODULE_ID, "\Laptop\Delivery", "onAfterUpdate");
        $eventManager->registerEventHandler("sale", 'onSaleDeliveryServiceCalculate',  $this->MODULE_ID, "\Laptop\Delivery", "onCalculate");
        return true;
    }

    function addAgents() {
        return true;
    }

    public function doUninstall() {
        if (Loader::includeModule($this->MODULE_ID)) {
            $this->unInstallFiles();
            $this->unInstallDB();
            $this->unInstallEvents();
            $this->removeAgents();
        }
        ModuleManager::unRegisterModule($this->MODULE_ID);
    }

    function UnInstallFiles() {
        return true;
    }

    public function unInstallDB() {
        Loader::includeModule($this->MODULE_ID);
        Application::getConnection(\Laptop\DeliveryOptionsTable::getConnectionName())->queryExecute('DROP TABLE IF EXISTS ' . Base::getInstance('\Laptop\DeliveryOptionsTable')->getDbTableName());
        Option::delete($this->MODULE_ID);
    }

    function unInstallEvents() {
        $eventManager = \Bitrix\Main\EventManager::getInstance();
        $eventManager->unRegisterEventHandler("main", "OnAdminTabControlBegin", $this->MODULE_ID, "\Laptop\Delivery", "addTab");
        $eventManager->unRegisterEventHandler("sale","\\Bitrix\Sale\Delivery\Services\::OnAfterUpdate",  $this->MODULE_ID, "\Laptop\Delivery", "onAfterUpdate");
        $eventManager->unRegisterEventHandler("sale", 'onSaleDeliveryServiceCalculate',  $this->MODULE_ID, "\Laptop\Delivery", "onCalculate");
        return true;
    }

    function removeAgents() {
        \CAgent::RemoveModuleAgents($this->MODULE_ID);
    }
}
