<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Security_User extends Model
{
    public $timestamps = false;
    protected $primaryKey = 'id_user';
    protected $table = 'security_user';
}
