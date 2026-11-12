<?php

namespace App\Models;

use Database\Factories\KaryawanFactory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;

class Karyawan extends Model
{
    /** @use HasFactory<KaryawanFactory> */
    use HasFactory;

    protected static function booted(): void
    {
        static::saved(function (Karyawan $karyawan): void {
            if ($karyawan->wasRecentlyCreated || $karyawan->wasChanged('position_id')) {
                $karyawan->user()->first()?->syncRoleFromKaryawan();
            }
        });
    }

    protected $fillable = [
        'nama_karyawan',
        'email',
        'team_id',
        'position_id',
    ];

    public function team(): BelongsTo
    {
        return $this->belongsTo(Team::class);
    }

    public function position(): BelongsTo
    {
        return $this->belongsTo(Position::class);
    }

    public function absensis(): HasMany
    {
        return $this->hasMany(Absensi::class);
    }

    public function user(): HasOne
    {
        return $this->hasOne(User::class);
    }

    public function getDepartemenAttribute(): ?Departemen
    {
        return $this->team?->departemen;
    }
}
