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

    // Only categories that are NOT for a committee (institutional)
    public function scopeNonCommittee($query)
    {
        return $query->whereNull('committee_id');
    }

    // Only categories for a specific committee
    public function scopeCommitteeOnly($query)
    {
        return $query->whereNotNull('committee_id');
    }

}
