<?php

namespace App\Imports;

use App\Models\MsAnggaran;
use Maatwebsite\Excel\Concerns\ToModel;
use Maatwebsite\Excel\Concerns\WithHeadingRow;
use Illuminate\Support\Facades\Log;

class MsAnggaranImport implements ToModel, WithHeadingRow
{
    public function model(array $row)
    {
        try {
            return new MsAnggaran([
                'kd_rek' => $row['kd_rek'] ?? null,
                'nm_rek' => $row['nm_rek'] ?? null,
                'anggaran_tahun' => $this->parseNumeric($row['anggaran_tahun'] ?? 0),
                'anggaran_tw1' => $this->parseNumeric($row['anggaran_tw1'] ?? 0),
                'anggaran_tw2' => $this->parseNumeric($row['anggaran_tw2'] ?? 0),
                'anggaran_tw3' => $this->parseNumeric($row['anggaran_tw3'] ?? 0),
                'anggaran_tw4' => $this->parseNumeric($row['anggaran_tw4'] ?? 0),
                'rek1' => $this->parseNumeric($row['rek1'] ?? 0),
                'rek2' => $this->parseNumeric($row['rek2'] ?? 0),
                'rek3' => $this->parseNumeric($row['rek3'] ?? 0),
                'rek4' => $this->parseNumeric($row['rek4'] ?? 0),
                'rek5' => $this->parseNumeric($row['rek5'] ?? 0),
                'rek6' => $this->parseNumeric($row['rek6'] ?? 0),
                'rek7' => $this->parseNumeric($row['rek7'] ?? 0),
                'rek8' => $this->parseNumeric($row['rek8'] ?? 0),
                'rek9' => $this->parseNumeric($row['rek9'] ?? 0),
                'rek10' => $this->parseNumeric($row['rek10'] ?? 0),
                'rek11' => $this->parseNumeric($row['rek11'] ?? 0),
                'rek12' => $this->parseNumeric($row['rek12'] ?? 0),
                'status_anggaran' => $row['status_anggaran'] ?? null,
                'status_anggaran_kas' => $row['status_anggaran_kas'] ?? null,
            ]);
        } catch (\Exception $e) {
            Log::error('Kesalahan saat mengimport data: ' . $e->getMessage());
            return null;
        }
    }

    private function parseNumeric($value)
    {
        return is_numeric($value) ? (float) $value : 0;
    }
}
