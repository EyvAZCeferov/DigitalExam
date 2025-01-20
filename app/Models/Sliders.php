<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Sliders extends Model
{
    use HasFactory;
    protected $table = 'sliders';
    protected $fillable = ['name', 'description', 'order_number', 'status', 'url', 'image'];
    protected $casts = ['name' => 'json', 'description' => 'json', 'status' => 'boolean', 'order_number' => 'integer'];
}
