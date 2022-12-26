<?php

AddEventHandler("main", "OnEndBufferContent", "deleteKernelCss");
function deleteKernelCss(&$content)
{
    global $USER, $APPLICATION;
    if (strpos($APPLICATION->GetCurDir(), "/bitrix/") !== false) return;
    if ($APPLICATION->GetProperty("save_kernel") == "Y") return;
    $arPatternsToRemove = array(
        '/<link.+?href=".+?bitrix\/css\/main\/bootstrap.css[^"]+"[^>]+>/',
        '/<link.+?href=".+?bitrix\/css\/main\/bootstrap.min.css[^"]+"[^>]+>/',
    );
    $content = preg_replace($arPatternsToRemove, "", $content);
    $content = preg_replace("/\n{2,}/", "\n\n", $content);
}


AddEventHandler("main", "OnBeforeEventAdd", array("FbackForm", "BeforeSend"));

class FbackForm
{

    function BeforeSend(&$arFields)
    {
        /*запись данных в инфоблок*/
        CModule::IncludeModule("iblock");

        $element = new CIBlockElement;

        $PROP = array();
        $PROP[171] = htmlspecialchars($_POST['phone_number'], ENT_QUOTES, 'UTF-8');
        $PROP[172] = htmlspecialchars($_POST['day_week'], ENT_QUOTES, 'UTF-8');
        $PROP[173] = htmlspecialchars($_POST['time_hour'], ENT_QUOTES, 'UTF-8');

        $arLoadProductArray = array(
            "IBLOCK_SECTION_ID" => false,
            "IBLOCK_ID" => 38,
            "NAME" => "Заказ звонка от " . date("d.m.y H:i"),
            "ACTIVE" => "Y",
            "PROPERTY_VALUES" => $PROP,
            "PREVIEW_TEXT" => $_POST['MESSAGE'],
        );

        $idElement = $element->Add($arLoadProductArray);
    }
}

AddEventHandler("main", "OnBeforeEventAdd", array("FbackForm1", "BeforeSend1"));

class FbackForm1
{

    function BeforeSend1(&$arFields)
    {
        /*запись данных в инфоблок*/
        CModule::IncludeModule("iblock");
        $element = new CIBlockElement;

        $PROP = array();
        $PROP[174] = htmlspecialchars($_POST['user_name'], ENT_QUOTES, 'UTF-8');
        $PROP[175] = htmlspecialchars($_POST['user_email'], ENT_QUOTES, 'UTF-8');
        $PROP[176] = htmlspecialchars($_POST['MESSAGE'], ENT_QUOTES, 'UTF-8');

        $arLoadProductArray = array(
            "IBLOCK_SECTION_ID" => false,
            "IBLOCK_ID" => 39,
            "NAME" => "Сообщение из формы обратной связи " . date("d.m.y H:i"),
            "ACTIVE" => "Y",
            "PROPERTY_VALUES" => $PROP,
            "PREVIEW_TEXT" => $_POST['MESSAGE'],
        );

        $idElement = $element->Add($arLoadProductArray);
    }
}

AddEventHandler("main", "OnBeforeEventAdd", array("PetitionForm", "PetitionSend"));

class PetitionForm
{

    function PetitionSend(&$arFields)
    {
        /*запись данных в инфоблок*/
        CModule::IncludeModule("iblock");
        $element = new CIBlockElement;

        $PROP = array();
        $PROP[227] = htmlspecialchars($_POST['user_name'], ENT_QUOTES, 'UTF-8');
        $PROP[225] = htmlspecialchars($_POST['user_email'], ENT_QUOTES, 'UTF-8');
        $PROP[226] = htmlspecialchars($_POST['MESSAGE'], ENT_QUOTES, 'UTF-8');

        $arLoadProductArray = array(
            "IBLOCK_SECTION_ID" => false,
            "IBLOCK_ID" => 44,
            "NAME" => "Жалоба директору " . date("d.m.y H:i"),
            "ACTIVE" => "Y",
            "PROPERTY_VALUES" => $PROP,
            "PREVIEW_TEXT" => $_POST['MESSAGE'],
        );

        $idElement = $element->Add($arLoadProductArray);
    }
}

AddEventHandler("main", "OnBeforeEventAdd", array("ErrorForm", "ErrorSend"));

