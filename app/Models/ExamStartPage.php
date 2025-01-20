<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class ExamStartPage extends Model
{
    use HasFactory,SoftDeletes;
    protected $table='exam_start_pages';
    protected $fillable=[
        'user_id',
        'image',
        'order_number',
        'name',
        'description',
        'type',
        'default'
    ];
    protected $casts=[
        'user_id'=>'integer',
        'order_number'=>'integer',
        'name'=>'json',
        'description'=>'json',
        'default'=>'boolean'
    ];
    public function user(){
        return $this->belongsTo(Admin::class);
    }
    public function exams(){
        return $this->hasMany(ExamStartPageIds::class,'start_page_id','id');
    }

}
