<?php
class SalesModel {
    private $filename = "sales_db.txt";

    // Zmienione na public, aby kontroler mógł czytać plik bezpośrednio
    public function readData() {
        if (!file_exists($this->filename)) return [];
        $rows = [];
        if (($handle = fopen($this->filename, "r")) !== FALSE) {
            while (($data = fgetcsv($handle, 1000, ";")) !== FALSE) {
                $rows[] = $data;
            }
            fclose($handle);
        }
        return $rows;
    }

    public function saveData($data) {
        $handle = fopen($this->filename, "w");
        foreach ($data as $row) {
            fputcsv($handle, $row, ";");
        }
        fclose($handle);
    }

    public function create($customerId, $product, $amount, $date) {
        $data = $this->readData();
        $id = substr(uniqid(), -8);
        $data[] = [$id, $customerId, $product, $amount, $date];
        $this->saveData($data);
        return $id;
    }

    public function getAll() {
        return $this->readData();
    }
    
    // ... reszta metod (update/delete) pozostaje bez zmian
}