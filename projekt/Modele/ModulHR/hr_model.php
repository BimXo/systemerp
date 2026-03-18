<?php

class HRModel {
    private $file = "employees.txt";

    private function readData() {
        if (!file_exists($this->file)) {
            return [];
        }
        $data = file_get_contents($this->file);
        return json_decode($data, true) ?? [];
    }

    private function writeData($data) {
        file_put_contents($this->file, json_encode($data, JSON_PRETTY_PRINT));
    }

    public function getAll() {
        return $this->readData();
    }

    public function create($name, $birthdate, $department, $level) {
        $data = $this->readData();

        $employee = [
            "id" => uniqid(),
            "name" => $name,
            "birthdate" => $birthdate, // YYYY-MM-DD
            "department" => $department,
            "level" => (int)$level
        ];

        $data[] = $employee;
        $this->writeData($data);
    }

    public function delete($id) {
        $data = $this->readData();
        $data = array_filter($data, fn($e) => $e["id"] !== $id);
        $this->writeData(array_values($data));
    }

    public function update($id, $name, $birthdate, $department, $level) {
        $data = $this->readData();

        foreach ($data as &$e) {
            if ($e["id"] === $id) {
                $e["name"] = $name;
                $e["birthdate"] = $birthdate;
                $e["department"] = $department;
                $e["level"] = (int)$level;
            }
        }

        $this->writeData($data);
    }


    public function getOldestAndYoungest() {
        $data = $this->readData();

        usort($data, fn($a, $b) => strtotime($a["birthdate"]) - strtotime($b["birthdate"]));

        return [
            "oldest" => $data[0]["name"] ?? null,
            "youngest" => $data[count($data)-1]["name"] ?? null
        ];
    }

    public function getAverageAge() {
        $data = $this->readData();
        $today = time();

        $ages = array_map(function($e) use ($today) {
            return floor(($today - strtotime($e["birthdate"])) / (365*24*60*60));
        }, $data);

        return count($ages) ? array_sum($ages) / count($ages) : 0;
    }

    public function getUpcomingBirthdays($date) {
        $data = $this->readData();
        $base = strtotime($date);

        return array_filter($data, function($e) use ($base) {
            $bday = strtotime(date("Y") . "-" . date("m-d", strtotime($e["birthdate"])));
            return ($bday >= $base && $bday <= strtotime("+14 days", $base));
        });
    }

    public function countByLevel($minLevel) {
        $data = $this->readData();
        return count(array_filter($data, fn($e) => $e["level"] >= $minLevel));
    }

    public function countByDepartment() {
        $data = $this->readData();
        $result = [];

        foreach ($data as $e) {
            $dep = $e["department"];
            if (!isset($result[$dep])) {
                $result[$dep] = 0;
            }
            $result[$dep]++;
        }

        return $result;
    }
}