<?php
require_once "hr_model.php";

$model = new HRModel();

// Obsługa akcji POST
$message = "";
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (isset($_POST['action'])) {
        switch ($_POST['action']) {
            case 'add':
                $model->create($_POST['name'], $_POST['birthdate'], $_POST['department'], $_POST['level']);
                $message = "Pracownik dodany!";
                break;
            case 'delete':
                $model->delete($_POST['id']);
                $message = "Pracownik usunięty!";
                break;
        }
    }
}

// Pobieranie danych do wyświetlenia
$allEmployees = $model->getAll();
$oldestYoungest = $model->getOldestAndYoungest();
$averageAge = $model->getAverageAge();
$departmentCounts = $model->countByDepartment();

// Obsługa urodzin
$birthdays = [];
if (isset($_POST['birthday_date'])) {
    $birthdays = $model->getUpcomingBirthdays($_POST['birthday_date']);
}

// Obsługa poziomu
$levelCount = "";
if (isset($_POST['min_level'])) {
    $levelCount = $model->countByLevel($_POST['min_level']);
}
?>

<!DOCTYPE html>
<html lang="pl">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Moduł HR</title>
    <style>
        body { font-family: Arial, sans-serif; margin: 20px; background-color: #f4f4f4; }
        .container { max-width: 1200px; margin: 0 auto; background: white; padding: 20px; border-radius: 8px; box-shadow: 0 0 10px rgba(0,0,0,0.1); }
        h1 { color: #333; }
        .section { margin-bottom: 30px; padding: 15px; border: 1px solid #ddd; border-radius: 5px; }
        form { margin-bottom: 15px; }
        input, button { padding: 8px; margin: 5px 0; }
        button { background-color: #4CAF50; color: white; border: none; cursor: pointer; }
        button:hover { background-color: #45a049; }
        table { width: 100%; border-collapse: collapse; margin-top: 10px; }
        th, td { border: 1px solid #ddd; padding: 8px; text-align: left; }
        th { background-color: #f2f2f2; }
        .message { color: green; font-weight: bold; }
        .powrot { margin-top: 20px; }
        .powrot a { color: #007BFF; text-decoration: none; }
        .powrot a:hover { text-decoration: underline; }
    </style>
</head>
<body>
    <div class="container">
        <h1>Moduł HR - Zarządzanie Pracownikami</h1>

        <?php if ($message): ?>
            <div class="message"><?php echo $message; ?></div>
        <?php endif; ?>

        <div class="section">
            <h2>1. Dodaj pracownika</h2>
            <form method="POST">
                <input type="hidden" name="action" value="add">
                <input type="text" name="name" placeholder="Imię" required>
                <input type="date" name="birthdate" placeholder="Data urodzenia" required>
                <input type="text" name="department" placeholder="Dział" required>
                <input type="number" name="level" placeholder="Poziom" required>
                <button type="submit">Dodaj</button>
            </form>
        </div>

        <div class="section">
            <h2>2. Lista pracowników</h2>
            <table>
                <tr>
                    <th>ID</th>
                    <th>Imię</th>
                    <th>Data urodzenia</th>
                    <th>Dział</th>
                    <th>Poziom</th>
                </tr>
                <?php foreach ($allEmployees as $emp): ?>
                <tr>
                    <td><?php echo htmlspecialchars($emp['id']); ?></td>
                    <td><?php echo htmlspecialchars($emp['name']); ?></td>
                    <td><?php echo htmlspecialchars($emp['birthdate']); ?></td>
                    <td><?php echo htmlspecialchars($emp['department']); ?></td>
                    <td><?php echo htmlspecialchars($emp['level']); ?></td>
                </tr>
                <?php endforeach; ?>
            </table>
        </div>

        <div class="section">
            <h2>3. Usuń pracownika</h2>
            <form method="POST">
                <input type="hidden" name="action" value="delete">
                <input type="text" name="id" placeholder="ID pracownika" required>
                <button type="submit">Usuń</button>
            </form>
        </div>

        <div class="section">
            <h2>4. Najstarszy i najmłodszy</h2>
            <p>Najstarszy: <?php echo htmlspecialchars($oldestYoungest['oldest'] ?? 'Brak danych'); ?></p>
            <p>Najmłodszy: <?php echo htmlspecialchars($oldestYoungest['youngest'] ?? 'Brak danych'); ?></p>
        </div>

        <div class="section">
            <h2>5. Średni wiek</h2>
            <p>Średni wiek: <?php echo round($averageAge, 2); ?> lat</p>
        </div>

        <div class="section">
            <h2>6. Urodziny (w ciągu 2 tygodni od podanej daty)</h2>
            <form method="POST">
                <input type="date" name="birthday_date" required>
                <button type="submit">Sprawdź</button>
            </form>
            <?php if ($birthdays): ?>
                <table>
                    <tr>
                        <th>Imię</th>
                        <th>Data urodzenia</th>
                        <th>Dział</th>
                    </tr>
                    <?php foreach ($birthdays as $emp): ?>
                    <tr>
                        <td><?php echo htmlspecialchars($emp['name']); ?></td>
                        <td><?php echo htmlspecialchars($emp['birthdate']); ?></td>
                        <td><?php echo htmlspecialchars($emp['department']); ?></td>
                    </tr>
                    <?php endforeach; ?>
                </table>
            <?php endif; ?>
        </div>

        <div class="section">
            <h2>7. Liczba pracowników z poziomem >=</h2>
            <form method="POST">
                <input type="number" name="min_level" placeholder="Minimalny poziom" required>
                <button type="submit">Sprawdź</button>
            </form>
            <?php if ($levelCount !== ""): ?>
                <p>Liczba: <?php echo $levelCount; ?></p>
            <?php endif; ?>
        </div>

        <div class="section">
            <h2>8. Liczba na dział</h2>
            <table>
                <tr>
                    <th>Dział</th>
                    <th>Liczba pracowników</th>
                </tr>
                <?php foreach ($departmentCounts as $dep => $count): ?>
                <tr>
                    <td><?php echo htmlspecialchars($dep); ?></td>
                    <td><?php echo $count; ?></td>
                </tr>
                <?php endforeach; ?>
            </table>
        </div>

        <div class="powrot">
            <a href="../../index.html">Powrót do menu</a>
        </div>
    </div>
</body>
</html>