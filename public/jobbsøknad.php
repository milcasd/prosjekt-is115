<?php
require "../config/dtb.inc.php";
session_start();

// Sjekk om brukeren er logget inn
if (!isset($_SESSION['bruker']['innlogget']) || $_SESSION['bruker']['innlogget'] !== true) {
    header("Location: login.php"); // Omdiriger til innloggingssiden hvis ikke innlogget
    exit();
}

// Logg ut brukeren
if (isset($_POST['loggut'])) {
    session_destroy();
    header("Location: hjem.php");
    exit();
}

$feilmeldinger = array();

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $fnavn = $_POST['fnavn'];
    $enavn = $_POST['enavn'];
    $tlf = $_POST['tlf'];
    $epost = $_POST['epost'];
    $utdanning = $_POST['utdanning'];
    $erfaring = $_POST['erfaring'];
    $soknadstekst = $_POST['soknadstekst'];

    if (!preg_match('/^[\p{L} -]+$/u', $fnavn)) {
        $feilmeldinger[] = "Fornavn kan kun inneholde bokstaver.";
    }

    if (!preg_match('/^[\p{L} -]+$/u', $enavn)) {
        $feilmeldinger[] = "Etternavn kan kun inneholde bokstaver.";
    }

        // Validering av e-postadresse
    $epost = $_POST["epost"];
    if (!filter_var($epost, FILTER_VALIDATE_EMAIL)) {
        $feilmeldinger[] = "E-postadressen er ugyldig.";
    }

    // Validering av telefonnummer (kun tall og riktig lengde)
    $tlf = $_POST["tlf"];
    if (!is_numeric($tlf) || strlen($tlf) != 8) {
        $feilmeldinger[] = "Telefonnummeret er ugyldig. Det må være 8 siffer.";
    }
        

    if (empty($feilmeldinger)) {

        $sql = "INSERT INTO jobbsoknad
                (fnavn, enavn, tlf, epost, utdanning, erfaring, soknadstekst) 
                VALUES 
                (:fnavn, :enavn, :tlf, :epost, :utdanning, :erfaring, :soknadstekst)"; 
        // Alternativt: INSERT IGNORE INTO users osv.

        $q = $pdo->prepare($sql);

        $q->bindParam(':fnavn', $fnavn, PDO::PARAM_STR);
        $q->bindParam(':enavn', $enavn, PDO::PARAM_STR);
        $q->bindParam(':tlf', $tlf, PDO::PARAM_INT);
        $q->bindParam(':epost', $epost, PDO::PARAM_STR);
        $q->bindParam(':utdanning', $utdanning, PDO::PARAM_STR);
        $q->bindParam(':erfaring', $erfaring, PDO::PARAM_STR);
        $q->bindParam(':soknadstekst', $soknadstekst, PDO::PARAM_STR);

        try {
            $q->execute();
            } catch (PDOException $e) {
            //echo "Error querying database: " . $e->getMessage() . "<br>"; // Aldri gjør dette i produksjon!
            }
            //$q->debugDumpParams(); //kan bruke den for å se hva som er feil 
            
            if($pdo->lastInsertId() > 0) {
                echo "Data inserted into database, identified by UID " . $pdo->lastInsertId() . ".";

                 // Sender bruker til hjemsiden
                header("Location: hjem1.php ");
                exit(); 
            } else {
                echo "Data were not inserted into database.";
            }
    } else {
        // Skriv ut feilmeldinger
        echo "<div style='color: red;'>";
        foreach ($feilmeldinger as $feilmelding) {
            echo "<p>$feilmelding</p>";
        }
        echo "</div>";
    }
}
// Skjema sendt?
if (isset($_REQUEST['last-opp-send'])) 
{
    
    // Definer matrise for meldinger
    $meldinger = array();
    
    // Filopplasting
    if (is_uploaded_file($_FILES['cv']['tmp_name'])) 
    {
        // Henter informasjon om fil
        $filtype = $_FILES['cv']['type'];
        $filstr = $_FILES['cv']['size'];
        
        $aks_filtyper = array("pdf" =>"application/pdf",
                              "doc" => "application/msword",
                              "docx" => "application/vnd.openxmlformats-officedocument.wordprocessingml.document"
        );
        $max_filstr = 1530000; // i byte

        $mappe = "/prosjekt/filer/";
        $filsystem_mappe = $_SERVER['DOCUMENT_ROOT'] . $mappe;
        $web_mappe = $_SERVER['REQUEST_SCHEME'] . "://" . $_SERVER['HTTP_HOST'] . $mappe;

        // Ingen mappe ved det navnet?
        if(!file_exists($filsystem_mappe)) 
        {
            if (!mkdir($filsystem_mappe, 0777, true)) 
                die("Kan ikke opprette mappe... " . $filsystem_mappe);
        }
        
        // Konstruerer filnavn
        $suffix = array_search($_FILES['cv']['type'], $aks_filtyper);

        // Hvis filnavnet eksisterer allerede av en eller annen grunn
        do {
            $filnavn  = substr(md5(date('YmdHisu')), 0, 5) . '.' . $suffix;
        }
        while(file_exists($filsystem_mappe . $filnavn));
        
        // Feil?
        if (!in_array($filtype, $aks_filtyper)) 
        {
            $typer = implode(", ", array_keys($aks_filtyper));
            $meldinger['error'][] = "Ugyldig filtype (kun <em>" . $typer . "</em> er akseptert)";
        }
        if ($filstr > $max_filstr)
            $meldinger['error'][] = "Filstørrelsen (" . round($filstr / 1048576, 2) . " MB) overgår maksimal filstørrelse (" . round($max_filstr / 1048576, 2) . " MB)"; // Bin. konvertering
        
        // Om alt er fint
        if (empty($meldinger)) 
        {
            // Flytter filen din den skal være
            $filsti = $filsystem_mappe . $filnavn;
            $opplastet_fil = move_uploaded_file($_FILES['cv']['tmp_name'], $filsti);
            
            if (!$opplastet_fil) 
                $meldinger['error'][] = "Filen kunne ikke bli lastet opp.";
            else
                $meldinger['success'][] = "Filen ble lastet opp og finnes her: <strong>" . $filsti . "</strong> (filsystemref.) eller her <strong>" . '<a href="' . $web_mappe . $filnavn . '">' . $web_mappe . $filnavn . "</a></strong> (URL)";
        }

    } 
    else 
    {
        $meldinger['error'][] = "Ingen fil er valgt.";
    }
}
?>
<!DOCTYPE html>
<html lang="no">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Jobbsøknadsskjema</title>
</head>
    <link rel="stylesheet" href="../css/ss.css">
