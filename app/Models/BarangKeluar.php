<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Orchid\Filters\Filterable;
use Orchid\Screen\AsSource;

class BarangKeluar extends Model
{
    use HasFactory, Filterable, AsSource;

    protected $fillable = [
        'barang_id',
        'jumlah',
        'tanggal_keluar',
        'keterangan',
    ];

    protected $allowedSorts = [
        'jumlah',
        'tanggal_keluar',
        'created_at',
    ];

    protected $allowedFilters = [
        'jumlah',
        'tanggal_keluar',
    ];

    protected $casts = [
        'tanggal_keluar' => 'date',
    ];

    public function barang(): BelongsTo
    {
        return $this->belongsTo(Barang::class);
    }
}
