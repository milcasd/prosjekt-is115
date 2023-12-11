<?php
require_once('../config/dtb.inc.php');
session_start();

// Sjekk om brukeren er logget inn
if (!isset($_SESSION['arbeidsgiver']['innlogget']) || $_SESSION['arbeidsgiver']['innlogget'] !== true) {
    header("Location: loginarbeidsgiver.php"); // Omdiriger til innloggingssiden hvis ikke innlogget
    exit();
}

// Logg ut brukeren
if (isset($_POST['loggut'])) {
    session_destroy();
    header("Location: hjem.php");
    exit();
}

// Definerer matrise for feilmeldinger
$meldinger = [];

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    if (isset($_REQUEST['last-opp-send'])) {
        // Filopplasting
        if (is_uploaded_file($_FILES['bilde']['tmp_name'])) {
            // Henter informasjon om fil
            $filtype = $_FILES['bilde']['type'];
            $filstr = $_FILES['bilde']['size'];

            $aks_filtyper = array("jpeg" => "image/jpeg", "png" => "image/png");
            $max_filstr = 1530000; // i byte

            $mappe = "/prosjekt/uploads/";
            $filsystem_mappe = $_SERVER['DOCUMENT_ROOT'] . $mappe;
            $web_mappe = $_SERVER['REQUEST_SCHEME'] . "://" . $_SERVER['HTTP_HOST'] . dirname($_SERVER['PHP_SELF']) . $mappe;

            // Ingen mappe ved det navnet?
            if (!file_exists($filsystem_mappe)) {
                if (!mkdir($filsystem_mappe, 0777, true)) {
                    die("Kan ikke opprette mappe... " . $filsystem_mappe);
                }
            }

            // Konstruerer filnavn
            $suffix = array_search($filtype, $aks_filtyper);

            // Hvis filnavnet eksisterer allerede av en eller annen grunn
            do {
                $filnavn  = substr(md5(date('YmdHisu')), 0, 5) . '.' . $suffix;
            } while (file_exists($filsystem_mappe . $filnavn));

            // Feil?
            if (!in_array($filtype, $aks_filtyper)) {
                $typer = implode(", ", array_keys($aks_filtyper));
                $meldinger['error'][] = "Ugyldig filtype (kun <em>" . $typer . "</em> er akseptert)";
            }
            if ($filstr > $max_filstr) {
                $meldinger['error'][] = "Filstørrelsen (" . round($filstr / 1048576, 2) . " MB) overgår maksimal filstørrelse (" . round($max_filstr / 1048576, 2) . " MB)"; // Bin. konvertering
            }

            // Hvis alt fungerer
            if (empty($meldinger['error'])) {
                // Flytter filen din den skal være
                $filsti = $filsystem_mappe . $filnavn;
                $opplastet_fil = move_uploaded_file($_FILES['bilde']['tmp_name'], $filsti);

                if (!$opplastet_fil) {
                    $meldinger['error'][] = "Filen kunne ikke bli lastet opp. Feil: " . error_get_last()['message'];
                } else {
                    $meldinger['success'][] = "Filen ble lastet opp og finnes her: <strong>" . $filsti . "</strong> (filsystemref.) eller her <strong>" . '<a href="' . $web_mappe . $filnavn . '">' . $web_mappe . $filnavn . "</a></strong> (URL)";
                }
            }
        } else {
            $meldinger['error'][] = "Ingen fil er valgt.";
        }
    }
}

// Database insetting
if (empty($meldinger['error'])) {
    $stillingstittel = isset($_POST["stillingstittel"]) ? $_POST["stillingstittel"] : null;
    $beskrivelse = isset($_POST["beskrivelse"]) ? $_POST["beskrivelse"] : null;
    $sted = isset($_POST["sted"]) ? $_POST["sted"] : null;
    $publiseringsdato = isset($_POST["publiseringsdato"]) ? $_POST["publiseringsdato"] : null;
    $frist = isset($_POST["frist"]) ? $_POST["frist"] : null;

    // Sjekker om nødvendige felt er fylt inn
    if ($stillingstittel === null || $beskrivelse === null || $sted === null || $publiseringsdato === null || $frist === null) {
        $meldinger['error'][] = "Alle felt må fylles ut.";
    } else {
        // Bruker filnavnet for insetting
        $bilde = isset($filnavn) ? $filnavn : null;

        // Sjekker om bilde har blitt lagt og legger til et tomt bilde
        $bilde = ($bilde !== null) ? $bilde : 'default.jpg';

        $sql = "INSERT INTO jobber (stillingstittel, beskrivelse, sted, publiseringsdato, frist, bilde) 
                VALUES (:stillingstittel, :beskrivelse, :sted, :publiseringsdato, :frist, :bilde)";
        $q = $pdo->prepare($sql);

        $q->bindParam(':stillingstittel', $stillingstittel, PDO::PARAM_STR);
        $q->bindParam(':beskrivelse', $beskrivelse, PDO::PARAM_STR);
        $q->bindParam(':sted', $sted, PDO::PARAM_STR);
        $q->bindParam(':publiseringsdato', $publiseringsdato, PDO::PARAM_STR);
        $q->bindParam(':frist', $frist, PDO::PARAM_STR);
        $q->bindParam(':bilde', $bilde, PDO::PARAM_STR); // Use the file name

        try {
            $q->execute();
            echo "Jobb lagt ut!";

            // Redirect to hjem2.php after successfully posting a job
            header("Location: hjem2.php");
            exit();
        } catch (PDOException $e) {
            // Handle database insertion errors
            echo "Feil ved utlegging av jobb: " . $e->getMessage();
        }
    }
}
?>
<!DOCTYPE html>
<html lang="no">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Legg ut Jobb</title>
    <link rel="stylesheet" href="https://www.phptutorial.net/app/css/style.css">
</head>
<body>
<main>
    <?php
    // Vis feilmeldinger hvis det er noen
    if (!empty($meldinger['error'])) {
        echo '<div class="feilmelding">';
        foreach ($meldinger['error'] as $feilmelding) {
            echo "<p>$feilmelding</p>";
        }
        echo '</div>';
    }
    ?>

    <form method="post" enctype="multipart/form-data">
        <div>
            <h2>Legg ut Jobb</h2>
            <label for="stillingstittel">Stillingstittel</label>
            <input type="text" name="stillingstittel" placeholder="Stillingstittel" required>
        </div>
        <div>
            <label for="bilde">Bilde</label>
            <input type="file" name="bilde" accept="image/*" required>
        </div>
        <div>
            <label for="beskrivelse">Beskrivelse</label>
            <textarea name="beskrivelse" placeholder="Beskrivelse" required></textarea>
        </div>
        <div>
            <label for="sted">Sted</label>
            <input type="text" name="sted" placeholder="Sted" required>
        </div>
        <div>
            <label for="publiseringsdato">Publiseringsdato</label>
            <input type="date" name="publiseringsdato" required>
        </div>
        <div>
            <label for="frist">Frist</label>
            <input type="date" name="frist" required>
        </div>
        <button type="submit" name="last-opp-send">Legg ut Jobb</button>
    </form>
</main>
</body>
</html>
