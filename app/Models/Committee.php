<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Auth;

class Committee extends Model
{
    /** @use HasFactory<\Database\Factories\CommitteeFactory> */
    use HasFactory;

    protected $fillable = [
        'institution_id',
        'name',
        'chairman_id',
        'secretary_id',
    ];

    public function institution()
    {
        return $this->belongsTo(Institution::class);
    }

    public function chairman()
    {
        return $this->belongsTo(User::class, 'chairman_id');
    }

    public function secretary()
    {
        return $this->belongsTo(User::class, 'secretary_id');
    }

    public function letterCategory()
    {
        return $this->hasOne(LetterCategory::class);
    }

    protected static function booted()
    {
        // Add global scope to filter by institution
        static::addGlobalScope('institution', function ($query) {
            if (Auth::check()) {
                $query->where('institution_id', Auth::user()->institution_id);
            }
        });
    }
}