<body>
    <?php
        // Skriv ut beskjeder til bruker
        if(isset($meldinger) && !empty($meldinger))
        {
            echo "<strong>Melding" . (sizeof($meldinger, COUNT_RECURSIVE)-1 > 1 ? "s:<br>" : ":<br>") . "</strong>";
            foreach($meldinger as $mld_type => $type_meldinger)
            {
                if($mld_type == 'error')
                    foreach($type_meldinger as $melding) { echo '<span style="color:red";>- ' . $melding . '</span><br>'; }
                elseif($mld_type == 'success')
                    foreach($type_meldinger as $melding) { echo '<span style="color:green";>- ' . $melding . '</span><br>'; }
            }
        }
        
    ?>
    <div class="container">
        <div class= "form-container">
            <h2>Søknadsskjema</h2>
            <form method="post" enctype="multipart/form-data" action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]);?>">
            <div>    
                <label for="fnavn">Fornavn:</label><br>
                <input type="text" name="fnavn"><br><br>
            </div>
            <div>
                <label for="enavn">Etternavn:</label><br>
                <input type="text" name="enavn"><br><br>
            </div>
            <div>    
                <label for="telefonnummer">Telefonnummer:</label><br>
                <input type="text" name="tlf"><br><br>
            </div>
            <div>
                <label for="epost">Email:</label><br>
                <input type="text" name="epost"><br><br>
            </div>
            <div>    
                <label for="cv">Last opp CV (PDF, DOC, or DOCX):</label><br>
                <input type="file" name="cv"><br><br>
            </div>
            <div>    
                <label for="utdanning">Utdanning:</label><br>
                <textarea name="utdanning" rows="4" cols="50"></textarea><br><br>
            </div>    
                <label for="erfaring">Arbeidserfaring:</label><br>
                <textarea name="erfaring" rows="4" cols="50"></textarea><br><br>
            <div>    
                <label for="soknadstekst">Søknadstekst:</label><br>
                <textarea name="soknadstekst" rows="9" cols="50"></textarea><br><br>
            </div>    
            <button type="submit" name="last-opp-send">Last opp søknad</button><br><br>
            </form>
        </div>
    </div>
</body>
</html>
