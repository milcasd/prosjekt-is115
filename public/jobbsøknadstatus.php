<!DOCTYPE html>
<html lang="no">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Jobbannonser</title>
    <style>
        table {
            border-collapse: collapse;
            width: 100%;
        }

        th, td {
            border: 1px solid #ddd;
            padding: 8px;
            text-align: left;
        }

        th {
            background-color: #f2f2f2;
        }
    </style>
</head>
<body>

<?php
include "../inc/bheader.php";
require "../config/dtb.inc.php";

$sql = "SELECT stillingstittel, frist FROM jobber";
$q = $pdo->prepare($sql);

try {
    $q->execute();
} catch (PDOException $e) {
    //echo "Error querying database: " . $e->getMessage() . "<br>";
}

$jobber = $q->fetchAll(PDO::FETCH_OBJ);

// Sjekk om det er resultater
if (count($jobber) > 0) {
    echo "<table>";
    echo "<tr><th>Stillingstittel</th><th>Frist</th><th>Status</th></tr>";
    
    // Loop gjennom resultatene
    foreach ($jobber as $jobb) {
        $stillingstittel = $jobb->stillingstittel;
        $frist = $jobb->frist;

        // Sjekk om annonser er avsluttet eller pågående basert på datoen
        $status = (strtotime($frist) > time()) ? "PÅGÅENDE" : "AVSLUTTET";

        echo "<tr>";
        echo "<td>$stillingstittel</td>";
        echo "<td>$frist</td>";
        echo "<td>$status</td>";
        echo "</tr>";
    }

    echo "</table>";
} else {
    echo "Ingen jobbannonser funnet.";
}

include "../inc/footer.php";
?>

</body>
</html>