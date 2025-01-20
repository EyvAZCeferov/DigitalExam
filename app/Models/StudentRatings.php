<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class StudentRatings extends Model
{
    use HasFactory;
    protected $table = 'student_ratings';
    protected $fillable = ['name', 'position', 'description', 'rating', 'order_number', 'status', 'image'];
    protected $casts = ['name' => 'json', 'position' => 'json', 'description' => 'json', 'status' => 'boolean', 'order_number' => 'integer', 'rating' => 'integer'];
}
