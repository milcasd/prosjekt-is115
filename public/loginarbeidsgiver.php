<?php
session_start();
$feilmeldinger = [];

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Validering av obligatoriske felt 
    $obligatoriske_felt = ["bedriftsnavn", "passord"];
    foreach ($obligatoriske_felt as $felt) {
        if (empty($_POST[$felt])) {
            $feilmeldinger[] = "Feltet '$felt' er obligatorisk.";
        }
    }

    if (empty($feilmeldinger)) {
        require_once('../config/dtb.inc.php');

        $bedriftsnavn = $_POST["bedriftsnavn"];
        $passord = $_POST["passord"];

        $sql = "SELECT uid, bedriftsnavn, passord
                FROM arbeidsgiver 
                WHERE bedriftsnavn = :bedriftsnavn";
        $q = $pdo->prepare($sql);
        $q->bindParam(':bedriftsnavn', $bedriftsnavn, PDO::PARAM_STR);

        try {
            $q->execute();
        } catch (PDOException $e) {
            echo "Error querying database: " . $e->getMessage();
        }

        $resultat = $q->fetch(PDO::FETCH_OBJ);

        if ($resultat !== false && password_verify($passord, $resultat->passord)) {
            $_SESSION['arbeidsgiver'] = array(
                'uid' => $resultat->uid,
                'bedriftsnavn' => $resultat->bedriftsnavn,
                'innlogget' => true
            );
            

            header("Location: arbeidsgiverdash.php");
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
            <label for="bedriftsnavn">Bedriftsnavn:</label>
            <input type="text" name="bedriftsnavn" placeholder="Bedriftsnavn">
        </div>
        <div>
            <label for="passord">Passord:</label>
            <input type="password" name="passord" placeholder="Passord">
        </div>
        <section>
            <button type="submit">Logg Inn</button>
            <a href="registerarbeidsgiver.php">Register</a>
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