<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class LetterCategory extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'institution_id',
        'committee_id',
    ];

    public function institution()
    {
        return $this->belongsTo(Institution::class);
    }

    public function committee()
    {
        return $this->belongsTo(Committee::class);
    }

    public function letters()
    {
        return $this->hasMany(Letter::class, 'category_id');
    }

    protected static function booted()
    {
        static::addGlobalScope('institution', function ($query) {
            if (auth()->check()) {
                $query->where('institution_id', auth()->user()->institution_id);
            }
        });
    }
}
