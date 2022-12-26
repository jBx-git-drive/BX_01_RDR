<?php

function userData($userId)
{
    $rsUser = CUser::GetByID($userId);
    $arUser = $rsUser->Fetch();
    return $arUser['NAME'] . ' ' . $arUser['LAST_NAME'];
}

function getUserData($userId)
{
    $filter = array("ID" => $userId);
    $rsUser = CUser::GetList(($by = "ID"), ($order = "ID"), $filter);
    $currUser = $rsUser->Fetch();

    return $currUser;
}

function countCommentsList($item, $iblockType)
{
    $resElemCnt = CIBlockElement::GetList(
        false,      // сортировка
        array(
            'IBLOCK_TYPE' => $iblockType,
            'PROPERTY_ELEMENT_ID' => $item,
            'ACTIVE' => 'Y',
        ),   // фильтрация
        false,      // параметры группировки полей
        false,      // параметры навигации
        array("ID") // поля для выборки
    );

    return $resElemCnt->SelectedRowsCount();
}

function getSectionList($filter, $select)
{
    $dbSection = CIBlockSection::GetList(
        array(
            'LEFT_MARGIN' => 'ASC',
        ),
        array_merge(
            array(
                'ACTIVE' => 'Y',
                'GLOBAL_ACTIVE' => 'Y'
            ),
            is_array($filter) ? $filter : array()
        ),
        false,
        array_merge(
            array(
                'ID',
                'IBLOCK_SECTION_ID'
            ),
            is_array($select) ? $select : array()
        )
    );

    while ($arSection = $dbSection->GetNext(true, false)) {

        $SID = $arSection['ID'];
        $PSID = (int)$arSection['IBLOCK_SECTION_ID'];

        $arLincs[$PSID]['CHILDS'][$SID] = $arSection;

        $arLincs[$SID] = &$arLincs[$PSID]['CHILDS'][$SID];
    }

    return array_shift($arLincs);
}

function dataElementByID($iblockID, $elementID)
{
    $resElement = \CIBlockElement::GetList(
        [],
        [
            'IBLOCK_ID' => $iblockID,
            'ID' => $elementID,
        ],
        false,
        false,
        [
            'ID',
            'IBLOCK_ID',
            'NAME',
            'PROPERTY_FILE'
        ]
    );
    if (!($element = $resElement->getNext())) {
        echo "Элемент не найден";
        return;
    } else return $element;
}

function getElementPrice($elementID)
{
    $price_result = CPrice::GetList(
        array(),
        array(
            "PRODUCT_ID" => $elementID,
            "CATALOG_GROUP_ID" => 1 // это группа цены, у меня есть как оптовые так и розничная цена
        )
    );
    while ($arPrices = $price_result->Fetch()) {
        return $arPrices["PRICE"];
    }
}

//Получить ID элемента по коду
function getElementIDbyCode($iblockID)
{
    global $APPLICATION;
    $path = parse_url($APPLICATION->GetCurPage(), PHP_URL_PATH);
    $elementCode = end(explode("/", trim($path, "/")));

    $objFindTools = new CIBlockFindTools();
    $elementID = $objFindTools->GetElementID(false, $elementCode, false, false, array("IBLOCK_ID" => $iblockID));

    return $elementID;
}

function getElementIDbyCodeWOIblock()
{
    global $APPLICATION;
    $path = parse_url($APPLICATION->GetCurPage(), PHP_URL_PATH);
    $elementCode = end(explode("/", trim($path, "/")));

    $objFindTools = new CIBlockFindTools();
    $elementID = $objFindTools->GetElementID(false, $elementCode, false, false, array("IBLOCK_ID" => $iblockID));

    return $elementID;
}

function getNameByID($id)
{
    $obElement = CIBlockElement::GetByID($id);
    if ($arEl = $obElement->GetNext())
        return $arEl['NAME'];
}

function getPropertyByID($iblockID, $itemID, $props = array())
{
    $resElement = \CIBlockElement::GetList(
        [],
        [
            'IBLOCK_ID' => $iblockID,
            'ID' => $itemID,
        ],
        false,
        false,
        $props
    );

    if (!($element = $resElement->getNext())) {
        echo "Элемент не найден";
        return;
    } else {
        return $element;
    }
}

function getIDFromSectionID($iblockid, $sectionId, $code = false)
{
    $arSelect = array("ID", "IBLOCK_ID", "NAME");
    $arFilter = array(
        "IBLOCK_ID" => $iblockid,
        //	'ACTIVE' => 'Y', // активность
        "SECTION_ID" => $sectionId,     // нужная секция
    );

    $res = CIBlockElement::GetList(array("SORT" => "ASC"), $arFilter, false, array("nPageSize" => 50), $arSelect);
    $mass_rez = array();
    while ($ob = $res->GetNextElement()) {
        //получаем поля (которые указали в $arSelect)
        $arFields[] = $ob->GetFields();
    }

    if ($code) {
        foreach ($arFields as $codeField) {
            $arFieldsCode[] = $codeField['ID'];
        }
        return $arFieldsCode;
    } else return $arFields;

}

function getSectionNameByID($sectionID)
{
    $res = CIBlockSection::GetByID($sectionID);
    if ($ar_res = $res->GetNext())
        return $ar_res;
}

