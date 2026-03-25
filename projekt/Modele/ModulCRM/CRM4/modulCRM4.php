<!DOCTYPE html>
<html lang="pl">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>CRM – Usuń klienta</title>
    <link rel="stylesheet" href="../../../css/crm.css">
</head>
<body class="crm crm-4">
<header>
    <h1>CRM – Usuń klienta</h1>
    <nav class="menu">
        <a href="../modulCRM.php">Menu CRM</a>
        <a href="javascript:history.back()">Wstecz</a>
    </nav>
</header>

<div class="container">
    <div class="card">
        <h2>Usuń klienta po ID</h2>
        <form method="POST" action="modulCRM4submit.php"
              onsubmit="return confirm('Na pewno usunąć klienta o tym ID?')">
            <label for="id">ID klienta:</label>
            <input type="number" id="id" name="id" placeholder="np. 1" min="1" required>
            <button type="submit" class="btn-delete">Usuń klienta</button>
        </form>

        <div class="powrot">
            <a href="../modulCRM.php">← Powrót do menu CRM</a>
        </div>
    </div>
</div>
</body>
</html>
