<!DOCTYPE html>
<html lang="pl">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>CRM - Usuń rekord</title>
    <link rel="stylesheet" href="../style.css">
</head>
<body>
<header>
    <h1>CRM - Usuń rekord</h1>
    <nav>
        <a href="../modulCRM.php">Menu CRM</a>
        <a href="javascript:history.back()">Wstecz</a>
    </nav>
</header>

<div class="container">
    <div class="card">
        <form method="post" action="modulCRM4submit.php">
            <input type="number" name="id" placeholder="ID rekordu" required>
            <button type="submit">Usuń</button>
        </form>
    </div>
</div>

</body>
</html>