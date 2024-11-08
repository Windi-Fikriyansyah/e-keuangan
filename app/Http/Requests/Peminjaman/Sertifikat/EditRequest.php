<?php

namespace App\Http\Requests\Peminjaman\Sertifikat;

use Illuminate\Foundation\Http\FormRequest;

use Illuminate\Support\Facades\Crypt;
use Illuminate\Support\Facades\DB;
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
        $dataSertifikat = DB::table('pinjamanSertifikat')
            ->where(['id' => $this->id])
            ->first();
        $this->redirect = 'peminjaman/sertifikat/edit/' . Crypt::encrypt($dataSertifikat->nomorSurat) . '/' . Crypt::encrypt($dataSertifikat->kodeSkpd);
        return [
            'nomorSurat' => 'required|max:255|unique:pinjamanSertifikat,nomorSurat,' . $this->id,
            'tanggalPinjam' => 'required|date',
            'nomorRegister' => 'required|max:255',
            'nomorSertifikat' => 'required|max:255',
            'nib' => 'nullable|max:255',
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
            'nomorSertifikat.required' => 'Nomor Sertifikat wajib diisi',
            'nomorSertifikat.max' => 'Nomor Sertifikat hanya bisa diisi 255 abjad',
            'nib.max' => 'NIB hanya bisa diisi 255 abjad',
            'nomorRangka.required' => 'Nomor Rangka wajib diisi',
            'nomorRangka.max' => 'Nomor Rangka hanya bisa diisi 255 abjad',
            'namaKsbtgn.required' => 'Nama Kepala Sub Bagian Tata Guna Tanah wajib diisi',
            'namaKsbtgn.max' => 'Nama Kepala Sub Bagian Tata Guna Tanah hanya bisa diisi 255 abjad',
            'nipKsbtgn.required' => 'NIP Kepala Sub Bagian Tata Guna Tanah wajib diisi',
            'nipKsbtgn.integer' => 'NIP Kepala Sub Bagian Tata Guna Tanah hanya bisa diisi angka',
            'noTelpKsbtgn.required' => 'Nomor telepon Kepala Sub Bagian Tata Guna Tanah wajib diisi',
            'noTelpKsbtgn.integer' => 'Nomor telepon Kepala Sub Bagian Tata Guna Tanah hanya bisa diisi angka',
        ];
    }
}
