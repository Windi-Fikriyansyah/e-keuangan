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
        'tanggal',
        'luas',
        'pemegangHak',
        'createdDate',
        'createdUsername',
        'updatedDate',
        'updatedUsername'
    ];



    public $timestamps = false;
}
