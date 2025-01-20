<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Database\Eloquent\SoftDeletes;

class Section extends Model
{
    use HasFactory, SoftDeletes;

    protected $table = 'exam_sections';

    public function questions (): HasMany
    {
        return $this->hasMany(ExamQuestion::class, 'exam_section_id', 'id')->whereHas('answers')->orderBy("order_number",'ASC');
    }
    public function correctAnswers($id): int
    {
        $exam_section_result_answer=ExamResultAnswer::where("result_id",$id)->where('section_id',$this->id);
        return $exam_section_result_answer->where('result', 1)->count();
    }
    public function wrongAnswers($id): int
    {
        $exam_section_result_answer=ExamResultAnswer::where("result_id",$id)->where('section_id',$this->id);
        return $exam_section_result_answer->where('result', 0)->count();
    }

}
