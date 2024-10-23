<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
class UserSuspensions extends Model
{
    use SoftDeletes, HasFactory;
    protected $table ='2024_10_19_042615_user_suspensions';
    protected $fillable = ['user_id', 'ban_reason', 'ban_type', 'end_date', 'banned_at'];
    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
