<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasOne;

class Payments extends Model
{
    use HasFactory;
    protected $table='payments';
    protected $fillable=[
        'token',
        'amount',
        'transaction_id',
        'payment_status',
        'data',
        'frompayment',
        'from_capital',
        'user_id',
        'exam_id',
        'coupon_id',
        'exam_result_id',
        'exam_data',
        'user_data',
        'coupon_data',
    ];
    protected $casts=[
        'amount'=>"double:10,2",
        'payment_status'=>'boolean',
        'data'=>"json",
        'frompayment'=>'json',
        'from_capital'=>'json',
        'user_id'=>"integer",
        'exam_id'=>"integer",
        'coupon_id'=>"integer",
        'exam_result_id'=>"integer",
        'exam_data'=>"json",
        'user_data'=>"json",
        'coupon_data'=>'json',
    ];
    public function exam():HasOne{
        return $this->hasOne(Exam::class,'id','exam_id');
    }
    public function user():HasOne{
        return $this->hasOne(User::class,'id','user_id');
    }
    public function coupon():HasOne{
        return $this->hasOne(CouponCodes::class,'id','coupon_id');
    }
    public function exam_result():HasOne{
        return $this->hasOne(ExamResult::class,'id','exam_result_id');
    }
}
