<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class MsAnggaran extends Model
{
    use HasFactory;

    public $timestamps = false;
    protected $table = 'ms_anggaran';

    protected $fillable = [
        'kd_rek',
        'nm_rek',
        'anggaran_tahun',
        'anggaran_tw1',
        'anggaran_tw2',
        'anggaran_tw3',
        'anggaran_tw4',
        'rek1',
        'rek2',
        'rek3',
        'rek4',
        'rek5',
        'rek6',
        'rek7',
        'rek8',
        'rek9',
        'rek10',
        'rek11',
        'rek12',
        'status_anggaran',
        'status_anggaran_kas',
    ];
}
