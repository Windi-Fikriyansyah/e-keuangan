<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class MasterSertifikat extends Model
{
    protected $table = 'masterSertifikat';

    protected $primaryKey = 'id';

    protected $fillable = [
        'nomorRegister',
        'nib',
        'nomorSertifikat',
        'tanggalSertifikat',
        'luas',
        'hak',
        'pemegangHak',
        'asalUsul',
        'alamat',
        'sertifikatAsli',
        'balikNama',
        'penggunaan',
        'keterangan',
        'createdDate',
        'createdUsername',
        'updatedDate',
        'updatedUsername',
        'kodeSkpd',
        'statusPinjam',
        'statusSertifikat',
        'Nibbar',
        'file',
    ];



    public $timestamps = false;
}
