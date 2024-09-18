<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Skpd extends Model
{
    protected $table = 'masterSkpd';

    protected $primaryKey = 'id';

    protected $fillable = [
        'kodeSkpd',
        'namaSkpd',
    ];


    public $timestamps = false;
}
