<?php

namespace App\Http\Requests\Peminjaman\BPKB;

use Illuminate\Foundation\Http\FormRequest;

class TambahRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        $this->redirect = 'peminjaman/bpkb/create';

        return [
            'nomorSurat' => 'nullable|max:255|unique:pinjamanBpkb,nomorSurat',
            'tanggalPinjam' => 'required|date',
            'nomorRegister' => 'required|max:255',
            'nomorBpkb' => 'required|max:255',
            'nomorPolisi' => 'required|max:255',
            'nomorRangka' => 'required|max:255',
            'namaPbp' => 'required|max:255',
            'nipPbp' => 'required|integer',
            'nomorTelpPbp' => 'required|integer',
            'keperluan' => 'required|string|max:5000'
        ];
    }

    public function messages()
    {
        return [
            'nomorRegister.required' => 'Nomor Register wajib dipilih',
            'nomorRegister.max' => 'Nomor Register hanya bisa diisi 255 abjad',
            'nomorBpkb.required' => 'Nomor BPKB wajib diisi',
            'nomorBpkb.max' => 'Nomor BPKB hanya bisa diisi 255 abjad',
            'nomorPolisi.required' => 'Nomor Polisi wajib diisi',
            'nomorPolisi.max' => 'Nomor Polisi hanya bisa diisi 255 abjad',
            'nomorRangka.required' => 'Nomor Rangka wajib diisi',
            'nomorRangka.max' => 'Nomor Rangka hanya bisa diisi 255 abjad',
            'namaPbp.required' => 'Nama Pengurus Barang Pengguna wajib diisi',
            'namaPbp.max' => 'Nama Pengurus Barang Pengguna hanya bisa diisi 255 abjad',
            'nipPbp.required' => 'NIP Pengurus Barang Pengguna wajib diisi',
            'nipPbp.integer' => 'NIP Pengurus Barang Pengguna hanya bisa diisi angka',
            'nomorTelpPbp.required' => 'Nomor telepon Pengurus Barang Pengguna wajib diisi',
            'nomorTelpPbp.integer' => 'Nomor telepon Pengurus Barang Pengguna hanya bisa diisi angka',
            'keperluan.required' => 'Keperluan wajib diisi',
            'keperluan.max' => 'Keperluan hanya bisa diisi 5000 abjad',
        ];
    }
}
