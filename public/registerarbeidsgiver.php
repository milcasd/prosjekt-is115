<?php
// Inkluderer databasekoblingen
require_once "../config/dtb.inc.php";

// Feilmeldingsvariabel
$feilmeldinger = array();

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Validering av innsendte data
    $bedriftsnavn = $_POST["bedriftsnavn"];
    $epost = $_POST["epost"];
    $orgnr = $_POST["orgnr"];
    $sted = $_POST["sted"];
    $zip = $_POST["zip"];
    $passord = $_POST["passord"];

    // Validering av organisasjonsnummer (kun tall og riktig lengde)
    $orgnr = $_POST["orgnr"];
    if (!is_numeric($orgnr) || strlen($orgnr) != 9) {
        $feilmeldinger[] = "Organisasjonsnummeret er ugyldig. Det må være 9 siffer.";
    }

    // Sjekk om Orgnr allerede er i bruk
    $sql = "SELECT COUNT(*) as antall FROM arbeidsgiver WHERE orgnr = :orgnr";
    $stmt = $pdo->prepare($sql);
    $stmt->bindParam(':orgnr', $orgnr, PDO::PARAM_STR);
    $stmt->execute();
    $resultat = $stmt->fetch(PDO::FETCH_ASSOC);
    if ($resultat['antall'] > 0) {
        $feilmeldinger[] = "Organisasjonen din er allerede registrert.";
    }

    // Hvis ingen feilmeldinger, legg til bruker i databasen
    if (empty($feilmeldinger)) {
        $hashet_passord = password_hash($passord, PASSWORD_DEFAULT);

        $sql = "INSERT INTO arbeidsgiver (bedriftsnavn, epost, orgnr, sted, zip, passord) VALUES (:bedriftsnavn, :epost, :orgnr, :sted, :zip, :passord)";
        $stmt = $pdo->prepare($sql);
        $stmt->bindParam(':bedriftsnavn', $bedriftsnavn, PDO::PARAM_STR);
        $stmt->bindParam(':epost', $epost, PDO::PARAM_STR);
        $stmt->bindParam(':orgnr', $orgnr, PDO::PARAM_STR);
        $stmt->bindParam(':sted', $sted, PDO::PARAM_STR);
        $stmt->bindParam(':zip', $zip, PDO::PARAM_STR);
        $stmt->bindParam(':passord', $hashet_passord, PDO::PARAM_STR);

        if ($stmt->execute()) {
            echo "Bedriften er registrert!";
        } else {
            echo "Feil ved registrering: " . $stmt->errorInfo()[2];
        }
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="https://www.phptutorial.net/app/css/style.css">
    <title>Registreringsside</title>
</head>
<body>
<main>
    <form method="post">
    <h2>Registrer bedriften din</h2>
        <div>
            <label for="bedriftsnavn">Bedriftsnavn:</label>
            <input type="text" name="bedriftsnavn" required>
        </div>
        <div>
            <label for="epost">Epost:</label>
            <input type="text" name="epost" required>
        </div>
        <div>
            <label for="orgnr">Orgnr:</label>
            <input type="text" name="orgnr" required>
        </div>
        <div>   
            <label for="sted">Sted:</label>
            <input type="text" name="sted" required>
        </div>
        <div>
            <label for="zip">Zip:</label>
            <input type="number" name="zip" required>
        </div>
        <div>    
            <label for="passord">Passord:</label>
            <input type="password" name="passord" required>
        </div>    
        <div>
            <label for="Enig">
                <input type="checkbox" name="agree" value="Ja"/> Jeg er enig med
                <a href="#" title="vilkår for tjenester">vilkår for tjenester</a>
            </label>
        </div>
        <button type="submit">Registrer</button>
        <footer>Medlem allerede? <a href="loginarbeidsgiver.php">Logg inn her</a></footer>
    </form>
    
    <?php
    // Vis feilmeldinger hvis det er noen
    if (!empty($feilmeldinger)) {
        echo '<div class="feilmelding">';
        foreach ($feilmeldinger as $feilmelding) {
            echo "<p>$feilmelding</p>";
        }
        echo '</div>';
    }
    ?>
</main>
</body>
</html>
