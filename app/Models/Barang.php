<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Model;
use Orchid\Filters\Filterable;
use Orchid\Screen\AsSource;

class Barang extends Model
{
    use HasFactory, Filterable, AsSource;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'nama_barang',
        'stok',
        'deskripsi',
    ];

    /**
     * The attributes for which can use sort in url.
     *
     * @var array
     */
    protected $allowedSorts = [
        'nama_barang',
        'stok',
        'created_at',
        'updated_at',
    ];

    /**
     * The attributes for which you can use filters in url.
     *
     * @var array
     */
    protected $allowedFilters = [
        'nama_barang' => \Orchid\Filters\Types\Like::class,
        'stok'        => \Orchid\Filters\Types\Where::class,
    ];

    /**
     * Get the requests for the item.
     */
    public function permintaanBarangs(): HasMany
    {
        return $this->hasMany(PermintaanBarang::class);
    }

    /**
     * Get the incoming stock records for the item.
     */
    public function barangMasuks(): HasMany
    {
        return $this->hasMany(BarangMasuk::class);
    }

    public function barangKeluars(): HasMany
    {
        return $this->hasMany(BarangKeluar::class);
    }

    public function perbaikanBarangs(): HasMany
    {
        return $this->hasMany(PerbaikanBarang::class);
    }
}
