<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PermissionChangeLog extends Model
{
    protected $table = 'permission_change_logs';
    public $timestamps = true;
    protected $fillable = ['user_id', 'actor_id', 'action', 'details'];
}