<?php
// Statisk side som kunne vist meldinger fra en arbeidsgiver 
require_once('../config/dtb.inc.php');

// Hent meldinger fra databasen
$sql = "SELECT * FROM meldinger";
$resultat = $pdo->query($sql);

// Les meldingene og lagre dem i en array
$meldinger = $resultat->fetchAll(PDO::FETCH_ASSOC);
?>

<!DOCTYPE html>
<html lang="no">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Meldinger</title>
    
</head>
<body>

    <div class="container">
        <h2>Meldinger</h2>
        <div id="meldingsliste">
            <?php
            // Vis meldinger på nettsiden
            foreach ($meldinger as $melding) {
                echo "<p><strong>Sender:</strong> " . $melding['sender'] . "</p>";
                echo "<p><strong>Melding:</strong> " . $melding['melding'] . "</p>";
                echo "<p><strong>Sendt:</strong> " . $melding['tid'] . "</p>";
            }
            ?>
        </div>
        
        <div class="container">
            <form id="meldingsform" action="prosesser/send_melding.php" method="post">
                <input type="text" name="melding" placeholder="Skriv inn melding" required>
                <br>
                <input type="submit" value="Send">
            </form>
        </div>
    </div>

</body>