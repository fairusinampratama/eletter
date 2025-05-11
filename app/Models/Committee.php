<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

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
        static::created(function ($committee) {
            // Create a letter category for this committee
            LetterCategory::create([
                'name' => 'Surat ' . $committee->name,
                'institution_id' => $committee->institution_id,
                'committee_id' => $committee->id
            ]);
        });
    }
}
