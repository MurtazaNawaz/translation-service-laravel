<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Tag extends Model
{
    use HasFactory;

    protected $fillable = ['name']; // dev tag name

    // dev tag belongs to many translations
    public function translations()
    {
        return $this->belongsToMany(Translation::class)->withTimestamps();
    }
}
