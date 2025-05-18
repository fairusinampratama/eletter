<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Signature extends Model
{
    /** @use HasFactory<\Database\Factories\SignatureFactory> */
    use HasFactory;

    protected $fillable = [
        'letter_id',
        'signer_id',
        'order',
        'signature',
        'signed_at',
        'qr_metadata',
    ];

    protected $casts = [
        'qr_metadata' => 'array',
    ];

    public function letter()
    {
        return $this->belongsTo(Letter::class);
    }

    public function signer()
    {
        return $this->belongsTo(User::class, 'signer_id');
    }
}
