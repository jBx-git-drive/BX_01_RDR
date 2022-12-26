<?php

function debug($pr, $vd = false, $all = false, $die = false)
{
    global $USER;

//    if ( ($USER->IsAdmin()) || ($_REQUEST["pr"]=="Y") || ($all == true)) {
    if (($USER->GetID() == 1) || ($_REQUEST["pr"] == "Y") || ($all == true)) {
        $bt = debug_backtrace();
        $bt = $bt[0];
        $dRoot = $_SERVER["DOCUMENT_ROOT"];
        $dRoot = str_replace("/", "\\", $dRoot);
        $bt["file"] = str_replace($dRoot, "", $bt["file"]);
        $dRoot = str_replace("\\", "/", $dRoot);
        $bt["file"] = str_replace($dRoot, "", $bt["file"]);
        ?>
        <div style='font-size:13px; color:#333; background-color:#fff; border:1px dashed #108ebd; border-radius:4px; margin:0 0 10px'>
            <div style='padding:3px 5px; background:#99e3ff; font-weight:bold;'>File: <?= $bt["file"] ?>
                [<?= $bt["line"] ?>]
            </div>
            <pre style='margin:0; padding:10px; background-color:#f5f5f5; border: none'>
                <?
                if ($pr && $vd == true) {
                    var_dump($pr);
                } else {
                    print_r($pr);
                }
                ?>
            </pre>
        </div>
        <?
    }
    if ($die) {
        die;
    }
}