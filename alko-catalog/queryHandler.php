<?php
require_once 'Connect.php';

// MUUTTUJIEN ALUSTUS (KÄSINVALITUT DB RIVIT); 
$pageStart = 0;
$pageEnd = 26;

function timeDate(){
   global $tablename;
   try {
       //YHTEYS
        $conn = connect();
        // LUODAAN HAKULAUSE
        $sql = "SELECT add_date FROM $tablename LIMIT 1";
        // STATEMENT
        $stmt = $conn->prepare($sql);
        $stmt->execute();
        // HAETAAN DATA KUTSUSTA PALAUTUNEESTA TIEDOSTA
        $result = $stmt->SetFetchMode(PDO::FETCH_NUM);

        while (($row = $stmt->fetch())){
            echo $row[0];
        }
   } catch (Exception $ex) {
       echo "ERROR : ".$ex;
   }   
}

function headerNames(){
    try {
            $conn = connect();
            
            // LUODAAN HAKULAUSE
            $sql = "DESCRIBE products";

            // STATEMENT
            $stmt = $conn->prepare($sql);
            $stmt->execute();
            $table_fields = $stmt->fetchAll(PDO::FETCH_COLUMN);
                      
            echo "<tr><td>".$table_fields[0]."</td>"
                            ."<td>".$table_fields[1]."</td>"
                            ."<td>".$table_fields[2]."</td>"
                            ."<td>".$table_fields[3]."</td>"
                            ."<td>".$table_fields[4]."</td>"
                            ."<td>".$table_fields[5]."</td>"
                            ."<td>".$table_fields[6]."</td>"
                            ."<td>".$table_fields[7]."</td>"
                            ."<td>".$table_fields[8]."</td>"
                            ."<td>".$table_fields[9]."</td>"
                            ."<td>".$table_fields[10]."</td>"
                            ."<td>".$table_fields[11]."</td></tr>";
            
            
        } catch (PDOException $ex) {
            echo "<br> table error: ".$ex;
        }
        $conn = null;
    
    
}

function headerNamesNOID(){
    try {
            $conn = connect();
            
            // LUODAAN HAKULAUSE
            $sql = "DESCRIBE products";

            // STATEMENT
            $stmt = $conn->prepare($sql);
            $stmt->execute();
            $table_fields = $stmt->fetchAll(PDO::FETCH_COLUMN);
                      
            echo "<tr><td>".$table_fields[1]."</td>"
                            ."<td>".$table_fields[2]."</td>"
                            ."<td>".$table_fields[3]."</td>"
                            ."<td>".$table_fields[4]."</td>"
                            ."<td>".$table_fields[5]."</td>"
                            ."<td>".$table_fields[6]."</td>"
                            ."<td>".$table_fields[7]."</td>"
                            ."<td>".$table_fields[8]."</td>"
                            ."<td>".$table_fields[9]."</td>"
                            ."<td>".$table_fields[10]."</td>"
                            ."<td>".$table_fields[11]."</td></tr>";
            
            
        } catch (PDOException $ex) {
            echo "<br> table error: ".$ex;
        }
        $conn = null;
    
    
}

function addFilters($tyyppi, $sizemin, $sizemax){
    global $tablename;
    $sql = "SELECT * FROM ".$tablename
            ." WHERE tyyppi='".$tyyppi."'"
            ." AND hinta>".$sizemin
            ." AND hinta<".$sizemax;
    return $sql;
}

