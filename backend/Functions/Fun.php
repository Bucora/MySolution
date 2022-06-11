<?php
    //Function connects to DB, returns active connection
    function connect_to_DB() {
        $servername = "newdb.cj4upkblvmyi.eu-central-1.rds.amazonaws.com";
        $username = "admin";
        $password = "adminadmin";
        $dbname = "Mydb";       
        $conn = new mysqli($servername, $username, $password, $dbname);
        if ($conn->connect_error) die("Connection failed: " . $conn->connect_error);
        return $conn;
    }
    //Function searches ARES for data, sends data to FE
    function ares_find($search_for, $str) {
        switch($search_for) {
            case "ico":
                $url = "http://wwwinfo.mfcr.cz/cgi-bin/ares/darv_bas.cgi?ico=".$str;
                $xml = simplexml_load_file($url);
                if ($xml->children('are', true)->children('D', true)->E->EK->__toString() === '1') echo "Found nothing";
                else {
                    $data = $xml->children('are', true)->children('D', true)[4]->children('D', true);
                    $json_data = array("id"=>$data[0]->__toString(), "Meno"=>$data[2]->__toString());
                    echo ("[".json_encode($json_data)."]");
                }
                return;
            case "name":    
                $url = "http://wwwinfo.mfcr.cz/cgi-bin/ares/ares_es.cgi?obch_jm=".$str;
                $xml = simplexml_load_file($url);
                if ($xml->children('are', true)[0]->children('dtt', true)->Pocet_zaznamu->__toString() === '0') echo "Found nothing";
                else {
                    $arr = $xml->children('are', true)->children('dtt', true)->children('dtt', true);
                    $count = 0;
                    echo ("[");
                    foreach ($arr as $entry) {
                        $json_data = array("id"=>$entry->ico->__toString(), "Meno"=>$entry->ojm->__toString());
                        echo (json_encode($json_data));
                        if ($count < count($arr) - 1) echo (",");
                        $count++;
                    }
                    echo ("]");
                }
                return;
        }
    }
    //Function to find entries in our own DB, if it fails, calls for ares_find()
    function db_find($search_for, $str) { 
        $conn = connect_to_DB();
        if ($search_for == "ico") $sql = "SELECT * FROM Firmy WHERE ICO=" . $str;  
        elseif ($search_for == "name") $sql = "SELECT * FROM Firmy WHERE Nazov=\"" . $str . "\""; 
        $result = $conn->query($sql); 
        if ($result->num_rows > 0) {
            $row = $result->fetch_assoc();
            $date1 = new DateTime($row["Datum"]);
            $date2 = new DateTime("now");
            if ($date1->diff($date2)->m < 1) {
                $json_data = array("id"=>$row["ICO"], "Meno"=>$row["Nazov"]);
                echo ("[" . json_encode($json_data) . "]");
            }
            else {
                ares_find($search_for, $str);
            }
        } else {
            ares_find($search_for, $str);
        }
        $conn->close();
    }
    //Function saves chosen entry to our DB
    function db_save($ico, $name) {
        $conn = connect_to_DB();
        $sql = "INSERT INTO Firmy VALUES (" . $ico . ", \"" . $name . "\", CURDATE())
            ON DUPLICATE KEY UPDATE Datum=CURDATE();"; 
        $result = $conn->query($sql);
        var_dump($result); 
        $conn->close();
    }