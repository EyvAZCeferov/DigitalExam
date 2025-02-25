<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Database\Eloquent\SoftDeletes;

class ExamResultAnswer extends Model
{
    use HasFactory, SoftDeletes;

    protected $table = 'exam_result_answers';
    protected $fillable = [
        'result_id',
        'section_id',
        'question_id',
        'answer_id',
        'answers',
        'value',
        'result',
    ];

    protected $casts = [
        'answers' => 'json',
        'result_id' => "integer",
        'section_id' => "integer",
        'question_id' => "integer",
        'answer_id' => "integer",
        'result' => "integer",
    ];
    // protected $with=[
    //     'question',
    //     'answer'
    // ];

    public function question(): HasOne
    {
        return $this->hasOne(ExamQuestion::class, 'id', 'question_id');
    }

    public function section(): HasOne
    {
        return $this->hasOne(Section::class, 'id', 'section_id');
    }

    public function answer(): HasOne
    {
        return $this->hasOne(ExamResultAnswer::class, 'id', 'answer_id');
    }

    public function result_model(): HasOne
    {
        return $this->hasOne(ExamResult::class, 'id', 'result_id')->with(['exam', 'user']);
    }
}
