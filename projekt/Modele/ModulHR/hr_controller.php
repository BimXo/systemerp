<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Document</title>
</head>
<body>
    
</body>
</html>
<?php
require_once "hr_model.php";

$model = new HRModel();

while (true) {
    echo "\nHR MENU\n";
    echo "1. Dodaj pracownika\n";
    echo "2. Lista pracowników\n";
    echo "3. Usuń\n";
    echo "4. Najstarszy i najmłodszy\n";
    echo "5. Średni wiek\n";
    echo "6. Urodziny (2 tygodnie)\n";
    echo "7. Liczba z poziomem\n";
    echo "8. Liczba na dział\n";
    echo "0. Wyjście\n";

    $choice = trim(fgets(STDIN));

    switch ($choice) {
        case 1:
            echo "Imię: ";
            $name = trim(fgets(STDIN));

            echo "Data urodzenia (YYYY-MM-DD): ";
            $birth = trim(fgets(STDIN));

            echo "Dział: ";
            $dep = trim(fgets(STDIN));

            echo "Poziom: ";
            $level = trim(fgets(STDIN));

            $model->create($name, $birth, $dep, $level);
            break;

        case 2:
            print_r($model->getAll());
            break;

        case 3:
            echo "ID: ";
            $id = trim(fgets(STDIN));
            $model->delete($id);
            break;

        case 4:
            print_r($model->getOldestAndYoungest());
            break;

        case 5:
            echo "Średni wiek: " . $model->getAverageAge() . "\n";
            break;

        case 6:
            echo "Podaj datę: ";
            $date = trim(fgets(STDIN));
            print_r($model->getUpcomingBirthdays($date));
            break;

        case 7:
            echo "Min poziom: ";
            $lvl = trim(fgets(STDIN));
            echo $model->countByLevel($lvl) . "\n";
            break;

        case 8:
            print_r($model->countByDepartment());
            break;

        case 0:
            exit;
    }
}
?>