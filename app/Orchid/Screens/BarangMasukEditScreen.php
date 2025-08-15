<?php

namespace App\Orchid\Screens;

use App\Models\Barang;
use App\Models\BarangMasuk;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Orchid\Screen\Actions\Button;
use Orchid\Screen\Fields\DateTimer;
use Orchid\Screen\Fields\Input;
use Orchid\Screen\Fields\Relation;
use Orchid\Screen\Fields\TextArea;
use Orchid\Screen\Screen;
use Orchid\Support\Facades\Layout;
use Orchid\Support\Facades\Toast;

class BarangMasukEditScreen extends Screen
{
    /**
     * @var BarangMasuk
     */
    public $barangMasuk;

    public function query(BarangMasuk $barangMasuk): iterable
    {
        // Set default date to today
        $barangMasuk->tanggal_masuk = now();

        return [
            'barangMasuk' => $barangMasuk,
        ];
    }

    public function name(): ?string
    {
        return 'Catat Barang Masuk Baru';
    }

    public function description(): ?string
    {
        return 'Formulir untuk mencatat penambahan stok barang baru.';
    }

    public function commandBar(): iterable
    {
        return [
            Button::make('Simpan Catatan')
                ->icon('bs.check-circle')
                ->method('save'),
        ];
    }

    public function layout(): iterable
    {
        return [
            Layout::rows([
                Relation::make('barangMasuk.barang_id')
                    ->title('Pilih Barang')
                    ->fromModel(Barang::class, 'nama_barang')
                    ->required()
                    ->help('Pilih barang yang stoknya akan ditambahkan.'),

                Input::make('barangMasuk.jumlah')
                    ->title('Jumlah Masuk')
                    ->required()
                    ->min(1)
                    ->help('Masukkan jumlah barang yang masuk.'),

                DateTimer::make('barangMasuk.tanggal_masuk')
                    ->title('Tanggal Masuk')
                    ->required()
                    ->allowInput()
                    ->format('Y-m-d'),

                TextArea::make('barangMasuk.keterangan')
                    ->title('Keterangan')
                    ->rows(3)
                    ->placeholder('Contoh: Pembelian dari supplier A'),
            ]),
        ];
    }

    public function save(Request $request)
    {
        $request->validate([
            'barangMasuk.barang_id' => 'required|exists:barangs,id',
            'barangMasuk.jumlah' => 'required|integer|min:1',
            'barangMasuk.tanggal_masuk' => 'required|date',
            'barangMasuk.keterangan' => 'nullable|string',
        ]);

        $data = $request->input('barangMasuk');

        try {
            DB::beginTransaction();

            $barangMasuk = BarangMasuk::create($data);
            $barangMasuk->barang()->increment('stok', $barangMasuk->jumlah);

            DB::commit();
            Toast::info(__('Catatan barang masuk berhasil disimpan dan stok telah diperbarui.'));
        } catch (\Exception $e) {
            DB::rollBack();
            Toast::error('Terjadi kesalahan: ' . $e->getMessage());
            return back();
        }

        return redirect()->route('platform.barang_masuk.list');
    }
}
