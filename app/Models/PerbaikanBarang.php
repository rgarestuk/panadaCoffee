<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Orchid\Filters\Filterable;
use Orchid\Screen\AsSource;

class PerbaikanBarang extends Model
{
    use HasFactory, AsSource, Filterable;

    protected $fillable = [
        'barang_id',
        'jumlah',
        'tanggal_mulai',
        'tanggal_selesai',
        'status',
        'keterangan',
    ];

    protected $allowedSorts = [
        'jumlah',
        'tanggal_mulai',
        'tanggal_selesai',
        'status',
        'created_at',
    ];

    protected $allowedFilters = [
        'status',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array
     */
    protected $casts = [
        'tanggal_mulai' => 'date',
        'tanggal_selesai' => 'date',
    ];

    /**
     * Get the barang being repaired.
     */
    public function barang(): BelongsTo
    {
        return $this->belongsTo(Barang::class);
    }
}
