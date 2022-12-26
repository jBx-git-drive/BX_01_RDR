<?php

function jsPop($mess, $url = "")
{
    ?>
    <script>
        $(document).ready(function () {
            $.magnificPopup.open({
                items: {
                    src: '<div class="white-popup"><div class="popupMessage mb0"><?=$mess?></div></div>',
                    type: 'inline'
                }, modal: false,
                <? if($url != ""){?>
                callbacks: {
                    close: function () {
                        window.location.href = "<?=$url?>";
                    }
                }
                <? } ?>
            });
        });
    </script>
    <?
}

function jsPopStyle($mess, $url = "")
{
    ?>
    <script>
        $(document).ready(function () {
            $.magnificPopup.open({
                items: {
                    src: '<div class="white-popup"><div class="popupMessage mb1"><?=$mess?></div></div>',
                    type: 'inline'
                }, modal: false,
                <? if($url != ""){?>
                callbacks: {
                    close: function () {
                        window.location.href = "<?=$url?>";
                    }
                }
                <? } ?>
            });
        });
    </script>
    <?
}

function my_mb_ucfirst($str)
{
    $fc = mb_strtoupper(mb_substr($str, 0, 1));
    return $fc . mb_substr($str, 1);
}

function countDaysBetweenDates($d1_ts, $d2)
{
    $d1_ts = time();
    $d2_ts = strtotime($d2);

    $seconds = abs($d1_ts - $d2_ts);

    if ($seconds < '2629743') {
        return true;
    } else {
        return false;
    }
}

function actualDate($dateTime) {
    $createDate = new DateTime($dateTime);
    $strip = $createDate->format('d.m.Y');
    return $strip;
}

CModule::IncludeModule("iblock");
$propertyFilter = array(93, 94, 95, 96, 97, 98, 99);
$db_props = CIBlockElement::GetPropertyValues(20, array("sort" => "asc"), $propertyFilter);
if ($ar_props = $db_props->Fetch()) {
    define("CONT_EMAIL", $ar_props["93"]);
    define("CONT_PHONE", $ar_props["94"]);
    define("CONT_WORK_HOURS", $ar_props["95"]);
    define("CONT_SUPPORT", $ar_props["96"]);
    define("CONT_COPYRIGHT", $ar_props["97"]);
    define("CONT_VK", $ar_props["98"]);
    define("CONT_YT", $ar_props["99"]);
}

function num_word($value, $words, $show = true)
{
    $num = $value % 100;
    if ($num < 5) {
        $num = $num % 10;
    }

    $out = ($show) ?  $value . ' ' : '';
    switch ($num) {
        case 1:  $out .= $words[0]; break;
        case 2:
        case 3:
        case 4:  $out .= $words[1]; break;
        default: $out .= $words[2]; break;
    }

    return $out;
}

function periodInDays($period) {
    switch ($period) {
        case '1 месяц':
            $result = "30";
            break;
        case '3 месяца':
            $result = "90";
            break;
        case '6 месяцев':
            $result = "180";
            break;
        case '12 месяцев':
            $result = "360";
            break;
        case 'бессрочно':
            $result = "36500";
            break;
    }
    return $result;
}

function gen_token()
{
    if (function_exists('com_create_guid') === true) {
        return trim(com_create_guid(), '{}');
    }
    return sprintf(
        '%04X%04X-%04X-%04X-%04X-%04X%04X%04X',
        mt_rand(0, 65535),
        mt_rand(0, 65535),
        mt_rand(0, 65535),
        mt_rand(16384, 20479),
        mt_rand(32768, 49151),
        mt_rand(0, 65535),
        mt_rand(0, 65535),
        mt_rand(0, 65535)
    );
}