class ErrorForm
{

    function ErrorSend(&$arFields)
    {
        /*запись данных в инфоблок*/
        CModule::IncludeModule("iblock");
        $element = new CIBlockElement;

        $PROP = array();
        $PROP[230] = htmlspecialchars($_POST['user_name'], ENT_QUOTES, 'UTF-8');
        $PROP[228] = htmlspecialchars($_POST['user_email'], ENT_QUOTES, 'UTF-8');
        $PROP[229] = htmlspecialchars($_POST['MESSAGE'], ENT_QUOTES, 'UTF-8');
        $PROP[231] = htmlspecialchars($_POST['error_type'], ENT_QUOTES, 'UTF-8');

        $arLoadProductArray = array(
            "IBLOCK_SECTION_ID" => false,
            "IBLOCK_ID" => 45,
            "NAME" => "Сообщение об ошибке - " . date("d.m.y H:i"),
            "ACTIVE" => "Y",
            "PROPERTY_VALUES" => $PROP,
            "PREVIEW_TEXT" => $_POST['MESSAGE'],
        );

        $idElement = $element->Add($arLoadProductArray);
    }
}

AddEventHandler("main", "OnBeforeEventAdd", array("FbackFormService", "BeforeSendService"));

class FbackFormService
{

    function BeforeSendService(&$arFields)
    {
        /*запись данных в инфоблок*/
        CModule::IncludeModule("iblock");
        $element = new CIBlockElement;

        $PROP = array();
        $PROP[189] = htmlspecialchars($_POST['user_email'], ENT_QUOTES, 'UTF-8');
        $PROP[190] = htmlspecialchars($_POST['MESSAGE'], ENT_QUOTES, 'UTF-8');
        $PROP[191] = htmlspecialchars($_POST['user_name'], ENT_QUOTES, 'UTF-8');
        $PROP[192] = htmlspecialchars($_POST['service_name'], ENT_QUOTES, 'UTF-8');

        $arLoadProductArray = array(
            "IBLOCK_SECTION_ID" => false,
            "IBLOCK_ID" => 40,
            "NAME" => "Заказ услуги " . htmlspecialchars($_POST['service_name']),
            "ACTIVE" => "Y",
            "PROPERTY_VALUES" => $PROP,
        );

        $idElement = $element->Add($arLoadProductArray);
    }
}


AddEventHandler("main", "OnBeforeUserLogin", "DoBeforeUserLoginHandler");
function DoBeforeUserLoginHandler(&$arFields)
{
    $userLogin = $_POST["USER_LOGIN"];
    if (isset($userLogin)) {
        $isEmail = strpos($userLogin, "@");
        if ($isEmail > 0) {
            $arFilter = array("EMAIL" => $userLogin);
            $rsUsers = CUser::GetList(($by = "id"), ($order = "desc"), $arFilter);
            if ($res = $rsUsers->Fetch()) {
                if ($res["EMAIL"] == $arFields["LOGIN"])
                    $arFields["LOGIN"] = $res["LOGIN"];
            }
        }
    }
}

AddEventHandler("main", "OnBeforeUserRegister", "OnBeforeUserUpdateHandler");
AddEventHandler("main", "OnBeforeUserUpdate", "OnBeforeUserUpdateHandler");
function OnBeforeUserUpdateHandler(&$arFields)
{
    $arFields["LOGIN"] = $arFields["EMAIL"];
    return $arFields;
}


AddEventHandler("currency", "CurrencyFormat", "myFormat");
function myFormat($fSum, $strCurrency)
{
    return number_format($fSum, 0, '.', ' ') . ' ₽';
}


$eventManager = \Bitrix\Main\EventManager::getInstance();
$eventManager->addEventHandler('sale', 'OnSaleOrderSaved', 'OnSaleOrderSavedHandler');

