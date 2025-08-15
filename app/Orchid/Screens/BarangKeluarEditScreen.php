<?php

namespace App\Orchid\Screens;

use App\Models\Barang;
use App\Models\BarangKeluar;
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

class BarangKeluarEditScreen extends Screen
{
    public $barangKeluar;

    public function query(BarangKeluar $barangKeluar): iterable
    {
        $barangKeluar->tanggal_keluar = now();

        return [
            'barangKeluar' => $barangKeluar,
        ];
    }

    public function name(): ?string
    {
        return 'Catat Barang Keluar Baru';
    }

    public function description(): ?string
    {
        return 'Formulir untuk mencatat pengurangan stok barang (misal: rusak, hilang).';
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
                Relation::make('barangKeluar.barang_id')
                    ->title('Pilih Barang')
                    ->fromModel(Barang::class, 'nama_barang')
                    ->required()
                    ->help('Pilih barang yang stoknya akan dikurangi.'),

                Input::make('barangKeluar.jumlah')
                    ->title('Jumlah Keluar')
                    ->required()
                    ->min(1)
                    ->help('Masukkan jumlah barang yang keluar.'),

                DateTimer::make('barangKeluar.tanggal_keluar')
                    ->title('Tanggal Keluar')
                    ->required()
                    ->allowInput()
                    ->format('Y-m-d'),

                TextArea::make('barangKeluar.keterangan')
                    ->title('Keterangan')
                    ->rows(3)
                    ->required()
                    ->placeholder('Contoh: Barang rusak, digunakan untuk operasional'),
            ]),
        ];
    }

    public function save(Request $request)
    {
        $request->validate([
            'barangKeluar.barang_id' => 'required|exists:barangs,id',
            'barangKeluar.jumlah' => 'required|integer|min:1',
            'barangKeluar.tanggal_keluar' => 'required|date',
            'barangKeluar.keterangan' => 'required|string',
        ]);

        $data = $request->input('barangKeluar');
        $barang = Barang::findOrFail($data['barang_id']);

        if ($barang->stok < $data['jumlah']) {
            Toast::error('Stok barang tidak mencukupi.');
            return back();
        }

        try {
            DB::beginTransaction();

            $barangKeluar = BarangKeluar::create($data);
            $barang->decrement('stok', $barangKeluar->jumlah);

            DB::commit();
            Toast::info(__('Catatan barang keluar berhasil disimpan dan stok telah diperbarui.'));
        } catch (\Exception $e) {
            DB::rollBack();
            Toast::error('Terjadi kesalahan: ' . $e->getMessage());
            return back();
        }

        return redirect()->route('platform.barang_keluar.list');
    }
}
