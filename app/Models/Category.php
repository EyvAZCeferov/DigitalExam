<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Spatie\MediaLibrary\HasMedia;
use Spatie\MediaLibrary\InteractsWithMedia;

class Category extends Model implements HasMedia
{
    use InteractsWithMedia;

    protected $fillable = [
        'name',
        'slugs',
        'description',
        'order_number',
        'parent_id',
        'image',
        'icon'
    ];

    protected $casts = [
        'parent_id'=>'integer',
        'created_at' => 'datetime:d.m.Y H:i',
        'name'=>"json",
        'slugs'=>'json',
        'description'=>'json',
        'order_number'=>'integer',
    ];

    public function exams(): HasMany
    {
        return $this->hasMany(Exam::class, 'category_id', 'id')->orderBy("order_number",'ASC');
    }

    public function parent(): HasOne
    {
        return $this->hasOne(Category::class, 'id', 'parent_id');
    }

    public function sub(): HasMany
    {
        return $this->hasMany(Category::class, 'parent_id', 'id');
    }
}
