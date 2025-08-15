<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Orchid\Filters\Filterable;
use Orchid\Screen\AsSource;


class BarangMasuk extends Model
{
    use HasFactory, Filterable, AsSource;

    protected $fillable = [
        'barang_id',
        'jumlah',
        'tanggal_masuk',
        'keterangan',
    ];

    protected $allowedSorts = [
        'jumlah',
        'tanggal_masuk',
        'created_at',
    ];

    protected $allowedFilters = [
        'jumlah',
        'tanggal_masuk',
    ];

    protected $casts = [
        'tanggal_masuk' => 'date',
    ];

    public function barang(): BelongsTo
    {
        return $this->belongsTo(Barang::class);
    }
}
