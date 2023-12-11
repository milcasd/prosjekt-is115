<?php
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
?>

<!DOCTYPE html>
<html lang="no">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Brukerdash</title>
    <style> 
        body {
            font-family: 'Arial', sans-serif;
            margin: 0;
            padding: 0;
            background-color: #f4f4f4;
        }

        h1 {
            text-align: center;
            color: #333;
            margin-top: 20px;
        }

        ul {
            list-style: none;
            padding: 0;
            width: 300px;
            margin: 20px auto;
            background-color: #fff;
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1); /* Skygge */
            border-radius: 8px;
        }

        li {
            border-bottom: 1px solid #eee;
        }

        li:last-child {
            border-bottom: none;
        }

        a {
            display: block;
            text-decoration: none;
            color: #444; /* Link farge for bedre kontrast */
            padding: 15px;
            transition: background-color 0.3s ease; /* Myk overgang for hover */
        }

        a:hover {
            background-color: #f9f9f9;
            color: #333; 
        }

    </style>
</head>
<body>
    <h1>Velkommen!</h1>
    <p>Du er nå logget inn.</p>

    <h3>Velkommen til ditt dashboard</h3>
    <ul>
        <li><a href="hjem1.php">Hjem</a></li>
        <li><a href="jobbliste2.php">Se jobber</a></li>
        <li><a href="meldinger.php">Meldinger</a></li>
        <li><a href="jobbsøknadstatus.php">Status på lagt ut stillinger</a></li>
        <form method="post">
        <button type="submit" name="loggut" class="knapp">Logg Ut</button>
    </form>
    </ul>
</body>
</html>