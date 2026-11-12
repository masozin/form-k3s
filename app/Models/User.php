<?php

namespace App\Models;

use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Notifications\Notifiable;

class User extends Authenticatable
{
    use HasFactory, Notifiable;

    // Konstanta role, hindari magic string tersebar di codebase
    public const ROLE_ADMIN    = 'admin';
    public const ROLE_OPERATOR = 'operator';
    public const ROLE_GURU     = 'guru';
    public const ROLE_USER     = 'user';

    protected $fillable = [
        'name',
        'email',
        'password',
        'school_id',
        'role',
    ];

    protected $hidden = [
        'password',
        'remember_token',
    ];

    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
        ];
    }

    public function school(): BelongsTo
    {
        return $this->belongsTo(School::class);
    }

    public function createdForms(): HasMany
    {
        return $this->hasMany(Form::class, 'created_by');
    }

    public function submissions(): HasMany
    {
        return $this->hasMany(FormSubmission::class);
    }

    // ---- Role helpers ----

    public function isAdmin(): bool
    {
        return $this->role === self::ROLE_ADMIN;
    }

    public function isOperator(): bool
    {
        return $this->role === self::ROLE_OPERATOR;
    }

    public function isGuru(): bool
    {
        return $this->role === self::ROLE_GURU;
    }

    // Admin & operator dianggap "pengelola" yang boleh akses builder
    public function canManageForms(): bool
    {
        return in_array($this->role, [self::ROLE_ADMIN, self::ROLE_OPERATOR]);
    }

    // Admin bisa lihat semua sekolah, operator/guru hanya sekolah sendiri
    public function scopeVisibleForms(User $user)
    {
        return $user->isAdmin()
            ? Form::query()
            : Form::where('school_id', $user->school_id);
    }
}
