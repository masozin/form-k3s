<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Relations\HasMany;

class School extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'npsn',
        'name',
        'address',
        'level',
        'accreditation',
        'principal_name',
        'principal_nip',
    ];

    public function users(): HasMany
    {
        return $this->hasMany(User::class);
    }

    public function forms(): HasMany
    {
        return $this->hasMany(Form::class);
    }

    // Helper: hitung jumlah form aktif milik sekolah ini
    public function activeFormsCount(): int
    {
        return $this->forms()->where('status', 'published')->count();
    }
}
