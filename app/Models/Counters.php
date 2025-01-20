<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Counters extends Model
{
    use HasFactory;
    protected $table = 'counters';
    protected $fillable = ['name', 'count', 'order_number', 'status', 'image'];
    protected $casts = ['name' => 'json', 'status' => 'boolean', 'order_number' => 'integer'];
}
