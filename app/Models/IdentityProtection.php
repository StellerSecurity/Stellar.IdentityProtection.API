<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Concerns\HasUuids;

class IdentityProtection extends Model
{

    use HasUuids;

    protected $table = 'identityprotection';

    protected $fillable = ['email', 'user_id', 'last_check', 'status'];

}
