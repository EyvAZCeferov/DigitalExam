<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class AuthAttempts extends Model
{
    use HasFactory, SoftDeletes;
    protected $table = 'auth_attempts';
    protected $fillable = ['code', 'user_id', 'phone_number', 'ipaddress', 'useragent'];
    protected $casts = ['user_id' => "integer", 'useragent' => "json"];
    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
