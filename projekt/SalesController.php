<?php
class SalesController {
    public $model; // public, aby widok miał łatwy dostęp

    public function __construct($model) {
        $this->model = $model;
    }

    public function getHighestRevenueTransaction() {
        $data = $this->model->getAll(); // Poprawione z . na ->
        if (empty($data)) return null;
        usort($data, function($a, $b) { return $b[3] <=> $a[3]; });
        return $data[0];
    }

    public function getHighestRevenueProduct() {
        $data = $this->model->getAll();
        $revenues = [];
        foreach ($data as $row) {
            $revenues[$row[2]] = ($revenues[$row[2]] ?? 0) + $row[3];
        }
        arsort($revenues);
        return !empty($revenues) ? key($revenues) : null;
    }

    public function getStatsBetweenDates($start, $end) {
        $data = $this->model->getAll();
        $stats = ['count' => 0, 'sum' => 0];
        foreach ($data as $row) {
            if ($row[4] >= $start && $row[4] <= $end) {
                $stats['count']++;
                $stats['sum'] += (float)$row[3];
            }
        }
        return $stats;
    }
}