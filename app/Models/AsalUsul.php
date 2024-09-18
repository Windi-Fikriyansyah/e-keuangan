<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class AsalUsul extends Model
{
    protected $table = 'masterAsalUsulTanah';

    protected $primaryKey = 'id';

    protected $fillable = [
        'nama',
    ];



    public $timestamps = false;
}
