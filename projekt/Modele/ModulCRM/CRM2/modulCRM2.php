<?php
$tekst = str_replace(";"," ",file_get_contents('../../crm.txt'));
?>

<!DOCTYPE html>
<html lang="pl">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>CRM - Podgląd danych</title>
    <link rel="stylesheet" href="../style.css">
</head>
<body>
<header>
    <h1>CRM - Podgląd danych</h1>
    <nav>
        <a href="../modulCRM.php">Menu CRM</a>
        <a href="javascript:history.back()">Wstecz</a>
    </nav>
</header>

<div class="container">
    <div class="card">
        <pre><?php echo htmlspecialchars($tekst); ?></pre>
    </div>
</div>

</body>
</html>