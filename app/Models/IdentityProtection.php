<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class IdentityProtection extends Model
{

    protected $table = 'identityprotection';

    protected $fillable = ['email', 'user_id', 'last_check', 'status'];

}
