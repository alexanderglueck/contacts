<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class TenantConnection extends Model
{
    protected $fillable = [
        'database'
    ];
}
