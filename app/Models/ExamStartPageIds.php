<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ExamStartPageIds extends Model
{
    use HasFactory;
    protected $table='exam_start_page_ids';
    protected $fillable=[
        'exam_id',
        'start_page_id',
        'order_number'
    ];
    protected $casts=[
        'exam_id'=>"integer",
        'start_page_id'=>"integer",
        'order_number'=>"integer"
    ];
    public function exam():BelongsTo{
        return $this->belongsTo(Exam::class);
    }
    public function start_page():BelongsTo{
        return $this->belongsTo(ExamStartPage::class);
    }
}
