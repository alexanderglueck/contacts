<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class TFABackupCode extends Model
{
    protected $connection = 'system';

    protected $table = 'tfa_backup_codes';

    protected $fillable = ['value'];
}
