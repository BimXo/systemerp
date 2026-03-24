<!DOCTYPE html>
<html lang="pl">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>CRM - Edycja rekordu</title>
    <link rel="stylesheet" href="../../../css/crm.css">
</head>
<body class="crm crm-3">
<header>
    <h1>CRM - Edycja rekordu</h1>
    <nav>
        <a href="../modulCRM.php">Menu CRM</a>
        <a href="javascript:history.back()">Wstecz</a>
    </nav>
</header>

<div class="container">
    <div class="card">
        <form method="post" action="modulCRM3submit.php">
            <input type="number" name="id" placeholder="ID rekordu" required>
            <button type="submit">Wyszukaj</button>
        </form>
    </div>
</div>

</body>
</html>