<?php

namespace App\Orchid\Screens;

use App\Models\BarangMasuk;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Orchid\Screen\Actions\Button;
use Orchid\Screen\Actions\DropDown;
use Orchid\Screen\Actions\Link;
use Orchid\Screen\Screen;
use Orchid\Screen\TD;
use Orchid\Support\Facades\Layout;
use Orchid\Support\Facades\Toast;

class BarangMasukListScreen extends Screen
{
    /**
     * Fetch data to be displayed on the screen.
     */
    public function query(): iterable
    {
        return [
            'barang_masuk' => BarangMasuk::with('barang')
                ->defaultSort('tanggal_masuk', 'desc')
                ->paginate(),
        ];
    }

    /**
     * The name of the screen displayed in the header.
     */
    public function name(): ?string
    {
        return 'Riwayat Barang Masuk';
    }

    /**
     * The description is displayed on the user's screen under the heading
     */
    public function description(): ?string
    {
        return 'Daftar semua catatan penambahan stok barang.';
    }

    /**
     * The screen's action buttons.
     */
    public function commandBar(): iterable
    {
        return [
            Link::make('Catat Barang Masuk')
                ->icon('bs.plus-circle')
                ->route('platform.barang_masuk.create'),
        ];
    }

    /**
     * The screen's layout elements.
     */
    public function layout(): iterable
    {
        return [
            Layout::table('barang_masuk', [
                TD::make('barang.nama_barang', 'Nama Barang')
                    ->render(fn (BarangMasuk $model) => $model->barang->nama_barang ?? 'N/A'),

                TD::make('jumlah', 'Jumlah'),

                TD::make('tanggal_masuk', 'Tanggal Masuk')
                    ->render(fn (BarangMasuk $model) => $model->tanggal_masuk->format('d F Y')),

                TD::make('keterangan', 'Keterangan')->width('300px'),

                TD::make(__('Actions'))
                    ->align(TD::ALIGN_CENTER)
                    ->width('100px')
                    ->render(fn (BarangMasuk $barangMasuk) => DropDown::make()
                        ->icon('bs.three-dots-vertical')
                        ->list([
                            Button::make(__('Delete'))
                                ->icon('bs.trash3')
                                ->confirm(__('Menghapus catatan ini akan mengurangi stok barang terkait. Apakah Anda yakin?'))
                                ->method('remove', ['id' => $barangMasuk->id]),
                        ])),
            ]),
        ];
    }

    public function remove(Request $request): void
    {
        $barangMasuk = BarangMasuk::with('barang')->findOrFail($request->get('id'));

        try {
            DB::beginTransaction();

            // Cek apakah stok cukup untuk dikurangi
            if ($barangMasuk->barang->stok < $barangMasuk->jumlah) {
                throw new \Exception('Gagal menghapus. Stok barang saat ini lebih kecil dari jumlah yang akan dikembalikan.');
            }

            // Kurangi stok barang
            $barangMasuk->barang->stok -= $barangMasuk->jumlah;
            $barangMasuk->barang->save();

            // Hapus catatan barang masuk
            $barangMasuk->delete();

            DB::commit();
            Toast::info(__('Catatan barang masuk berhasil dihapus dan stok telah diperbarui.'));
        } catch (\Exception $e) {
            DB::rollBack();
            Toast::error('Terjadi kesalahan: ' . $e->getMessage());
        }
    }
}
