<?php
require_once('../config/dtb.inc.php');

// Henter alle jobber fra databasen 
$sql = "SELECT * FROM jobber";
$q = $pdo->query($sql);
$jobs = $q->fetchAll(PDO::FETCH_ASSOC);

// Filterering etter title og sted
$filterTitle = isset($_POST['title']) ? $_POST['title'] : '';
$filterPlace = isset($_POST['place']) ? $_POST['place'] : '';

if (!empty($filterTitle)) {
    $jobs = array_filter($jobs, function ($job) use ($filterTitle) {
        return stripos($job['stillingstittel'], $filterTitle) !== false;
    });
}

if (!empty($filterPlace)) {
    $jobs = array_filter($jobs, function ($job) use ($filterPlace) {
        return stripos($job['sted'], $filterPlace) !== false;
    });
}
?>
<!DOCTYPE html>
<html lang="no">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Vis Jobber</title>
    <style>
        body {
            background-color: #f5f5f5;
            font-family: Arial, sans-serif;
            color: #333;
        }

        h2 {
            color: #91b88e;
        }

        form {
            margin-bottom: 20px;
        }

        label {
            display: block;
            margin-bottom: 5px;
            color: #333;
        }

        input {
            padding: 8px;
            margin-bottom: 10px;
            width: 200px;
        }

        button {
            background-color: #91b88e;
            color: #fff;
            padding: 10px;
            cursor: pointer;
            border: none;
        }

        button:hover {
            background-color: #749c73;
        }

        .job-container {
            display: flex;
            flex-wrap: wrap;
            gap: 20px;
        }

        .job-box {
            border: 1px solid #ddd;
            padding: 10px;
            width: 300px;
            background-color: #fff;
            box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
            overflow: auto; 
        }

        .job-image {
            max-width: 100%;
            height: 200px;
        }

        strong {
            color: #91b88e;
        }

        /* Justering av farge innenfor boxene */
        .job-box h3,
        .job-box p {
            color: #333;
        }
    </style>
</head>
<body>
    <h2>Ledige Jobber</h2>

    <!-- Filterering form med post  -->
    <form method="post">
        <label for="title">Filtrer etter stillingstittel:</label>
        <input type="text" name="title" id="title" value="<?= htmlspecialchars($filterTitle) ?>">

        <label for="place">Filtrer etter sted:</label>
        <input type="text" name="place" id="place" value="<?= htmlspecialchars($filterPlace) ?>">

        <button type="submit">Filtrer</button>
    </form>

    <!-- Viser jobber -->
    <div class="job-container">
        <?php foreach ($jobs as $job) : ?>
            <div class="job-box">
                <h3><?= htmlspecialchars($job['stillingstittel']) ?></h3>
                <img class="job-image" src="/prosjekt/uploads/<?= htmlspecialchars($job['bilde']) ?>" alt="">
                <p><strong>Sted:</strong> <?= htmlspecialchars($job['sted']) ?></p>
                <p><strong>Beskrivelse:</strong> <?= htmlspecialchars($job['beskrivelse']) ?></p>
                <p><strong>Publiseringsdato:</strong> <?= htmlspecialchars($job['publiseringsdato']) ?></p>
                <p><strong>Frist:</strong> <?= htmlspecialchars($job['frist']) ?></p>

                <!-- Link til jobbsøknadsside -->
                <p><button type="submit"><a href="jobbsøknad.php">Søk nå</a><button</p>
            </div>
        <?php endforeach; ?>
    </div>
</body>
</html>
