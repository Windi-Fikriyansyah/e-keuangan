<?php

namespace App\Http\Requests\Peminjaman\Sertifikat;

use Illuminate\Foundation\Http\FormRequest;

class TambahRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'nomorSurat' => 'nullable|max:255|unique:pinjamanSertifikat,nomorSurat',
            'tanggalPinjam' => 'required|date',
            'nomorRegister' => 'required|max:255',
            'nomorSertifikat' => 'required|max:255',
            'nib' => 'required|max:255',
            'tanggal' => 'required|date',
            'pemegangHak' => 'required|max:255',
            'luas' => 'required|numeric',
            'peruntukan' => 'required|max:255',
            'namaKsbtgn' => 'required|max:255',
            'nipKsbtgn' => 'required|regex:/^[0-9-]+$/',
            'noTelpKsbtgn' => 'required|regex:/^[0-9-]+$/',

        ];
    }

    public function messages()
    {
        return [
            // ... (keep existing messages)
            'tanggal.required' => 'Tanggal wajib diisi',
            'tanggal.date' => 'Tanggal harus berformat tanggal yang valid',
            'pemegangHak.required' => 'Pemegang Hak wajib diisi',
            'pemegangHak.max' => 'Pemegang Hak hanya bisa diisi 255 karakter',
            'luas.required' => 'Luas wajib diisi',
            'luas.numeric' => 'Luas harus berupa angka',
            'peruntukan.required' => 'Peruntukan wajib diisi',
            'peruntukan.max' => 'Peruntukan hanya bisa diisi 255 karakter',
            'nipKsbtgn.regex' => 'NIP Ksbtgn hanya bisa diisi angka dan tanda hubung (-)',
            'noTelpKsbtgn.regex' => 'Nomor telepon Ksbtgn hanya bisa diisi angka dan tanda hubung (-)',

        ];
    }
}