function OnSaleOrderSavedHandler(\Bitrix\Main\Event $event)
{

    $order = $event->getParameter("ENTITY");
    $oldValues = $event->getParameter("VALUES");
    if (!$order->getField('PAYED') || !$oldValues['PAYED'] || !(($order->getField('PAYED') == 'Y') && ($oldValues['PAYED'] == 'N')))
        return;


//Получаем информацию по заказу

    $orderID = $order->getId();

    CModule::IncludeModule('sale');
    $res = CSaleBasket::GetList(array(), array("ORDER_ID" => $orderID)); // ID заказа
    $json_product = array();

    while ($arItem = $res->Fetch()) {


        $iblock = CIBlockElement::GetIBlockByID($arItem['PRODUCT_ID']);

        if ($iblock == '47') {
            $getTime = getPropertyByID(47, $arItem['PRODUCT_ID'], array('PROPERTY_RANGE'));
            $tokenPeriod = periodInDays($getTime['PROPERTY_RANGE_VALUE']);
        } else {
            $getTime = getPropertyByID(51, $arItem['PRODUCT_ID'], array('PROPERTY_TIME'));
            $tokenPeriod = periodInDays($getTime['PROPERTY_TIME_VALUE']);
        }


        $json[] = array(
            'name' => $arItem['NAME'],
            'user_id' => $order->getUserId(),
            'id' => $arItem['PRODUCT_ID'],
            'parent_id' => CCatalogSku::GetProductInfo($arItem['PRODUCT_ID'])['ID'],
            'token_days' => $tokenPeriod,  // получить количество дней из торгового предложения
            'price' => $arItem['PRICE'],
            'quantity' => $arItem['QUANTITY']
        );
    }


//Получение списка токенов
    $arFilter = array(
        "IBLOCK_ID" => 46,
        "ACTIVE" => 'Y',
        "PROPERTY_USER_ID" => $order->getUserId(),
    );
    $arSelect = array(
        "ID",
        "NAME",
        "ACTIVE",
        "DATE_ACTIVE_FROM",
        "DATE_ACTIVE_TO",
        "PROPERTY_USER_ID",
        "PROPERTY_ITEM_ID",
        "PROPERTY_USER_IP",
        "PROPERTY_USER_INN",
        "PROPERTY_TOKEN_PERIOD",
        "PROPERTY_TOKEN_FINISH",
        "PROPERTY_BLOCK_REASON",
        "PROPERTY_LAST_SOLD_ID",
    );
    $res = CIBlockElement::GetList(array(), $arFilter, false, array("nPageSize" => 50), $arSelect);
    while ($ob = $res->GetNextElement()) {
        $arFields[] = $ob->GetFields();
    }
    foreach ($arFields as $key => $field) {

        $arToken[$key]['ID'] = $field['ID'];
        $arToken[$key]['NAME'] = $field['NAME'];
        $arToken[$key]['ITEM_NAME'] = getNameByID($field['PROPERTY_ITEM_ID_VALUE']);
        $arToken[$key]['LAST_SOLD_ID'] = $field['PROPERTY_LAST_SOLD_ID_VALUE'];
        $arToken[$key]['ITEM_ID'] = $field['PROPERTY_ITEM_ID_VALUE'];
        $arToken[$key]['PARENT_ID'] = CCatalogSku::GetProductInfo($field['PROPERTY_ITEM_ID_VALUE'])['ID'];
        $arToken[$key]['PARENT_NAME'] = getNameByID($arToken[$key]['PARENT_ID']);
        $arToken[$key]['ACTIVE_FROM'] = $field['DATE_ACTIVE_FROM'];
        $arToken[$key]['ACTIVE_TO'] = $field['DATE_ACTIVE_TO'];
        $arToken[$key]['USER_ID'] = $field['PROPERTY_USER_ID_VALUE'];
        $arToken[$key]['USER_IP'] = $field['PROPERTY_USER_IP_VALUE'];
        $arToken[$key]['USER_INN'] = $field['PROPERTY_USER_INN_VALUE'];
        $arToken[$key]['TOKEN_PERIOD'] = $field['PROPERTY_TOKEN_PERIOD_VALUE'];
        $arToken[$key]['TOKEN_FINISH'] = $field['PROPERTY_TOKEN_FINISH_VALUE'];

        $arToken[$key]['BLOCK_REASON'] = $field['PROPERTY_BLOCK_REASON_VALUE'];

        if ((new DateTime() < new DateTime($field['PROPERTY_TOKEN_FINISH_VALUE'])) and (empty($arToken[$key]['BLOCK_REASON']))) {
            $arToken[$key]['STATUS'] = 'ACTIVE';
        } else $arToken[$key]['STATUS'] = 'BLOCKED';

        if ($arToken[$key]['STATUS'] == 'BLOCKED') {
            $arToken[$key]['ACTIVE'] = 'N';
        } else $arToken[$key]['ACTIVE'] = 'Y';

        $objDateTime = new DateTime($field['PROPERTY_TOKEN_FINISH_VALUE']);

        $arToken[$key]['TIMESTAMP'] = $objDateTime->getTimestamp();
    }


//Итоговый токен для обновления
    foreach ($json as $orderItems) {
        foreach ($arToken as $tokenItems) {
            if (
                ($orderItems['parent_id'] == $tokenItems['ITEM_ID']) and
                ($orderItems['user_id'] == $tokenItems['USER_ID'])
            ) {
                $updateItem = $tokenItems;
            }
        }
        $updateItem['new_id'] = $orderItems['id'];
    }


    if (CModule::IncludeModule("iblock"))

        if (new DateTime() < new DateTime(date('Y-m-d H:i:s', $updateItem['TIMESTAMP']))) {
            $time = strtotime("+$tokenPeriod days", $updateItem['TIMESTAMP']);
        } else {
            $time = strtotime("+$tokenPeriod days", time());
        }

    $lostTokenDays = round(abs($time - time()) / 86400, 0);
    $iblockUpdateId = '46';

    $sendFields = [
        'ITEM_ID' => $updateItem['ITEM_ID'],
        'PARENT_ID' => $updateItem['ITEM_ID'],
        'LAST_SOLD_ID' => $updateItem['new_id'],
        'USER_ID' => $updateItem['USER_ID'],
        'USER_IP' => $updateItem['USER_IP'],
        'USER_INN' => $updateItem['USER_INN'],
        'TOKEN_PERIOD' => $lostTokenDays,
        'TOKEN_FINISH' => ConvertTimeStamp($time, "FULL"),
        'TIMESTAMP' => $time,
    ];


    $el = new CIBlockElement;

    $fields = [
        "ID" => $updateItem['ID'],
        "NAME" => $updateItem['NAME'],
        "IBLOCK_ID" => $iblockUpdateId,
        "ACTIVE" => "Y",
        "DATE_ACTIVE_FROM" => $updateItem['DATE_ACTIVE_FROM'],
        "DATE_ACTIVE_TO" => ConvertTimeStamp($time, "FULL"),
        "PROPERTY_VALUES" => $sendFields,
    ];

    $res = $el->Update($updateItem['ID'], $fields);

}

