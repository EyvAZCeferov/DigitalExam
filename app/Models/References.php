<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class References extends Model
{
    use HasFactory,SoftDeletes;
    protected $table='references';
    protected $fillable=[
        'name',
        'description',
        'image',
        'order_number'
    ];
    protected $casts=[
        'name'=>'json',
        'description'=>'json',
        'order_number'=>'integer'
    ];
    public function exams(){
        return $this->hasMany(ExamReferences::class,'reference_id','id');
    }
}
