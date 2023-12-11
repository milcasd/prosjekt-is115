<?php
session_start();
$feilmeldinger = [];

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Validering av obligatoriske felt
    $obligatoriske_felt = ["epost", "passord"];
    foreach ($obligatoriske_felt as $felt) {
        if (empty($_POST[$felt])) {
            $feilmeldinger[] = "Feltet '$felt' er obligatorisk.";
        }
    }

    // Validering av email 
    if (!filter_var($_POST["epost"], FILTER_VALIDATE_EMAIL)) {
        $feilmeldinger[] = "Ugyldig e-postformat.";
    }

    if (empty($feilmeldinger)) {
        require_once('../config/dtb.inc.php');

        $epost = $_POST["epost"];
        $passord = $_POST["passord"];

        $sql = "SELECT uid, epost, passord
                FROM brukere 
                WHERE epost = :epost";
        $q = $pdo->prepare($sql);
        $q->bindParam(':epost', $epost, PDO::PARAM_STR);

        try {
            $q->execute();
        } catch (PDOException $e) {
            echo "Error querying database: " . $e->getMessage();
        }

        //Henter ut resultater og verifiserer passordet 
        $resultat = $q->fetch(PDO::FETCH_OBJ);

        if ($resultat !== false && password_verify($passord, $resultat->passord)) {
            $_SESSION['uid'] = $resultat->uid;
            $_SESSION['epost'] = $resultat->epost;
            $_SESSION['bruker']['innlogget'] = true;

            header("Location: brukerdash.php");
            exit();
        } else {
            echo "Feil brukernavn eller passord";
        }
    }
}
?>

<!DOCTYPE html>
<html lang="no">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="https://www.phptutorial.net/app/css/style.css">
    <title>Login</title>
</head>
<body>
<main>
    <form method="post">
        <h1>Login</h1>
        <div>
            <label for="epost">Brukernavn:</label>
            <input type="text" name="epost" placeholder="E-post">
        </div>
        <div>
            <label for="passord">Passord:</label>
            <input type="password" name="passord" placeholder="Passord">
        </div>
        <section>
            <button type="submit">Logg Inn</button>
            <a href="registerbruker.php">Register</a>
        </section>
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