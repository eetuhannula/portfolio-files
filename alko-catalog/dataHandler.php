<?php
require_once 'SimpleXLSX.php';
require_once 'Connect.php';

// XLSX FILE URL
$url = 'https://www.alko.fi/INTERSHOP/static/WFS/Alko-OnlineShop-Site/-/Alko-OnlineShop/fi_FI/Alkon%20Hinnasto%20Tekstitiedostona/alkon-hinnasto-tekstitiedostona.xlsx';
$file_name = basename($url);

// LATAA FILE URL OSOITTEESTA 
function downloadFile(){
   global $file_name;
   global $url;
   if(file_put_contents( $file_name,file_get_contents($url))) {
//        echo "File downloaded successfully";
    }
    else {
        echo "File downloading failed";
    } 
}

// OTETAAN HALUTUT SARAKKEET RIVILTÄ
function wantedRowItems($row){
    //    SARAKKEEN JÄRJESTYSNUMERO XLSX TAULUKOSSA
    //    0 = Numero
    //    1 = Nimi
    //    2 = Valmistaja
    //    3 = Pullokoko
    //    4 = Hinta
    //    5 = Litrahinta
    //    8 = Tyyppi
    //    12 = Valmistusmaa
    //    14 = Vuosikerta
    //    21 = Alkoholi %
    //    27 = Energia kcal/100ml
    $items = [$row[0],$row[1],$row[2],$row[3],$row[4],$row[5],$row[8],$row[12],$row[14],$row[21],$row[27],];
    return $items;    
}

// LUODAAN SIISTITTY ARRAY (JOSSA VAIN HALUTUT SARAKKEET)
function xlsxToArray(){
    global $file_name;
    
    $xlsxrows = [];
    if ( $xlsx = SimpleXLSX::parse($file_name)) {
        foreach ( $xlsx->rows() as $r => $row) {
            if ($r > 3){
                $newrow = wantedRowItems($row);
                $xlsxrows[] = $newrow;   
            }   
        }
    }
    return $xlsxrows;
}

// FUNKTIO DATABASE TAULUN LUONTIIN
function createDbTable() {
    global $tablename;
    
    // YHTEYS DB:
    $conn = connect();
    
    try {
        // TUHOTAAN VANHA TAULU 
        $sql = "DROP TABLE $tablename";
        $conn->exec($sql);
        
        // LUODAAN UUSI TAULU
        $sql = "CREATE TABLE $tablename ("
                . "id INT(6) UNSIGNED AUTO_INCREMENT PRIMARY KEY,"
                . "numero INT(6) NOT NULL,"
                . "nimi VARCHAR(128) NOT NULL,"
                . "valmistaja VARCHAR(64),"
                . "pullokoko VARCHAR(16) ,"
                . "hinta DEC(10, 2),"
                . "litrahinta DEC(10, 2),"                
                . "tyyppi VARCHAR(64),"
                . "valmistusmaa VARCHAR(32),"
                . "vuosikerta VARCHAR(4),"
                . "alkoholi FLOAT(4),"
                . "energia FLOAT(4),"
                . "add_date TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP"
                . ")";
        
        $conn->exec($sql);
//        DEBUG: 
//        echo "Table created. <br>";
    } catch (PDOException $ex) {
        echo "<br> table error: ".$ex;
    }
}

// FUNKTIO DATAN VIEMISEKSI TAULUUN
function dataToTable(){
    global $tablename;
    
    // SIISTITÄÄN XLSX TIEDOSTO
    $xlsxrows = xlsxToArray();
    
    // DATABASE YHTEYS:
    $conn = connect();
    
    try {
        // COUNTER :
	$x = 0;
        
        //Prepare and bind: 
        $stmt = $conn->prepare(
        "INSERT INTO $tablename(numero, nimi, valmistaja, pullokoko, hinta, litrahinta, tyyppi, valmistusmaa, vuosikerta, alkoholi, energia)
        VALUES(:numero, :nimi, :valmistaja, :pullokoko, :hinta, :litrahinta, :tyyppi, :valmistusmaa, :vuosikerta, :alkoholi, :energia)");
        $stmt->bindParam(':numero', $numero);
        $stmt->bindParam(':nimi', $nimi);
        $stmt->bindParam(':valmistaja', $valmistaja);
        $stmt->bindParam(':pullokoko', $pullokoko);
        $stmt->bindParam(':hinta', $hinta);
        $stmt->bindParam(':litrahinta', $litrahinta);
        $stmt->bindParam(':tyyppi', $tyyppi);
        $stmt->bindParam(':valmistusmaa', $valmistusmaa);
        $stmt->bindParam(':vuosikerta', $vuosikerta);
        $stmt->bindParam(':alkoholi', $alkoholi);
        $stmt->bindParam(':energia', $energia);
        
        foreach ($xlsxrows as $row) {
            // COUNTER +1
            $x++;
            
            // Insert values: 
            $numero = (empty($row[0])) ? NULL : $row[0] ;
            $nimi = (empty($row[1])) ? NULL : $row[1] ;
            $valmistaja = (empty($row[2])) ? NULL : $row[2] ;
            $pullokoko = (empty($row[3])) ? NULL : $row[3] ;
            $hinta = (empty($row[4])) ? NULL : $row[4] ;
            $litrahinta = (empty($row[5])) ? NULL : $row[5] ;
            $tyyppi = (empty($row[6])) ? NULL : $row[6] ;
            $valmistusmaa = (empty($row[7])) ? NULL : $row[7] ;
            $vuosikerta = (empty($row[8])) ? NULL : $row[8] ;
            $alkoholi = (empty($row[9])) ? NULL : $row[9] ;
            $energia = (empty($row[10])) ? NULL : $row[10] ;
            
            
//            echo "Row $x created <br>";
//            echo "$numero, $nimi, $valmistaja, $pullokoko, $hinta, $litrahinta, $tyyppi, $valmistusmaa, $vuosikerta, $alkoholi, $energia <br>";
            
            $stmt->execute();
           
        }
    } catch (PDOException $ex) {
        echo "<br> insert error: ".$ex;
    }
}

function updateDatabase(){
    global $file_name;
    downloadFile();
    xlsxToArray();
    createDbTable();
    dataToTable();
    echo "<div class='dbupdate'>";
    echo "<h2> DATABASE UPDATED!</h2>";
    echo "</div>";

}



