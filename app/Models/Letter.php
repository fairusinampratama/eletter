<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Letter extends Model
{
    /** @use HasFactory<\Database\Factories\LetterFactory> */
    use HasFactory;

    protected $fillable = [
        'verification_id',
        'code',
        'category_id',
        'creator_id',
        'file_path',
        'file_hash',
        'date',
        'status',
    ];

    protected static function booted()
    {
        static::addGlobalScope('institution', function ($query) {
            $user = auth()->user();
            if (!$user)
                return;

            // If user is from institution (Sekretaris Umum, Ketua Umum, Pembina)
            if (in_array($user->role_id, [2, 3, 6])) {
                $query->whereHas('category', function ($q) use ($user) {
                    $q->where('institution_id', $user->institution_id)
                        ->whereNull('committee_id');
                });
            }
            // If user is from committee (Sekretaris Panitia, Ketua Panitia)
            else if (in_array($user->role_id, [4, 5])) {
                $query->whereHas('category', function ($q) use ($user) {
                    $q->whereHas('committee', function ($q) use ($user) {
                        if ($user->role_id === 4) { // Ketua Panitia
                            $q->where('chairman_id', $user->id);
                        } else { // Sekretaris Panitia
                            $q->where('secretary_id', $user->id);
                        }
                    });
                });
            }
        });

        // Create signatures when letter is created
        static::created(function ($letter) {
            $creator = $letter->creator;
            $institutionId = $letter->category->institution_id;

            if ($creator->isGeneralSecretary()) {
                // Order: Sekretaris Umum -> Ketua Umum -> Pembina
                $this->createSignaturesForInstitutionLetter($letter, $institutionId);
            } else if ($creator->isCommitteeSecretary()) {
                // Order: Sekretaris Panitia -> Ketua Panitia -> Ketua Umum -> Pembina
                $this->createSignaturesForCommitteeLetter($letter, $creator);
            }
        });
    }

    protected static function createSignaturesForInstitutionLetter($letter, $institutionId)
    {
        // 1. Sekretaris Umum (creator)
        $letter->signatures()->create([
            'signer_id' => $letter->creator_id,
            'order' => 1
        ]);

        // 2. Ketua Umum
        $ketuaUmum = User::ketuaUmum($institutionId)->first();
        if ($ketuaUmum) {
            $letter->signatures()->create([
                'signer_id' => $ketuaUmum->id,
                'order' => 2
            ]);
        }

        // 3. Pembina (Optional)
        $pembina = User::pembina($institutionId)->first();
        if ($pembina) {
            $letter->signatures()->create([
                'signer_id' => $pembina->id,
                'order' => 3
            ]);
        }
    }

    protected static function createSignaturesForCommitteeLetter($letter, $creator)
    {
        $committee = $creator->committeesAsSecretary()->first();
        if (!$committee)
            return;

        // 1. Sekretaris Panitia (creator)
        $letter->signatures()->create([
            'signer_id' => $letter->creator_id,
            'order' => 1
        ]);

        // 2. Ketua Panitia
        if ($committee->chairman_id) {
            $letter->signatures()->create([
                'signer_id' => $committee->chairman_id,
                'order' => 2
            ]);
        }

        // 3. Ketua Umum
        $ketuaUmum = User::ketuaUmum($committee->institution_id)->first();
        if ($ketuaUmum) {
            $letter->signatures()->create([
                'signer_id' => $ketuaUmum->id,
                'order' => 3
            ]);
        }

        // 4. Pembina (Optional)
        $pembina = User::pembina($committee->institution_id)->first();
        if ($pembina) {
            $letter->signatures()->create([
                'signer_id' => $pembina->id,
                'order' => 4
            ]);
        }
    }

    public function category()
    {
        return $this->belongsTo(LetterCategory::class, 'category_id');
    }

    public function creator()
    {
        return $this->belongsTo(User::class, 'creator_id');
    }

    public function institution()
    {
        return $this->belongsTo(Institution::class);
    }

    public function signatures()
    {
        return $this->hasMany(Signature::class);
    }

    public function scopeSearch($query, $search)
    {
        if ($search) {
            return $query->where(function ($q) use ($search) {
                $q->where('code', 'like', "%{$search}%")
                    ->orWhereHas('category', function ($q) use ($search) {
                        $q->where('name', 'like', "%{$search}%");
                    })
                    ->orWhereHas('creator', function ($q) use ($search) {
                        $q->where('fullname', 'like', "%{$search}%");
                    });
            });
        }

        return $query;
    }

    public function isFullySigned()
    {
        $signatures = $this->signatures()
            ->whereNotNull('signed_at')
            ->orderBy('order')
            ->get();

        // Check if all required signatures are present in correct order
        $requiredOrder = range(1, $signatures->max('order'));
        $signedOrder = $signatures->pluck('order')->toArray();

        return empty(array_diff($requiredOrder, $signedOrder));
    }

    public function hasUserSigned($userId)
    {
        return $this->signatures()
            ->where('signer_id', $userId)
            ->whereNotNull('signed_at')
            ->exists();
    }

    public function getSigningStatus()
    {
        $signatures = $this->signatures()
            ->with('signer')
            ->orderBy('order')
            ->get();

        return [
            'signed' => $signatures->whereNotNull('signed_at'),
            'pending' => $signatures->whereNull('signed_at'),
            'order' => $signatures->pluck('order', 'signer_id'),
        ];
    }

    public function canUserSign($userId)
    {
        $user = User::find($userId);
        if (!$user)
            return false;

        // Get all signatures ordered by their sequence
        $signatures = $this->signatures()->orderBy('order')->get();

        // Find the user's signature
        $userSignature = $signatures->firstWhere('signer_id', $userId);
        if (!$userSignature)
            return false;

        // If user has already signed, they can't sign again
        if ($userSignature->signed_at)
            return false;

        // Check if all previous signatures are completed
        $previousSignatures = $signatures->where('order', '<', $userSignature->order);
        return $previousSignatures->every(fn($sig) => $sig->signed_at !== null);
    }
}
