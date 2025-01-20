<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Blogs extends Model
{
    use HasFactory;
    protected $table = 'blogs';
    protected $fillable = [
        'images',
        'name',
        'slugs',
        'description',
        'status'
    ];
    protected $casts = [
        'images' => 'json',
        'name' => 'json',
        'slugs' => 'json',
        'description' => 'json',
        'status' => 'boolean',
    ];
}
