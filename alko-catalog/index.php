<!DOCTYPE html>
<?php 
// HARJOITUKSEN VUOKSI KÄYTIN PHP SESSION SYSTEEMIÄ SIVUNUMEROINTIIN
// ongelmana täss kuitenkin että tämän toimimiseksi tarvitsee antaa konttiin
// oikeuksia mitkä ei tietoturvan kannalta kaiketi paras ratkaisu 
// sillä kontin /var/lib/php kansion sisällölle piti antaa 777 jotta toimi
// tähänkin kaiketi on joku fixi olemassa mutta ei nyt tällä tasolla vielä tiedossa :P
session_start();
require_once 'dataHandler.php';
require_once 'queryHandler.php';
?>

<html>
    <head>
        <meta charset="UTF-8">
        <title> Alkon tuotekatalogi </title>
        <link rel="stylesheet" href="styles.css">
    </head>
    <body>
        
        <!-- HEADER --> 
        <div class="header">
            <img id="logo" src="images/alkologo.svg" alt="logo"/>
            <h1>Hyvää Joulua!</h1>
        </div>

        <!--INFOT-->
        <?php
            echo "<div class='infoarea'>";
            echo "<h2> Alkon tuotekatalogi:</h2>";
            
            echo "<h2>";
            timeDate();
            echo "</h2>";

            echo "</div>";
        ?>
        
        <!-- KONTROLLERIT -->
        <div class="controllers">
            <div class="readmecontroller">
                <div class="readmevisible"><button>README</button></div>
                <div class="readmehidden">
                    <p>NOTES: Nappi tietokannan päivittämiseen sekä koko tietokannan tulostamiseen on enemmänkin ns "admin"
                    työkaluja, jotka nyt vain jätin näkyviin ja helpottamaan tehtävän tekemistä. 
                    Saa tietenkin kokeilla, joskin varaudu odottamaan pieni hetki jos päivität tietokannan 
                    (Tehtävä valmis kun näytölle ilmestyy DATABASE UPDATED viesti. 
                    Tämä lataa uuden XLSX tiedoston servulle, tuhoaa vanhan tietokannan (taulun) ja luo uuden käyttäen uutta XLSX tiedostoa. </p>
                </div> 
            </div>
            <div class="pagecontroller">
                <form action="index.php" method="POST">
                    <input type="submit" name="firstpage" value="SIVU 1">
                    <input type="submit" name="prevpage" value="<<">
                    <input type="submit" name="nextpage" value=">>">
                </form>
            </div>
            
            <div class="filtercontroller">
                <div class="filters"><button>FILTTERIT</button></div>
                <div class="filtershidden">   
                    <form action="index.php" method="POST"> 
                        <input type="radio" name="tyyppi" value="punaviinit"><label>Punaviinit</label><br>
                        <input type="radio" name="tyyppi" value="roseeviinit"><label>Roseeviinit</label><br>
                        <input type="radio" name="tyyppi" value="valkoviinit"><label>Valkoviinit</label><br>
                        <input type="radio" name="tyyppi" value="rommit"><label>Rommit</label><br>
                        <input type="radio" name="tyyppi" value="konjakit"><label>Konjakit</label><br>
                        <input type="radio" name="tyyppi" value="viskit"><label>Viskit</label><br>
                        <input type="radio" name="tyyppi" value="oluet"><label>Oluet</label><br>
                        <input type="radio" name="tyyppi" value="siiderit"><label>Siiderit</label><br>
                        <input type="radio" name="tyyppi" value="juomasekoitukset"><label>Sekoitukset</label><br>
                        <input type="radio" name="tyyppi" value="alkoholittomat"><label>Alkoholiton</label><br>
                        <input type="radio" name="tyyppi" value="lahja- ja juomatarvikkeet"><label>Tarvikkeet</label><br>
                        <input type="radio" name="tyyppi" value="Jälkiruokaviinit, väkevöidyt ja muut viinit"><label>Jäkiruoka-, väkevöidyt ja muut viinit</label><br>
                        <input type="radio" name="tyyppi" value="Brandyt, Armanjakit ja Calvadosit"><label>Brandyt, Armanjakit, Calvadosit</label><br>
                        <input type="radio" name="tyyppi" value="Ginit ja maustetut viinat"><label>Ginit ja maustetut viinat</label><br>
                        <input type="radio" name="tyyppi" value="Liköörit ja Katkerot"><label>Liköörit ja katkerot</label><br>
                        <input type="radio" name="tyyppi" value="kuohuviinit ja shamppanjat"><label>Kuohuviinit ja shampanjat</label><br>
                        <input type="radio" name="tyyppi" value="vodkat ja viinat"><label>Vodkat ja viinat</label><br><br>
                        <label>Hinta(€): </label><input type="number" name="sizemin" step="0.01" min="0" value="0"> - <input type="number" name="sizemax" step="0.01" min="0" value="20000" >
                        <input type="submit" name="applyfilter" value="APPLY FILTER">
                    </form>
                </div> 
            </div>
            

            
            <div class="dbcontrol">
                <form action="index.php" method="POST">
                    <input type="submit" name="fulldatabase" value="Tulosta koko tietokanta">
                    <input type="submit" name="dbupdate" value="Tietokannan päivitys (vie hetken)">
                    
                </form>
            </div>
        </div>
        
        <!--        TESTI DIV MANUAALISILLE RIVIHAULLE-->
    <!--<div class="manualcontroller">
                <form action="index.php" method="POST">
                    <label for="startrow"> Query rows from: </label><input type="number" name="startrow" value="0">
                    <label for="endrow"> to: </label><input type="number" name="endrow" value="25">
                    <input type="submit" name="submitmanual" value='Select rows'>
                </form>
            </div> -->
        
        <!-- TULOSTETTAVAN TAULUKON ALUE-->
        <div class ="dataview"> 
        <?php
        
        //MUUTTUJAT 
        $pgnro = $_SESSION['pgnro'];
        $pageStart = $_POST['startrow'];
        $pageEnd = $_POST['endrow'];
        
        // DEBUGS:
       
        // ALUSTUS ENSIMMÄISELLÄ LATAUKSELLA
        if (!isset($_SESSION['query'])){
            $_SESSION['query'] = "SELECT * FROM ".$tablename;
            $pgnro = 1;
            createTablePage($pgnro);
        }
                
        //PAINETAAN PREVIOUS PAGE NAPPIA
        if (isset($_POST['prevpage'])){
            if ($pgnro > 1){
                $pgnro = $pgnro - 1;
                $_SESSION['pgnro'] = $pgnro;
                createTablePage($pgnro);
            } else {
                $pgnro = 1;
                createTablePage($pgnro);
            }             
        }
        
        // PAINETAAN NEXT PAGE NAPPIA
        elseif (isset($_POST['nextpage'])){
            $pgnro = $pgnro + 1;
            $_SESSION['pgnro'] = $pgnro;
            createTablePage($pgnro);
        }
        
        // PAINETAAN RELOAD / SIVU 1 NAPPIA (Palataan alkuun)
        elseif (isset($_POST['firstpage'])){
            $pgnro = 1;
            $_SESSION['pgnro'] = $pgnro;
            createTablePage($pgnro);
        }
        
        // SYÖTETÄÄN MANUAALISET ARVOT (EI KÄYTÖSSÄ OLI TESTIMODUULI)
        elseif (isset($_POST['submitmanual'])){
            createTableManual($pageStart, $pageEnd);
              
        }
        
        // TIETOKANNAN PÄIVITYS (LATAA FILEN, TUHOAA VANHAT TIEDOT, VIE UUDET TIEDOT DATABASEEN)
        elseif (isset($_POST['dbupdate'])){
            updateDatabase();
        }
        
        // TULOSTA KOKO TIETOKANTA
        elseif (isset($_POST['fulldatabase'])){
            $_SESSION['query'] = "SELECT * FROM $tablename";
            createTableFull();
        }

        // FILTERIEN LISÄÄMINEN
       
        elseif (isset($_POST['applyfilter'])){
            $pgnro = 1;
            $tyyppi = $_POST['tyyppi'];
            $sizemin = $_POST['sizemin'];
            $sizemax = $_POST['sizemax'];
            $_SESSION['query'] = addFilters($tyyppi, $sizemin, $sizemax);
            createTablePage($pgnro);
        }
        
        ?> 
            
        </div>
        
    </body>
</html> 