<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class MasterTtd extends Model
{
    protected $table = 'masterTtd';

    protected $primaryKey = 'id';

    protected $fillable = [
        'nip',
        'nama',
        'jabatan',
        'pangkat',
        'kodeSkpd',
    ];

    public function Skpd()
    {
        return $this->belongsTo(Skpd::class, 'kodeSkpd', 'kodeSKPD');
    }

    public $timestamps = false;
}
