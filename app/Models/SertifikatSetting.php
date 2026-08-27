<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class SertifikatSetting extends Model
{
    protected $fillable = [
        'nama_penandatangan',
        'jabatan_penandatangan',
        'tempat_tanda_tangan',
        'logo_instansi',
        'ttd_image',
        'tipe_ttd'
    ];
    //
}

