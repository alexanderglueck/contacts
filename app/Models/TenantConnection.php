<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class TenantConnection extends Model
{
    protected $connection = 'system';

    protected $fillable = [
        'database'
    ];
}
