<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use App\Services\ECDSAService;

class User extends Authenticatable
{
    use HasFactory, Notifiable;
    protected $fillable = [
        'username',
        'password',
        'fullname',
        'email',
        'role_id',
        'institution_id',
        'public_key',
        'private_key',
    ];

    protected $hidden = [
        'password',
    ];

    public function role(): BelongsTo
    {
        return $this->belongsTo(Role::class);
    }

    public function institution(): BelongsTo
    {
        return $this->belongsTo(Institution::class);
    }

    public function committeesAsChairman(): HasMany
    {
        return $this->hasMany(Committee::class, 'chairman_id');
    }

    public function committeesAsSecretary(): HasMany
    {
        return $this->hasMany(Committee::class, 'secretary_id');
    }

    public function isChairman(): bool
    {
        return $this->role_id === 2;
    }

    public function isSecretary(): bool
    {
        return $this->role_id === 3;
    }

    public function isMentor(): bool
    {
        return $this->role_id === 6;
    }

    public function isCommitteeChairman(): bool
    {
        return $this->committeesAsChairman()->exists();
    }

    public function isCommitteeSecretary(): bool
    {
        return $this->committeesAsSecretary()->exists();
    }

    public function generateKeyPair()
    {
        $ecdsaService = new ECDSAService();
        $keyPair = $ecdsaService->generateKeyPair();
        $this->update([
            'public_key' => $keyPair['publicKey'],
            'private_key' => $keyPair['privateKey']
        ]);
    }

    public function scopeForInstitution($query)
    {
        $user = auth()->user();
        if (!$user) {
            return $query;
        }

        // If user is from institution (Sekretaris Umum, Ketua Umum, Pembina)
        if (in_array($user->role_id, [2, 3, 6])) {
            return $query->where('institution_id', $user->institution_id);
        }

        // If user is from committee (Sekretaris Panitia, Ketua Panitia)
        if (in_array($user->role_id, [4, 5])) {
            return $query->where(function ($q) use ($user) {
                $q->whereHas('committeesAsChairman', function ($q) use ($user) {
                    $q->where('institution_id', $user->institution_id);
                })->orWhereHas('committeesAsSecretary', function ($q) use ($user) {
                    $q->where('institution_id', $user->institution_id);
                });
            });
        }

        return $query;
    }

    public function scopeChairman($query, $institutionId)
    {
        return $query->where('role_id', 2)
            ->where('institution_id', $institutionId);
    }

    public function scopeSecretary($query, $institutionId)
    {
        return $query->where('role_id', 3)
            ->where('institution_id', $institutionId);
    }

    public function scopeMentor($query, $institutionId)
    {
        return $query->where('role_id', 6)
            ->where('institution_id', $institutionId);
    }

    public function getAuthIdentifierName()
    {
        return 'username';
    }

    public function scopeWithRelations($query)
    {
        return $query->with(['role', 'institution']);
    }

    public function scopeSearch($query, $search)
    {
        if ($search) {
            return $query->where(function ($q) use ($search) {
                $q->where('username', 'like', "%{$search}%")
                    ->orWhere('fullname', 'like', "%{$search}%")
                    ->orWhereHas('role', function ($q) use ($search) {
                        $q->where('name', 'like', "%{$search}%");
                    })
                    ->orWhereHas('institution', function ($q) use ($search) {
                        $q->where('name', 'like', "%{$search}%");
                    });
            });
        }

        return $query;
    }

    public function hasKeyPair(): bool
    {
        return !empty($this->public_key) && !empty($this->private_key);
    }

    protected static function boot()
    {
        parent::boot();

        static::creating(function ($user) {
            if (empty($user->public_key) || empty($user->private_key)) {
                $ecdsaService = app(ECDSAService::class);
                $keyPair = $ecdsaService->generateKeyPair();
                $user->public_key = $keyPair['publicKey'];
                $user->private_key = $keyPair['privateKey'];
            }
        });
    }
}
