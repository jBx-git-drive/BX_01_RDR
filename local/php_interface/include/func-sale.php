<?php

CModule::IncludeModule("sale");
$cntBasketItems = CSaleBasket::GetList(
    array(),
    array(
        "FUSER_ID" => CSaleBasket::GetBasketUserID(),
        "LID" => SITE_ID,
        "ORDER_ID" => "NULL"
    ),
    array()
);
if ($cntBasketItems === 0) {
    // Если в корзине нет товаров
}

if (CModule::IncludeModule("sale"))
{
    $arBasketItems = array();
    $dbBasketItems = CSaleBasket::GetList(
        array("NAME" => "ASC","ID" => "ASC"),
        array("FUSER_ID" => CSaleBasket::GetBasketUserID(), "LID" => SITE_ID, "ORDER_ID" => "NULL"),
        false,
        false,
        array("ID","MODULE","PRODUCT_ID","QUANTITY","CAN_BUY","PRICE"));
    while ($arItems=$dbBasketItems->Fetch())
    {
        $arItems=CSaleBasket::GetByID($arItems["ID"]);
        $arBasketItems[]=$arItems;
        $cart_num+=$arItems['QUANTITY'];
        $cart_sum+=$arItems['PRICE']*$arItems['QUANTITY'];
    }
    if (empty($cart_num))
        $cart_num="0";
    if (empty($cart_sum))
        $cart_sum="0";

}

function getOffersByID($torgEl, $iblockId) {
    if (CCatalogSKU::IsExistOffers($torgEl, $iblockId))
        $arSKU = CCatalogSKU::getOffersList($torgEl, 0, array('ACTIVE' => 'Y'), array('NAME', 'SORT', 'CATALOG_PRICE_1'), array());

    foreach ($arSKU as $skus) {
        foreach ($skus as $sku) {
            $offers[$sku['SORT']] = $sku;
        }
    }
    ksort($offers);

    return $offers;
}


/*$eventManager = \Bitrix\Main\EventManager::getInstance();
$eventManager->addEventHandler('sale', 'OnSaleOrderSaved', 'OnSaleOrderSavedHandler');

function OnSaleOrderSavedHandler(\Bitrix\Main\Event $event) {
    $order = $event->getParameter('ENTITY');
    $oldValues = $event->getParameter('VALUES');
    $isNew = $event->getParameter('IS_NEW');

    debug($order);
}*/