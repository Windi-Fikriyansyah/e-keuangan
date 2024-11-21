<?php

namespace App\Models;
use Illuminate\Database\Eloquent\Model;

class Bpkb extends Model
{

    // Table name
    protected $table = 'masterBpkb'; // Sesuaikan dengan nama tabel Anda

    // Primary key
    protected $primaryKey = 'id';

    // Indicates if the IDs are auto-incrementing
    public $incrementing = true;

    // Indicates if the model should be timestamped
    public $timestamps = false;

    // The attributes that are mass assignable
    protected $fillable = [
        'id',
        'nomorRegister',
        'nomorBpkb',
        'nomorPolisi',
        'namaPemilik',
        'jenis',
        'merk',
        'tipe',
        'model',
        'tahunPembuatan',
        'tahunPerakitan',
        'isiSilinder',
        'warna',
        'alamat',
        'nomorRangka',
        'nomorMesin',
        'keterangan',
        'nomorPolisiLama',
        'nomorBpkbLama',
        'createdDate',
        'createdUsername',
        'updatedDate',
        'updatedUsername',
        'kodeSkpd',
        'statusBpkb',
        'statusPinjam',
        'Nibbar',
        'namapenerimakendaraan',
        'filesuratpenunjukan',
        'fileba',
        'filepaktaintegritas',
    ];


}
