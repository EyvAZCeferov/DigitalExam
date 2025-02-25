<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Builder;
use League\CommonMark\Reference\Reference;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Exam extends Model
{
    use HasFactory, SoftDeletes;
    protected $table = 'exams';
    protected $casts = [
        'name' => 'json',
        'content' => 'json',
        'price' => 'double',
        'endirim_price' => 'double',
        'start_time' => 'timestamp',
        'rebuy' => "boolean"
    ];

    const LAYOUTS = [
        'standart' => 'Standart Rənglər (sarı və mavi)',
        'sat' => 'SAT rənglər (qara)',
    ];

    protected $fillable = [
        'name',
        'slug',
        'content',
        'point',
        'image',
        'category_id',
        'status',
        'rebuy',
        'show_calc',
        'order_number',
        'price',
        'endirim_price',
        'user_id',
        'user_type',
        'repeat_sound',
        'show_result_user',
        'start_time',
        'layout_type'
    ];


    protected static function booted()
    {
        static::addGlobalScope('active_status', function (Builder $builder) {
            $builder->where('status', 1);
        });
    }

    public function questionCount()
    {
        $count = 0;
        $qesutions = $this->sections->pluck('questions');
        foreach ($qesutions as $qesution) {
            $count += $qesution->count();
        }
        return $count;
    }

    public function questions()
    {
        return $this->sections->pluck('questions');
    }

    public function getquestions($exam_result)
    {
        $questions = collect();
        $sections = $this->sections;
        foreach ($sections as $section) {
            if ($section->question_count > 0) {
                if ($section->random == true) {
                    $exam_results_using_in_this_section_questions = $exam_result->answers->where("section_id", $section->id)->pluck("question_id");
                    $questions = $questions->concat($section->questions->whereIn("id", $exam_results_using_in_this_section_questions));
                } else {
                    $questions = $questions->concat($section->questions->take($section->question_count));
                }
            } else {
                $questions = $questions->concat($section->questions);
            }
        }
        return $questions;
    }

    public function results(): HasMany
    {
        return $this->hasMany(ExamResult::class, 'exam_id', 'id');
    }

    public function sections(): HasMany
    {
        return $this->hasMany(Section::class, 'exam_id', 'id')->with("questions");
    }

    public function category(): HasOne
    {
        return $this->hasOne(Category::class, 'id', 'category_id');
    }

    public function resultHandler()
    {
        //        $questions = count($this->questions);
        $results = ExamResult::where('exam_id', $this->id)->where('user_id', auth('users')->id())->count();

        //        if ($results < $questions) {
        //            return true;
        //        }
        return $results;
    }
    public function start_pages()
    {
        return $this->hasMany(ExamStartPageIds::class, 'exam_id', 'id')
            ->with([
                'start_page' => function ($query) {
                    $query->orderBy('order_number', 'ASC');
                }
            ])
            ->with('exam');
    }
    public function user()
    {
        return $this->hasOne(User::class, 'id', 'user_id');
    }
    public function references()
    {
        return $this->hasMany(ExamReferences::class, 'exam_id', 'id')->with([
            'reference' => function ($query) {
                $query->orderBy('order_number', 'ASC');
            }
        ])->with(['exam']);
    }
}
