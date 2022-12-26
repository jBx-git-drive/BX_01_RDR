<?php

use Bitrix\Main\Loader;

function isToken() {
    $userPaidProdsList = userPaidProdsList();
//    debug($userPaidProdsList);

    foreach ($userPaidProdsList as $product) {
        $oBlockProducts[$product]['ID'] = $product;
        $oBlockProducts[$product]['IBLOCK'] = CIBlockElement::GetIBlockByID($product);
    }
//    debug($oBlockProducts);

    foreach ($oBlockProducts as $prod) {

        $token = getPropertyByID($prod['IBLOCK'], $prod['ID'], array('PROPERTY_TOKEN'));

        if(isset($token['NOP']) || empty($token['PROPERTY_TOKEN_VALUE'])) {
            continue;
        } else {
            $prodProps[] = $prod['ID'];
//            $prodProps[$prod['ID']] = 'Y';
//            $prodProps[$prod['ID']]['TOKEN'] = 'Y';
        }
/*        $prodProps[$prod['ID']]['ID'] = $prod['ID'];
        $prodProps[$prod['ID']]['IBLOCK'] = $prod['IBLOCK'];*/

//    $prodProps[$prod['ID']]['TOKEN'] = getPropertyByID($prod['IBLOCK'], $prod['ID'], array('PROPERTY_TOKEN'));
//    debug(getPropertyByID($prod['IBLOCK'], $prod['ID'], array('PROPERTY_TOKEN')));
    }

//    debug($prodProps);

    return $prodProps;

}


function getTokenJSON($id = '') {
    global $USER;

    if (Loader::includeModule('iblock')) {

        $arFilter = array(
            "IBLOCK_ID" => 46,
            "NAME" => $id,
            "PROPERTY_USER_ID" => $USER->GetID(),
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
        );
        $res = CIBlockElement::GetList(array(), $arFilter, false, array("nPageSize" => 50), $arSelect);
        while ($ob = $res->GetNextElement()) {
            $arFields[] = $ob->GetFields();
        }


        foreach ($arFields as $key => $field) {

            $rsUser = CUser::GetByID($field['PROPERTY_USER_ID_VALUE']);
            $arUser = $rsUser->Fetch();

            $arResult['TOKENS'][$key]['ID'] = $field['ID'];
            $arResult['TOKENS'][$key]['NAME'] = $field['NAME'];
            $arResult['TOKENS'][$key]['ITEM_NAME'] = getNameByID($field['PROPERTY_ITEM_ID_VALUE']);
            $arResult['TOKENS'][$key]['ITEM_ID'] = $field['PROPERTY_ITEM_ID_VALUE'];
//            $arResult['TOKENS'][$key]['PARENT_ID'] = CCatalogSku::GetProductInfo($field['PROPERTY_ITEM_ID_VALUE'])['ID'];
//            $arResult['TOKENS'][$key]['PARENT_NAME'] = getNameByID($arResult['TOKENS'][$key]['PARENT_ID']);
            $arResult['TOKENS'][$key]['ACTIVE_FROM'] = $field['DATE_ACTIVE_FROM'];
//            $arResult['TOKENS'][$key]['ACTIVE_TO'] = $field['DATE_ACTIVE_TO'];
            $arResult['TOKENS'][$key]['USER_ID'] = $field['PROPERTY_USER_ID_VALUE'];
            $arResult['TOKENS'][$key]['USER_IP'] = $field['PROPERTY_USER_IP_VALUE'];
            $arResult['TOKENS'][$key]['USER_INN'] = $arUser['UF_INN'];
//            $arResult['TOKENS'][$key]['TOKEN_PERIOD'] = $field['PROPERTY_TOKEN_PERIOD_VALUE'];
            $arResult['TOKENS'][$key]['TOKEN_FINISH'] = $field['PROPERTY_TOKEN_FINISH_VALUE'];

            $arResult['TOKENS'][$key]['BLOCK_REASON'] = $field['PROPERTY_BLOCK_REASON_VALUE'];

            if ((new DateTime() < new DateTime($field['PROPERTY_TOKEN_FINISH_VALUE'])) and (empty($arResult['TOKENS'][$key]['BLOCK_REASON']))) {
                $arResult['TOKENS'][$key]['STATUS'] = 'ACTIVE';
            } else $arResult['TOKENS'][$key]['STATUS'] = 'BLOCKED';

            if ($arResult['TOKENS'][$key]['STATUS'] == 'BLOCKED') {
                $arResult['TOKENS'][$key]['ACTIVE'] = 'N';
            } else $arResult['TOKENS'][$key]['ACTIVE'] = 'Y';

            $objDateTime = new DateTime($field['PROPERTY_TOKEN_FINISH_VALUE']);

            $arResult['TOKENS'][$key]['TIMESTAMP'] = $objDateTime->getTimestamp();



        }

        foreach ($arResult['TOKENS'] as $keys => $fields) {
            foreach ($fields as $key => $field) {
//                if (strstr($key, '~') or strpos($key, '_ID')) {
                if (strstr($key, '~')) {
                    unset($key);
                } else {
                    $resFields[$keys][$key] = $field;
                }
            }
        }

        echo json_encode($resFields);

    }

}


function getTokensForUser() {
    global $USER;


    $arFilter = array(
        "IBLOCK_ID" => 46,
        "ACTIVE" => 'Y',
        "PROPERTY_USER_ID" => $USER->GetID(),
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
    );
    $res = CIBlockElement::GetList(array(), $arFilter, false, array("nPageSize" => 50), $arSelect);
    while ($ob = $res->GetNextElement()) {
        $arFields[] = $ob->GetFields();
    }

    foreach ($arFields as $key => $field) {

        $arToken[$key]['ID'] = $field['ID'];
        $arToken[$key]['NAME'] = $field['NAME'];
        $arToken[$key]['ITEM_NAME'] = getNameByID($field['PROPERTY_ITEM_ID_VALUE']);
//        $arToken[$key]['LAST_SOLD_ID'] = $field['PROPERTY_ITEM_ID_VALUE'];
        $arToken[$key]['ITEM_ID'] = $field['PROPERTY_ITEM_ID_VALUE'];
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

    return $arToken;
}

