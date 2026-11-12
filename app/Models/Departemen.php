<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Departemen extends Model
{
    use HasFactory;

    protected $fillable = [
        'nama_departemen',
    ];

    /**
     * Departemen memiliki banyak Team.
     */
    public function teams(): HasMany
    {
        return $this->hasMany(Team::class);
    }
}
