<?php


namespace Laptop;

use Bitrix\Main;
use Bitrix\Main\Config\Option;
use Bitrix\Main\Localization\Loc;
use Bitrix\Main\Entity;
use Bitrix\Main\Type\DateTime;

Loc::loadMessages(__FILE__);

class DeliveryOptionsTable extends Main\Entity\DataManager {

    public static function getTableName()
    {
        return 'laptop_delivery_options';
    }
    public static function getMap()
    {
        return array(
            new Main\Entity\IntegerField('ID', array(
                'primary' => true,
                'autocomplete' => true
            )),
            new Main\Entity\IntegerField('PROFILE_ID'),
            new Main\Entity\BooleanField('IS_FIX_PRICE', array(
                'values' => array('N', 'Y'),
                'default_value' => 'N',
            )),
            new Main\Entity\BooleanField('IS_FIX_DEADLINE', array(
                'values' => array('N', 'Y'),
                'default_value' => 'N',
            )),
            new Main\Entity\IntegerField('FIX_PRICE_VALUE', array(
                'default_value' => 0,
            )),
            new Main\Entity\StringField('FIX_DEADLINE_VALUE')
        );
    }
}