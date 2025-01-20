<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class CouponCodes extends Model
{
    use HasFactory,SoftDeletes;
    protected $table='coupon_codes';
    protected $fillable=[
        'name',
        'code',
        'discount',
        'status',
        'type',
        'user_id',
        'user_type'
    ];
    protected $casts=[
        'name'=>'json',
        'discount'=>'integer',
        'status'=>"boolean",
    ];
    public function user(){
        if($this->user_type=="users"){
            return $this->hasOne(User::class,'id','user_id');
        }else{
            return $this->hasOne(Admin::class,'id','user_id');
        }
    }
}
