<!DOCTYPE html>
<html lang="pl">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>System CRM opcja 1</title>
    <link rel="stylesheet" href="../style.css">
</head>
<body>
<header>
    <h1>CRM - Dodaj użytkownika</h1>
    <nav>
        <a href="../modulCRM.php">Menu CRM</a>
        <a href="javascript:history.back()">Wstecz</a>
    </nav>
</header>

<div class="container">
    <div class="card">
        <form method="post" action="modulCRM1submit.php">
    <input type="text" name="imie" required>
    <input type="text" name="mail" required>
    <input type="text" name="sub" required>
            <input type="submit" value="Zapisz">
        </form>
    </div>
</div>

</body>
</html>

<?php






?>