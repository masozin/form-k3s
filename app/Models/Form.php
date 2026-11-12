<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Str;
use Illuminate\Support\Carbon;

class Form extends Model
{
    use HasFactory, SoftDeletes;

    public const STATUS_DRAFT     = 'draft';
    public const STATUS_PUBLISHED = 'published';
    public const STATUS_CLOSED    = 'closed';

    public const TARGET_ALL = 'semua';

    protected $fillable = [
        'school_id',
        'created_by',
        'title',
        'slug',
        'description',
        'banner_image',
        'start_at',
        'end_at',
        'status',
        'allow_multiple_submissions',
        'require_login',
        'target_role',
    ];

    protected function casts(): array
    {
        return [
            'start_at' => 'datetime',
            'end_at' => 'datetime',
            'allow_multiple_submissions' => 'boolean',
            'require_login' => 'boolean',
        ];
    }

    public function needsLogin(): bool
    {
        return $this->require_login;
    }

    public function isTargetedAt(?string $role): bool
    {
        if (!$this->target_role || $this->target_role === self::TARGET_ALL) {
            return true;
        }
        return $this->target_role === $role;
    }

    // submission milik user tertentu, untuk daftar riwayat
    public function submissionsBy(int $userId)
    {
        return $this->submissions()->where('user_id', $userId)->latest();
    }

    protected static function booted(): void
    {
        static::creating(function (Form $form) {
            if (empty($form->slug)) {
                $form->slug = static::generateUniqueSlug($form->title);
            }
        });
    }

    public static function generateUniqueSlug(string $title): string
    {
        $slug = Str::slug($title);
        $original = $slug;
        $i = 1;

        while (static::where('slug', $slug)->exists()) {
            $slug = "{$original}-{$i}";
            $i++;
        }

        return $slug;
    }

    // ---- Relasi ----

    public function school(): BelongsTo
    {
        return $this->belongsTo(School::class);
    }

    public function creator(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function sections(): HasMany
    {
        return $this->hasMany(FormSection::class)->orderBy('sort_order');
    }

    public function fields(): HasMany
    {
        return $this->hasMany(FormField::class)->orderBy('sort_order');
    }

    public function submissions(): HasMany
    {
        return $this->hasMany(FormSubmission::class);
    }

    // ---- Scopes ----

    public function scopePublished($query)
    {
        return $query->where('status', self::STATUS_PUBLISHED);
    }

    public function scopeForSchool($query, int $schoolId)
    {
        return $query->where('school_id', $schoolId);
    }

    // ---- Business logic ----

    public function isOpen(): bool
    {
        if ($this->status !== self::STATUS_PUBLISHED) {
            return false;
        }

        $now = Carbon::now();

        if ($this->start_at && $now->lt($this->start_at)) {
            return false;
        }

        if ($this->end_at && $now->gt($this->end_at)) {
            return false;
        }

        return true;
    }

    /**
     * Alasan kenapa form tidak bisa diisi saat ini, atau null kalau form terbuka.
     * Dipakai untuk menampilkan pesan yang tepat di halaman publik.
     */
    public function closedReason(): ?string
    {
        if ($this->status === self::STATUS_DRAFT) {
            return 'not_published';
        }

        if ($this->status === self::STATUS_CLOSED) {
            return 'closed';
        }

        $now = Carbon::now();

        if ($this->start_at && $now->lt($this->start_at)) {
            return 'not_started';
        }

        if ($this->end_at && $now->gt($this->end_at)) {
            return 'ended';
        }

        return null; // terbuka
    }

    public function hasSubmittedBy(?int $userId): bool
    {
        if (!$userId || $this->allow_multiple_submissions) {
            return false;
        }

        return $this->submissions()->where('user_id', $userId)->exists();
    }

    public function submissionsCount(): int
    {
        return $this->submissions()->count();
    }
}
