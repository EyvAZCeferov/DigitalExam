<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Spatie\MediaLibrary\HasMedia;
use Spatie\MediaLibrary\InteractsWithMedia;
use Spatie\MediaLibrary\MediaCollections\File;
use Spatie\MediaLibrary\MediaCollections\Models\Media;

class ExamQuestion extends Model implements HasMedia
{
    use HasFactory, InteractsWithMedia;

    protected $table = 'exam_questions';

    const TYPES = [
        'Tək Seçimli' => 1,
        'Çox Seçimli' => 2,
        'Açıq' => 3,
        'Uzlaşma' => 4,
        'Səs' => 5,
    ];

    const TYS=[
        1=>'radio',
        2=>'checkbox',
        3=>'textbox',
        4=>'match',
        5=>'audio',
    ];

    const LAYOUTS = [
        'onepage' => 'Yuxarıda sual, aşağıda cavab',
        'standart' => 'Sol tərəfdə sual, sağ tərəfdə cavablar',
    ];

    public const ALLOWED_FILE_SIZE_KB = 20 * 1024;

    public const ALLOWED_FILE_MIMES = [
        // taken from https://www.freeformatter.com/mime-types-list.html
        'image/jpeg', 'image/x-citrix-jpeg', // jpeg, jpg
        'image/png', 'image/x-citrix-png', 'image/x-png', // png
        'image/gif', // gif
        'image/svg+xml', // svg
    ];

    public function registerMediaConversions(Media $media = null): void
    {
        $this
            ->addMediaCollection('exam_question')
            ->acceptsMimeTypes(self::ALLOWED_FILE_MIMES)
            ->acceptsFile(fn (File $file) => $file->size <= self::ALLOWED_FILE_SIZE_KB)
            ->registerMediaConversions(function (Media $media) {
                $this->addMediaConversion('thumb')
                    ->nonQueued()
                    ->width(270);
            });
    }

    public function answers(): HasMany
    {
        return $this->hasMany(ExamAnswer::class, 'question_id', 'id');
    }

    public function correctAnswer()
    {
        if ($this->type == 1) {
            return $this->answers?->where('correct', true)?->first();
        } else if ($this->type == 2) {
            return $this->answers?->where('correct', true);
        } else if ($this->type == 3) {
            return $this->answers?->where('correct', true)->first();
        } else if ($this->type == 4) {
            $answers= $this->answers?->where('correct', true)->pluck('answer');
            return $answers;
        } else {
            return $this->answers?->where('correct', true)?->first();
        }
    }

    public function section()
    {
        return $this->hasOne(Section::class, 'id', 'exam_section_id');
    }
}
