<?php

namespace App\Orchid\Screens;

use App\Models\PerbaikanBarang;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Orchid\Screen\Actions\Button;
use Orchid\Screen\Actions\DropDown;
use Orchid\Screen\Actions\Link;
use Orchid\Screen\Screen;
use Orchid\Screen\TD;
use Orchid\Support\Facades\Layout;
use Orchid\Support\Facades\Toast;

class PerbaikanBarangListScreen extends Screen
{
    /**
     * Fetch data to be displayed on the screen.
     */
    public function query(): iterable
    {
        return [
            'perbaikan_barang' => PerbaikanBarang::with('barang')
                ->defaultSort('created_at', 'desc')
                ->paginate(),
        ];
    }

    /**
     * The name of the screen displayed in the header.
     */
    public function name(): ?string
    {
        return 'Daftar Perbaikan Barang';
    }

    public function description(): ?string
    {
        return 'Kelola barang yang sedang dalam perbaikan atau sudah selesai diperbaiki.';
    }

    /**
     * The screen's action buttons.
     */
    public function commandBar(): iterable
    {
        return [
            Link::make('Kirim untuk Perbaikan')
                ->icon('bs.plus-circle')
                ->route('platform.perbaikan_barang.create'),
        ];
    }

    /**
     * The screen's layout elements.
     */
    public function layout(): iterable
    {
        return [
            Layout::table('perbaikan_barang', [
                TD::make('barang.nama_barang', 'Nama Barang')
                    ->render(fn(PerbaikanBarang $model) => $model->barang->nama_barang ?? 'N/A'),

                TD::make('jumlah', 'Jumlah'),

                TD::make('status', 'Status')->render(function (PerbaikanBarang $model) {
                    $colors = [
                        'dalam_perbaikan' => 'info',
                        'selesai' => 'success',
                        'gagal_diperbaiki' => 'danger',
                    ];
                    return "<span class='badge bg-{$colors[$model->status]}'>" . str_replace('_', ' ', ucfirst($model->status)) . "</span>";
                }),

                TD::make('tanggal_mulai', 'Tgl. Mulai')->render(fn(PerbaikanBarang $model) => $model->tanggal_mulai->format('d/m/Y')),
                TD::make('tanggal_selesai', 'Tgl. Selesai')->render(fn(PerbaikanBarang $model) => $model->tanggal_selesai?->format('d/m/Y')),

                TD::make(__('Actions'))
                    ->align(TD::ALIGN_CENTER)
                    ->width('100px')
                    ->render(fn(PerbaikanBarang $perbaikan) => DropDown::make()
                        ->icon('bs.three-dots-vertical')
                        ->list([
                            Link::make(__('Edit'))->route('platform.perbaikan_barang.edit', $perbaikan->id)->icon('bs.pencil'),
                            Button::make(__('Delete'))->icon('bs.trash3')->confirm(__('Data ini akan dihapus. Jika statusnya "Dalam Perbaikan", stok akan dikembalikan.'))->method('remove', ['id' => $perbaikan->id]),
                        ])),
            ]),
        ];
    }

    public function remove(Request $request): void
    {
        $perbaikan = PerbaikanBarang::findOrFail($request->get('id'));

        try {
            DB::beginTransaction();

            // Jika catatan dihapus saat barang masih diperbaiki, kembalikan stoknya.
            if ($perbaikan->status === 'dalam_perbaikan') {
                $perbaikan->barang()->increment('stok', $perbaikan->jumlah);
            }

            $perbaikan->delete();

            DB::commit();
            Toast::info(__('Catatan perbaikan berhasil dihapus.'));
        } catch (\Exception $e) {
            DB::rollBack();
            Toast::error('Terjadi kesalahan: ' . $e->getMessage());
        }
    }
}
