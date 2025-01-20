<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class StandartPages extends Model
{
    use HasFactory;
    protected $table='standart_pages';
    protected $fillable=['name','slugs','description','type','images'];
    protected $casts=['name'=>'json','slugs'=>'json','description'=>'json','images'=>'json'];
    public STATIC $TYPES=['about','privarcypolicy','termsandcondition'];
}
