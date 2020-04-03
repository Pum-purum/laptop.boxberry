<?php

namespace Laptop;

use Bitrix\Main\Localization\Loc;
use Bitrix\Main;
use Bitrix\Main\EventResult;

class Delivery {
    public static function onAfterUpdate(\Bitrix\Main\Event $event) {
        $ID = $event->getParameters()['id']['ID'];
        $fields = $event->getParameters()['fields'];

        $elemDataRaw = \Laptop\DeliveryOptionsTable::getlist(array('filter' => array('PROFILE_ID' => $ID),
                                                                   'select' => array('ID')));
        if ($elemData = $elemDataRaw->fetch()) {
            \Laptop\DeliveryOptionsTable::update($elemData['ID'], array(
                "PROFILE_ID"         => $ID,
                "IS_FIX_PRICE"       => $fields['CONFIG']['MAIN']['IS_FIX_PRICE'] ? 'Y' : 'N',
                "IS_FIX_DEADLINE"    => $fields['CONFIG']['MAIN']['IS_FIX_DEADLINE'] ? 'Y' : 'N',
                "FIX_PRICE_VALUE"    => (int)$fields['CONFIG']['MAIN']['FIX_PRICE_VALUE'] ?? 0,
                "FIX_DEADLINE_VALUE" => $fields['CONFIG']['MAIN']['FIX_DEADLINE_VALUE'] ?? 0,
            ));
        } else {
            \Laptop\DeliveryOptionsTable::add(array(
                "PROFILE_ID"         => $ID,
                "IS_FIX_PRICE"       => $fields['CONFIG']['MAIN']['IS_FIX_PRICE'] ? 'Y' : 'N',
                "IS_FIX_DEADLINE"    => $fields['CONFIG']['MAIN']['IS_FIX_DEADLINE'] ? 'Y' : 'N',
                "FIX_PRICE_VALUE"    => (int)$fields['CONFIG']['MAIN']['FIX_PRICE_VALUE'] ?? 0,
                "FIX_DEADLINE_VALUE" => $fields['CONFIG']['MAIN']['FIX_DEADLINE_VALUE'] ?? 0,
            ));
        }
        return new Main\EventResult(
            Main\EventResult::SUCCESS,
            false,
            'laptop.boxberry');

    }

    function addTab(&$form) {
        $instance = \Bitrix\Main\Application::getInstance();
        $context = $instance->getContext();
        $request = $context->getRequest();
        if ($GLOBALS["APPLICATION"]->GetCurPage() == "/bitrix/admin/sale_delivery_service_edit.php" && $request->get('PARENT_ID')) {

            $elemDataRaw = \Laptop\DeliveryOptionsTable::getlist(array('filter' => array('PROFILE_ID' => $request->get('ID')),
                                                                       'select' => array('*')));
            $elemData = $elemDataRaw->fetch();
            $fixPriceChecked = $elemData['IS_FIX_PRICE'] == 'Y' ? 'checked' : '';
            $fixDeadlineChecked = $elemData['IS_FIX_DEADLINE'] == 'Y' ? 'checked' : '';

            $form->tabs[] = array("DIV"     => "fix_price_and_deadline_edit",
                                  "TAB"     => "Фикс.цена и срок",
                                  "ICON"    => "main_user_edit",
                                  "TITLE"   => "Фикс.цена и срок",
                                  "CONTENT" =>
                                      '<tr><td width="40%" class="adm-detail-content-cell-l">Фикс.цена:</td><td width="60%" class="adm-detail-content-cell-r"><input type="checkbox" name="CONFIG[MAIN][IS_FIX_PRICE]" ' . $fixPriceChecked . ' class="adm-designed-checkbox" id="fix_price_checkbox_1"><label class="adm-designed-checkbox-label" for="fix_price_checkbox_1" ' . $fixPriceChecked . '></label></td></tr><tr><td width="40%" class="adm-detail-content-cell-l">Фикс.цена:</td><td width="60%" class="adm-detail-content-cell-r"><input type="text" name="CONFIG[MAIN][FIX_PRICE_VALUE]" value="' . $elemData['FIX_PRICE_VALUE'] . '"></td></tr><tr><td width="40%" class="adm-detail-content-cell-l">Фикс.срок:</td><td width="60%" class="adm-detail-content-cell-r"><input type="checkbox" name="CONFIG[MAIN][IS_FIX_DEADLINE]" class="adm-designed-checkbox" id="fix_deadline_checkbox_1" ' . $fixDeadlineChecked . '><label class="adm-designed-checkbox-label" for="fix_deadline_checkbox_1"></label></td></tr><tr><td width="40%" class="adm-detail-content-cell-l">Фикс.срок:</td><td width="60%" class="adm-detail-content-cell-r"><input type="text" name="CONFIG[MAIN][FIX_DEADLINE_VALUE]" value="' . $elemData['FIX_DEADLINE_VALUE'] . '"></td></tr>'
            );
        }
    }
    public function onCalculate(\Bitrix\Main\Event $event) {
        $baseResult = $event->getParameter('RESULT');
        $shipment = $event->getParameter('SHIPMENT');

        //Вот тут я написал бы кастомизацию стоимости и сроков доставки, если бы модуль Boxberry у меня заработал

        $event->addResult(
            new EventResult(
                EventResult::SUCCESS, array('RESULT' => $baseResult)
            )
        );
    }
}