function createTablePage($currentPage){
    
    global $pageStart;
    global $pageEnd;
    
    try {
            //YHTEYS
            $conn = connect();
            // LUODAAN HAKULAUSE
            $sql = $_SESSION['query'];
            // STATEMENT
            $stmt = $conn->prepare($sql);
            $stmt->execute();
            // HAETAAN DATA KUTSUSTA PALAUTUNEESTA TIEDOSTA
            $result = $stmt->SetFetchMode(PDO::FETCH_NUM);
            
            //SIVUN RIVIEN ALUN JA LOPUN MÄÄRITYS
            $pageStart = ($currentPage * 25) - 25;
            $pageEnd = $pageStart + 25 + 1;
            
            // TAULUKON LUONTI
            echo "<table>";
            $count = 0;
            headerNamesNOID();
            while (($row = $stmt->fetch())){
                $count++;
                
                if (($count > $pageStart) && ($count < $pageEnd)){
                    echo "<tr><td>".$row[1]."</td>"
                            ."<td>".$row[2]."</td>"
                            ."<td>".$row[3]."</td>"
                            ."<td>".$row[4]."</td>"
                            ."<td>".$row[5]."</td>"
                            ."<td>".$row[6]."</td>"
                            ."<td>".$row[7]."</td>"
                            ."<td>".$row[8]."</td>"
                            ."<td>".$row[9]."</td>"
                            ."<td>".$row[10]."</td>"
                            ."<td>".$row[11]."</td></tr>";
                } else {
                    
                }
            } 
            echo "</table>";
            
        } catch (PDOException $ex) {
            echo "<br> table error: ".$ex;
        }
        $conn = null;
}

function createTableManual($pageStart, $pageEnd){

    try {
            $conn = connect();
            
            // LUODAAN HAKULAUSE
            $sql = $_SESSION['query'];

            // STATEMENT
            $stmt = $conn->prepare($sql);
            $stmt->execute();

            // HAETAAN DATA KUTSUSTA PALAUTUNEESTA TIEDOSTA
            $result = $stmt->SetFetchMode(PDO::FETCH_NUM);

            // TABLE
            echo "<table>";
            $count = 0;
            headerNames();
            while (($row = $stmt->fetch())){
                $count++;
                
                if (($count >= $pageStart) && ($count <= $pageEnd)){
                    echo "<tr><td>".$row[0]."</td>"
                            ."<td>".$row[1]."</td>"
                            ."<td>".$row[2]."</td>"
                            ."<td>".$row[3]."</td>"
                            ."<td>".$row[4]."</td>"
                            ."<td>".$row[5]."</td>"
                            ."<td>".$row[6]."</td>"
                            ."<td>".$row[7]."</td>"
                            ."<td>".$row[8]."</td>"
                            ."<td>".$row[9]."</td>"
                            ."<td>".$row[10]."</td>"
                            ."<td>".$row[11]."</td></tr>";
                } else {
                    
                }
            } 
            echo "</table>";
            
        } catch (PDOException $ex) {
            echo "<br> table error: ".$ex;
        }
        $conn = null;
    
    
}

function createTableFull(){
    try {
            
            
            $conn = connect();
            
            // LUODAAN HAKULAUSE
            $sql = $_SESSION['query'];

            // STATEMENT
            $stmt = $conn->prepare($sql);
            $stmt->execute();

            // HAETAAN DATA KUTSUSTA PALAUTUNEESTA TIEDOSTA
            $result = $stmt->SetFetchMode(PDO::FETCH_NUM);

            // TABLE
            echo "<table>";
            $count = 0;
            headerNames();
            while (($row = $stmt->fetch())){
                $count++;
                echo "<tr><td>".$row[0]."</td>"
                            ."<td>".$row[1]."</td>"
                            ."<td>".$row[2]."</td>"
                            ."<td>".$row[3]."</td>"
                            ."<td>".$row[4]."</td>"
                            ."<td>".$row[5]."</td>"
                            ."<td>".$row[6]."</td>"
                            ."<td>".$row[7]."</td>"
                            ."<td>".$row[8]."</td>"
                            ."<td>".$row[9]."</td>"
                            ."<td>".$row[10]."</td>"
                            ."<td>".$row[11]."</td></tr>";
            } 
            echo "</table>";
            
        } catch (PDOException $ex) {
            echo "<br> table error: ".$ex;
        }
        $conn = null;
}
