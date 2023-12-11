<?php
// Hjemside som vises om arbeidsgiver er innlogget 
include "../inc/aheader.php";
?>

<html>
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<link rel="stylesheet" type="text/css" href="../css/index.css">
<title>Stillinger i andre bedrifter</title>
<meta charset=utf-8>
</head>
<body>
  <section>
    <?php 
    include "jobbliste2.php";
    ?>
  </section>
</body>
</html>

<?php 
include "../inc/footer.php";
?>