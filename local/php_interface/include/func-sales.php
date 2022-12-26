<?php

// список товаров по id заказа
function BasketItemsFull($order_id)
{
    $dbBasketItems = CSaleBasket::GetList(
        array(
            "NAME" => "ASC",
            "ID" => "ASC"
        ),
        array(
            // "FUSER_ID" => $user_id,
            "LID" => SITE_ID,
            "ORDER_ID" => $order_id,
            "CAN_BUY" => "Y"
        ),
        false,
        false,
        array("ID", "NAME", "CALLBACK_FUNC", "MODULE",
            "PRODUCT_ID", "QUANTITY", "DELAY",
            "CAN_BUY", "PRICE", "WEIGHT")
    );
    while ($arItems = $dbBasketItems->Fetch()) {
        if (strlen($arItems["CALLBACK_FUNC"]) > 0) {
            CSaleBasket::UpdatePrice(
                $arItems["ID"],
                $arItems["NAME"],
                $arItems["CALLBACK_FUNC"],
                $arItems["MODULE"],
                $arItems["PRODUCT_ID"],
                $arItems["QUANTITY"]);
            $arItems = CSaleBasket::GetByID($arItems["ID"]);
        }

        $arBasketItems[] = $arItems;
    }
    return $arBasketItems;
}

// список товаров по id заказа
function BasketItems($order_id)
{
    $dbBasketItems = CSaleBasket::GetList(
        array(
            "NAME" => "ASC",
            "ID" => "ASC"
        ),
        array(
            // "FUSER_ID" => $user_id,
            "LID" => SITE_ID,
            "ORDER_ID" => $order_id,
            "CAN_BUY" => "Y"
        ),
        false,
        false,
        array("PRODUCT_ID")
    );
    while ($arItems = $dbBasketItems->Fetch()) {
        $arBasketItems[] = $arItems;
    }

    return $arBasketItems;
}

function userPaidProds()
{

    global $USER, $DB, $APPLICATION;
// Выведем даты всех заказов текущего пользователя за текущий месяц, отсортированные по дате заказа
    $arFilter = array(
        "USER_ID" => $USER->GetID(),
        //">=DATE_INSERT" => date($DB->DateFormatToPHP(CSite::GetDateFormat("SHORT")), mktime(0, 0, 0, date("n"), 1, date("Y")))
    );

    $db_sales = CSaleOrder::GetList(array("DATE_INSERT" => "ASC"), $arFilter);
    while ($ar_sales = $db_sales->Fetch()) {
        $paidOffers[] = $ar_sales['ID'];
    }

    $paidProducts = [];
    foreach ($paidOffers as $paid) {
        $paidProducts[] = BasketItemsFull($paid);
    }

    $result = [];
    foreach ($paidProducts as $products) {
        foreach ($products as $product) {
            $result[] = $product;
        }
    }

    return $result;

}

function userPaidProdsID()
{
    global $USER;
    $arFilter = array(
        "USER_ID" => $USER->GetID(),
    );
    $db_sales = CSaleOrder::GetList(array("DATE_INSERT" => "ASC"), $arFilter);
    while ($ar_sales = $db_sales->Fetch()) {
        echo $ar_sales["DATE_INSERT_FORMAT"] . "<br>";
        $paidOffers[] = $ar_sales['ID'];
    }


    $paidProducts = [];
    foreach ($paidOffers as $paid) {
        $paidProducts[] = BasketItems($paid);
    }

    $result = [];

    foreach ($paidProducts as $products) {
        foreach ($products as $key => $product) {
            $result[$key][] = $product;
        }
    }

//    debug($result);

    $fin = array_map(function($tag) {
        return array(
            'name' => $tag['PRODUCT_ID'],
        );
    }, $result);

//    debug($fin);

    return $result;

}

function isPayed($orderId) {
    CModule::IncludeModule('sale');
    $res = CSaleOrder::GetList(array(), array("ID" => $orderId)); // ID заказа из переменной
    while ($arItemOrder = $res->Fetch())
    return $arItemOrder['PAYED'];

}

//Товары приобретенные пользователем

function userPaidProdsList() {
    CModule::IncludeModule('sale');
    global $USER;

    $arFilter = Array(
        "USER_ID" => $USER->GetID(),
    );
    $db_sales = CSaleOrder::GetList(array(), $arFilter);
    while ($ar_sales = $db_sales->Fetch()){
        if($ar_sales['PAYED'] == 'Y') {
            $allSales[$ar_sales['ID']]['ID'] = $ar_sales['ID'];
            $allSales[$ar_sales['ID']]['PAYED'] = $ar_sales['PAYED'];
        }
    }

    foreach ($allSales as $sale) {
        $paidProductId[] = BasketItems($sale['ID']);
    }



    $result = [];
    array_walk_recursive($paidProductId, function ($item, $key) use (&$result) {
        $result[] = $item;
    });

    foreach ($result as $isOffer) {
        if(CCatalogSku::GetProductInfo($isOffer)) {
            $result[] = CCatalogSku::GetProductInfo($isOffer)['ID'];
        } else {
            $result[] = $isOffer;
        }
    }
    $finResult = array_unique($result);

    return $finResult;
}