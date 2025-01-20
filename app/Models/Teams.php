<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Teams extends Model
{
    use HasFactory;
    protected $table = 'teams';
    protected $fillable = [
        'image',
        'name',
        'position',
        'slugs',
        'description',
        'social_media',
        'order_number'
    ];
    protected $casts = [
        'name' => 'json',
        'position' => 'json',
        'slugs' => 'json',
        'description' => 'json',
        'social_media' => 'json',
        'order_number' => 'integer'
    ];
}