function getDownloadList()
{
    global $USER;

    $userPaidProdsList = userPaidProdsList();

//    debug($userPaidProdsList);

    foreach ($userPaidProdsList as $key => $selectedProd) {
        if (CCatalogSKU::IsExistOffers($selectedProd)) {
            unset($key);
        } else {
            $uniqPaidProdsList[] = $selectedProd;
        }
    }

//    debug($uniqPaidProdsList);

    foreach ($uniqPaidProdsList as $userProd) {
        $iblockID = CIBlockElement::GetIBlockByID($userProd);
        $itemID = $userProd;
        $downloadList[] = getPropertyByID($iblockID, $itemID, array(
            "ID",
            "IBLOCK_ID",
            "NAME",
            "ACTIVE",
            "DATE_ACTIVE_FROM",
            "DATE_ACTIVE_TO",
            'PROPERTY_FILE',
            'PROPERTY_SOLUTION', 'PROPERTY_TOKEN', 'PROPERTY_TIME', 'PROPERTY_RANGE',
            "PROPERTY_USER_ID",
            "PROPERTY_ITEM_ID",
            "PROPERTY_USER_IP",
            "PROPERTY_USER_INN",
            "PROPERTY_TOKEN_PERIOD",
            "PROPERTY_TOKEN_FINISH",
            "PROPERTY_BLOCK_REASON",

        ));
    }

//    debug($downloadList);

    foreach ($downloadList as $key => $list) {

        $parentDataID = CCatalogSku::GetProductInfo($list['ID'])['ID'];
        $parentDataIblock = CCatalogSku::GetProductInfo($list['ID'])['IBLOCK_ID'];
        $parentDataToken = getPropertyByID($parentDataIblock, $parentDataID, array('PROPERTY_TOKEN'))['PROPERTY_TOKEN_VALUE'];
        $parentDataSolution = getPropertyByID($parentDataIblock, $parentDataID, array('PROPERTY_SOLUTION'))['PROPERTY_SOLUTION_VALUE'];
        $dataSolution = getPropertyByID(CIBlockElement::GetIBlockByID($list['ID']), $list['ID'], array('PROPERTY_MORE_FILES'))['PROPERTY_MORE_FILES_VALUE'];


        if (CCatalogSku::GetProductInfo($list['ID'])) {
            $downloadList[$key]['SOLD_ITEM'] = $list['ID'];
            $downloadList[$key]['PROPERTY_TOKEN_VALUE'] = $parentDataToken;

            $downloadList[$key]['PARENT_PRODUCT'] = $parentDataID;
            $downloadList[$key]['PARENT_PRODUCT_NAME'] = getNameByID($downloadList[$key]['PARENT_PRODUCT']);
            $downloadList[$key]['PROPERTY_SOLUTION_VALUE'] = $dataSolution;
        }
    }

//    debug($downloadList);

    //Получение списка токенов
    $arFilter = array(
        "IBLOCK_ID" => 46,
        "ACTIVE" => 'Y',
        "PROPERTY_USER_ID" => $USER->GetID(),
    );
    $arSelect = array(
        "ID",
        "PROPERTY_ITEM_ID",
        "PROPERTY_LAST_SOLD_ID",
    );
    $res = CIBlockElement::GetList(array(), $arFilter, false, array("nPageSize" => 50), $arSelect);
    while ($ob = $res->GetNextElement()) {
        $arFields[] = $ob->GetFields();
    }

    foreach ($arFields as $key => $field) {

        if (!empty($field['PROPERTY_LAST_SOLD_ID_VALUE'])) {
            $arToken[$key]['ID'] = $field['ID'];
            $arToken[$key]['LAST_SOLD_ID'] = $field['PROPERTY_LAST_SOLD_ID_VALUE'];
            $arToken[$key]['ITEM_ID'] = $field['PROPERTY_ITEM_ID_VALUE'];
        }
    }

//    debug($arToken);

    foreach ($downloadList as $keys => $fields) {
        foreach ($fields as $key => $field) {
            if (strstr($key, '~') or strpos($key, '_VALUE_ID') or strpos($key, '_ENUM_ID')) {
                unset($key);
            } else {
                $downloadListMin[$keys][$key] = $field;
            }
        }
    }

//    debug($downloadListMin);

    foreach ($downloadListMin as $list) {
        if (!isset($list['SOLD_ITEM'])) {
            $fullList[] = $list;
        } else {
            if (!empty($arToken)) {
                foreach ($arToken as $soldItem) {
                    if ($list['SOLD_ITEM'] == $soldItem['LAST_SOLD_ID']) {
                        $fullList[] = $list;
                    } /*else if ($list['PARENT_PRODUCT'] == $soldItem['ITEM_ID']) {
                        $fullList[] = $list;
                    }*/
                }
            } else {
                $fullList[] = $list;
                break;
            }
        }
    }

//    debug($fullList);

    return $fullList;
}

function getIdByEmail($email) {
    $filter = Array("EMAIL" => $email);
    $rsUser = CUser::GetList(($by="id"), ($order="desc"), $filter);
    $arUser = $rsUser->Fetch();
    return $arUser;
}