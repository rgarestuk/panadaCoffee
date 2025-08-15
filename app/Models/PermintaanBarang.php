<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Orchid\Filters\Filterable;
use Orchid\Platform\Models\User;
use Orchid\Screen\AsSource;

class PermintaanBarang extends Model
{
    use HasFactory, Filterable, AsSource;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'barang_id',
        'jumlah',
        'status',
        'keterangan',
    ];

    /**
     * The attributes for which can use sort in url.
     *
     * @var array
     */
    protected $allowedSorts = [
        'jumlah',
        'status',
        'created_at',
        'updated_at',
    ];

    /**
     * The attributes for which you can use filters in url.
     *
     * @var array
     */
    protected $allowedFilters = [
        'status' => \Orchid\Filters\Types\Where::class,
    ];

    /**
     * Get the barang that owns the permintaan.
     */
    public function barang(): BelongsTo
    {
        return $this->belongsTo(Barang::class);
    }
}