AddEventHandler("iblock", "OnAfterIBlockElementAdd", Array("OnAfterArticleAddComment", "OnAfterIBlockElementAddHandlerLast1"));
class OnAfterArticleAddComment {
    function OnAfterIBlockElementAddHandlerLast1(&$arFields) {
        if ($arFields["IBLOCK_ID"] == 22) {

            $headers = "MIME-Version: 1.0\r\n";
            $headers .= "Content-Type: text/html; charset=UTF-8\r\n";

            $message = 'Текст отзыва: '.$arFields['PREVIEW_TEXT'];
            mail('support@1c-code.ru', 'Добавлен коммкентарий на сайт', $message, $headers);
        }
    }
}

AddEventHandler("iblock", "OnAfterIBlockElementAdd", Array("OnAfterArticleAddReview", "OnAfterIBlockElementAddHandlerLast2"));
class OnAfterArticleAddReview {
    function OnAfterIBlockElementAddHandlerLast2(&$arFields) {
        if ($arFields["IBLOCK_ID"] == 23) {

            $headers = "MIME-Version: 1.0\r\n";
            $headers .= "Content-Type: text/html; charset=UTF-8\r\n";

            $message = 'Текст отзыва: '.$arFields['PREVIEW_TEXT'];
            mail('support@1c-code.ru', 'Добавлен отзыв на сайт', $message, $headers);
        }
    }
}

// этот обработчик можно добавить например в init.php
AddEventHandler('form', 'onBeforeResultAdd', 'antiSpam');

function antiSpam($WEB_FORM_ID, &$arFields, &$arrVALUES)
{
    global $APPLICATION;
    if (isset($_REQUEST["ANTI_SPAM"]) && $_REQUEST["ANTI_SPAM_VALUE"] !== "") {
        $APPLICATION->ThrowException('Проверка на антиспам не пройдена!');
    }
}