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
        $user = auth()->user();

        // If user is Sekretaris Panitia or Ketua Panitia, restrict to their committee
        if (in_array($user->role_id, [4, 5])) { // 4 = Ketua Panitia, 5 = Sekretaris Panitia
            $committee = \App\Models\Committee::where(function($q) use ($user) {
                $q->where('secretary_id', $user->id)
                  ->orWhere('chairman_id', $user->id);
            })->first();

            if ($committee) {
                $query->where('committee_id', $committee->id);
            } else {
                // No committee found, return no results
                $query->whereNull('id');
            }
        }
        // For other roles, do not restrict by committee_id (see all in institution)
        // The global scope already restricts to institution_id
    }
}
