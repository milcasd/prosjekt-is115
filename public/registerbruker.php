<?php
require_once "../config/dtb.inc.php";

$feilmeldinger = array();

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $brukernavn = $_POST['brukernavn'];
    $fnavn = $_POST['fnavn'];
    $enavn = $_POST['enavn'];
    $epost = $_POST['epost'];
    $tlf = $_POST['tlf'];
    $passord = $_POST['passord'];
    $sted = $_POST['sted'];
    $zip = $_POST['zip'];

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
        $hashet_passord = password_hash($passord, PASSWORD_DEFAULT);

        $sql = "INSERT INTO brukere 
                (brukernavn, fnavn, enavn, epost, tlf, passord, sted, zip) 
                VALUES 
                (:brukernavn, :fnavn, :enavn, :epost, :tlf, :passord, :sted, :zip)"; 
        // Alternativt: INSERT IGNORE INTO users osv.

        $q = $pdo->prepare($sql);

        $q->bindParam(':brukernavn', $brukernavn, PDO::PARAM_STR);
        $q->bindParam(':fnavn', $fnavn, PDO::PARAM_STR);
        $q->bindParam(':enavn', $enavn, PDO::PARAM_STR);
        $q->bindParam(':epost', $epost, PDO::PARAM_STR);
        $q->bindParam(':tlf', $tlf, PDO::PARAM_INT);
        $q->bindParam(':passord', $hashet_passord, PDO::PARAM_STR);
        $q->bindParam(':sted', $sted, PDO::PARAM_STR);
        $q->bindParam(':zip', $zip, PDO::PARAM_INT);

        try {
            $q->execute();
            } catch (PDOException $e) {
            //echo "Error querying database: " . $e->getMessage() . "<br>"; // Aldri gjør dette i produksjon!
            }
            //$q->debugDumpParams(); //kan bruke den for å se hva som er feil 
            
            if($pdo->lastInsertId() > 0) {
                echo "Brukeren din er registreret" . $pdo->lastInsertId() . ".";
            } else {
                echo "Kunne ikke registrere bruker";
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

?> 

<!DOCTYPE html>
<html lang="no">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="https://www.phptutorial.net/app/css/style.css">
    <title>Registrer</title>
</head>
<body>
<main>
    <form method="post">
        <h1>Meld deg inn</h1>
        <div>
            <label for="brukernavn">Brukernavn:</label>
            <input type="text" name="brukernavn">
        </div>
        <div>
            <label for="fnavn">Fornavn:</label>
            <input type="text" name="fnavn">
        </div>
        <div>
            <label for="enavn">Etternavn:</label>
            <input type="text" name="enavn">
        </div>
        <div>
            <label for="epost">epost:</label>
            <input type="epost" name="epost">
        </div>
        <div>
            <label for="tlf">Telefonnummer:</label>
            <input type="tel" name="tlf">
        </div>
        <div>
            <label for="sted">Sted:</label>
            <input type="text" name="sted">
        </div>
        <div>
            <label for="zip">Postnummer:</label>
            <input type="number" name="zip">
        </div>
        <div>
            <label for="passord">Passord:</label>
            <input type="password" name="passord">
        </div>
        <div>
            <label for="passord2">Skriv passord på nytt:</label>
            <input type="password" name="passord2">
        </div>
        <div>
            <label for="Enig">
                <input type="checkbox" name="agree" value="Ja"/> Jeg er enig med
                <a href="#" title="vilkår for tjenester">vilkår for tjenester</a>
            </label>
        </div>
        <button type="submit">Registrer</button>
        <footer>Medlem allerede? <a href="login.php">Logg inn her</a></footer>
    </form>
</main>
</body>
</html>