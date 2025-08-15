<?php

namespace App\Orchid\Screens;

use App\Models\BarangKeluar;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Orchid\Screen\Actions\Button;
use Orchid\Screen\Actions\DropDown;
use Orchid\Screen\Actions\Link;
use Orchid\Screen\Screen;
use Orchid\Screen\TD;
use Orchid\Support\Facades\Layout;
use Orchid\Support\Facades\Toast;

class BarangKeluarListScreen extends Screen
{
    public function query(): iterable
    {
        return [
            'barang_keluar' => BarangKeluar::with('barang')
                ->defaultSort('tanggal_keluar', 'desc')
                ->paginate(),
        ];
    }

    public function name(): ?string
    {
        return 'Riwayat Barang Keluar';
    }

    public function description(): ?string
    {
        return 'Daftar semua catatan pengurangan stok barang (misal: rusak, hilang).';
    }

    public function commandBar(): iterable
    {
        return [
            Link::make('Catat Barang Keluar')
                ->icon('bs.plus-circle')
                ->route('platform.barang_keluar.create'),
        ];
    }

    public function layout(): iterable
    {
        return [
            Layout::table('barang_keluar', [
                TD::make('barang.nama_barang', 'Nama Barang')
                    ->render(fn (BarangKeluar $model) => $model->barang->nama_barang ?? 'N/A'),

                TD::make('jumlah', 'Jumlah'),

                TD::make('tanggal_keluar', 'Tanggal Keluar')
                    ->render(fn (BarangKeluar $model) => $model->tanggal_keluar->format('d F Y')),

                TD::make('keterangan', 'Keterangan')->width('300px'),

                TD::make(__('Actions'))
                    ->align(TD::ALIGN_CENTER)
                    ->width('100px')
                    ->render(fn (BarangKeluar $barangKeluar) => DropDown::make()
                        ->icon('bs.three-dots-vertical')
                        ->list([
                            Button::make(__('Delete'))
                                ->icon('bs.trash3')
                                ->confirm(__('Menghapus catatan ini akan mengembalikan stok barang terkait. Apakah Anda yakin?'))
                                ->method('remove', ['id' => $barangKeluar->id]),
                        ])),
            ]),
        ];
    }

    public function remove(Request $request): void
    {
        $barangKeluar = BarangKeluar::with('barang')->findOrFail($request->get('id'));

        try {
            DB::beginTransaction();

            $barangKeluar->barang()->increment('stok', $barangKeluar->jumlah);
            $barangKeluar->delete();

            DB::commit();
            Toast::info(__('Catatan barang keluar berhasil dihapus dan stok telah dikembalikan.'));
        } catch (\Exception $e) {
            DB::rollBack();
            Toast::error('Terjadi kesalahan: ' . $e->getMessage());
        }
    }
}
