<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Translation extends Model
{
    use HasFactory;

    protected $fillable = ['key', 'locale_id', 'value', 'meta']; // dev fillable

    protected $casts = [
        'meta' => 'array', // dev cast meta to array
    ];

    // dev translation belongs to one locale
    public function locale()
    {
        return $this->belongsTo(Locale::class);
    }

    // dev translation belongs to many tags
    public function tags()
    {
        return $this->belongsToMany(Tag::class)->withTimestamps();
    }
}
