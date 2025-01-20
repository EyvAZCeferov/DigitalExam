<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ExamReferences extends Model
{
    use HasFactory;
    protected $table='exam_references';
    protected $fillable=[
        'exam_id',
        'reference_id',
        'order_number'
    ];
    public function exam():BelongsTo{
        return $this->belongsTo(Exam::class);
    }
    public function reference():BelongsTo{
        return $this->belongsTo(References::class);
    }
}
