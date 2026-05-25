<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ContactNonDuplicate extends Model
{
    protected $fillable = ['contact_a_id', 'contact_b_id', 'team_id', 'created_by'];
}
