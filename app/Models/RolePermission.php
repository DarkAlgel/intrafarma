<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class RolePermission extends Model
{
    protected $table = 'role_permissions';
    public $timestamps = false;
    protected $fillable = ['role_id', 'permission_id'];
}