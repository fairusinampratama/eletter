<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Institution extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'code',
        'address',
        'phone',
        'email',
        'website',
        'logo',
        'status'
    ];

    public function users(): HasMany
    {
        return $this->hasMany(User::class);
    }

    public function committees(): HasMany
    {
        return $this->hasMany(Committee::class);
    }

    public function letterCategories(): HasMany
    {
        return $this->hasMany(LetterCategory::class);
    }

    public function ketuaUmum()
    {
        return $this->users()->ketuaUmum($this->id)->first();
    }

    public function sekretarisUmum()
    {
        return $this->users()->sekretarisUmum($this->id)->first();
    }

    public function pembina()
    {
        return $this->users()->pembina($this->id)->first();
    }

    public function hasKetuaUmum(): bool
    {
        return $this->users()->ketuaUmum($this->id)->exists();
    }

    public function hasSekretarisUmum(): bool
    {
        return $this->users()->sekretarisUmum($this->id)->exists();
    }

    public function hasPembina(): bool
    {
        return $this->users()->pembina($this->id)->exists();
    }

    public function scopeSearch($query, $search)
    {
        if ($search) {
            return $query->where('name', 'like', "%{$search}%");
        }

        return $query;
    }
}
