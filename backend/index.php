<?php
    header('Access-Control-Allow-Origin: *');
    header('Access-Control-Allow-Headers: *');
    header('Access-Control-Allow-Methods: GET, PUT, POST, DELETE, OPTIONS');
    header('Content-Type: application/x-www-form-urlencoded');
    require_once './Functions/Fun.php';
    if ($_POST['ico'] != "") $_POST['name'] = "";
    if ($_POST['action'] == 'submit') {
        if(isset($_POST['ico']) && $_POST['ico'] != "") {
            db_find("ico", $_POST['ico']);
        }
        if(isset($_POST['name']) && $_POST['name'] != "") {
            db_find("name", $_POST['name']);
        }
    }
    elseif ($_POST['action'] == "save") {
        db_save($_POST['ico'], $_POST['name']);
    }
    //http://wwwinfo.mfcr.cz/cgi-bin/ares/darv_bas.cgi?ico=27074358
    //http://wwwinfo.mfcr.cz/cgi-bin/ares/ares_es.cgi?obch_jm=asseco
?>
