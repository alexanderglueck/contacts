<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class TFABackupCode extends Model
{
    protected $table = 'tfa_backup_codes';
    //

    protected $fillable = ['value'];
}
