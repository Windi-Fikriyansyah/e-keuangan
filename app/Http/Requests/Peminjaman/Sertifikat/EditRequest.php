<?php

namespace App\Http\Requests\Peminjaman\Sertifikat;

use Illuminate\Foundation\Http\FormRequest;

class EditRequest extends FormRequest
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
        return [
            'nomorSurat' => 'required|max:255|unique:pinjamanSertifikat,nomorSurat,' . $this->id,
            'tanggalPinjam' => 'required|date',
            'nomorRegister' => 'required|max:255',
            'nomorSertifikat' => 'required|max:255',
            'nib' => 'required|max:255',
            'namaKsbtgn' => 'required|max:255',
            'nipKsbtgn' => 'required|integer',
            'noTelpKsbtgn' => 'required|regex:/^[0-9-]+$/',
        ];
    }

    public function messages()
    {
        return [
            'nomorRegister.required' => 'Nomor Register wajib dipilih',
            'nomorRegister.max' => 'Nomor Register hanya bisa diisi 255 abjad',
            'nomorSertifikat.required' => 'Nomor BPKB wajib diisi',
            'nomorSertifikat.max' => 'Nomor BPKB hanya bisa diisi 255 abjad',
            'nib.required' => 'Nomor Polisi wajib diisi',
            'nib.max' => 'Nomor Polisi hanya bisa diisi 255 abjad',
            'nomorRangka.required' => 'Nomor Rangka wajib diisi',
            'nomorRangka.max' => 'Nomor Rangka hanya bisa diisi 255 abjad',
            'namaKsbtgn.required' => 'Nama Pengurus Barang Pengguna wajib diisi',
            'namaKsbtgn.max' => 'Nama Pengurus Barang Pengguna hanya bisa diisi 255 abjad',
            'nipKsbtgn.required' => 'NIP Pengurus Barang Pengguna wajib diisi',
            'nipKsbtgn.integer' => 'NIP Pengurus Barang Pengguna hanya bisa diisi angka',
            'noTelpKsbtgn.required' => 'Nomor telepon Pengurus Barang Pengguna wajib diisi',
            'noTelpKsbtgn.integer' => 'Nomor telepon Pengurus Barang Pengguna hanya bisa diisi angka',
        ];
    }
}
