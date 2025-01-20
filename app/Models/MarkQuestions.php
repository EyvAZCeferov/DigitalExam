<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class MarkQuestions extends Model
{
    use HasFactory,SoftDeletes;
    protected $table='exam_mark_questions';
    protected $fillable=[
        'exam_id',
        'exam_result_id',
        'question_id',
        'user_id'
    ];
    protected $casts=[
        'exam_id'=>'integer',
        'exam_result_id'=>'integer',
        'question_id'=>'integer',
        'user_id'=>'integer'
    ];
    public function exam():BelongsTo{
        return $this->belongsTo(Exam::class);
    }
    public function result():HasOne{
        return $this->hasOne(ExamResult::class,'id','exam_result_id');
    }
    public function question():BelongsTo{
        return $this->belongsTo(ExamQuestion::class);
    }
    public function user():HasOne{
        return $this->hasOne(User::class,'id','user_id');
    }
}
