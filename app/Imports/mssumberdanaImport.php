<?php

namespace App\Imports;

use App\Models\MsSumberDana;
use Maatwebsite\Excel\Concerns\ToModel;
use Maatwebsite\Excel\Concerns\WithHeadingRow;
use Illuminate\Support\Facades\Log;

class mssumberdanaImport implements ToModel, WithHeadingRow
{
    public function model(array $row)
    {
        try {
            $data = new MsSumberDana([
                'kd_dana' => $row['kd_dana'] ?? null,
                'nm_dana' => $row['nm_dana'] ?? null,
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
            ]);

            if ($data->save()) {
                return $data;
            }
            throw new \Exception("Gagal menyimpan data");
        } catch (\Exception $e) {
            Log::error('Kesalahan saat mengimport data: ' . $e->getMessage());
            return null;
        }
    }


    public function rules(): array
    {
        return [
            'kd_dana' => 'required',
            'nm_dana' => 'required',
        ];
    }

    public function onError(\Throwable $e)
    {
        Log::error('Error pada import: ' . $e->getMessage());
    }

    private function parseNumeric($value)
    {
        return is_numeric($value) ? (float) $value : 0;
    }
}
