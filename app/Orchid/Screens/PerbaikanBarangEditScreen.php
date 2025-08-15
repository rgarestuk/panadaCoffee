<?php

namespace App\Orchid\Screens;

use App\Models\Barang;
use App\Models\PerbaikanBarang;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Orchid\Screen\Actions\Button;
use Orchid\Screen\Fields\DateTimer;
use Orchid\Screen\Fields\Input;
use Orchid\Screen\Fields\Relation;
use Orchid\Screen\Fields\Select;
use Orchid\Screen\Fields\TextArea;
use Orchid\Screen\Screen;
use Orchid\Support\Facades\Layout;
use Orchid\Support\Facades\Toast;

class PerbaikanBarangEditScreen extends Screen
{
    /** @var PerbaikanBarang */
    public $perbaikan;

    /**
     * Fetch data to be displayed on the screen.
     */
    public function query(PerbaikanBarang $perbaikan): iterable
    {
        if (!$perbaikan->exists) {
            $perbaikan->tanggal_mulai = now();
        }

        return [
            'perbaikan' => $perbaikan,
        ];
    }

    /**
     * The name of the screen displayed in the header.
     */
    public function name(): ?string
    {
        return $this->perbaikan->exists ? 'Edit Catatan Perbaikan' : 'Kirim Barang untuk Diperbaiki';
    }

    /**
     * The screen's action buttons.
     */
    public function commandBar(): iterable
    {
        return [
            Button::make('Simpan')
                ->icon('bs.check-circle')
                ->method('save'),
        ];
    }

    /**
     * The screen's layout elements.
     */
    public function layout(): iterable
    {
        return [
            Layout::rows([
                Relation::make('perbaikan.barang_id')
                    ->title('Pilih Barang')
                    ->fromModel(Barang::class, 'nama_barang')
                    ->required()
                    ->disabled($this->perbaikan->exists)
                    ->help('Pilih barang yang akan diperbaiki.'),

                Input::make('perbaikan.jumlah')
                    ->title('Jumlah')
                    ->required()
                    ->min(1)
                    ->disabled($this->perbaikan->exists)
                    ->help('Jumlah barang yang dikirim untuk perbaikan.'),

                DateTimer::make('perbaikan.tanggal_mulai')
                    ->title('Tanggal Mulai Perbaikan')
                    ->required()
                    ->allowInput()
                    ->format('Y-m-d'),

                DateTimer::make('perbaikan.tanggal_selesai')
                    ->title('Tanggal Selesai Perbaikan')
                    ->required()
                    ->allowInput()
                    ->format('Y-m-d'),

                TextArea::make('perbaikan.keterangan')
                    ->title('Keterangan')
                    ->rows(3)
                    ->placeholder('Contoh: Kerusakan pada komponen X'),

                // Fields below are only for editing existing records
                Select::make('perbaikan.status')
                    ->title('Status Perbaikan')
                    ->options([
                        'dalam_perbaikan' => 'Dalam Perbaikan',
                        'selesai' => 'Selesai',
                        'gagal_diperbaiki' => 'Gagal Diperbaiki',
                    ])
                    ->canSee($this->perbaikan->exists)
                    ->help('Perbarui status perbaikan. Jika "Selesai", stok akan dikembalikan.'),

                DateTimer::make('perbaikan.tanggal_selesai')
                    ->title('Tanggal Selesai Perbaikan')
                    ->allowInput()
                    ->format('Y-m-d')
                    ->canSee($this->perbaikan->exists)
                    ->help('Isi tanggal jika perbaikan sudah selesai atau gagal.'),
            ]),
        ];
    }

    public function save(PerbaikanBarang $perbaikan, Request $request)
    {
        $data = $request->validate([
            'perbaikan.barang_id' => 'required|exists:barangs,id',
            'perbaikan.jumlah' => 'required|integer|min:1',
            'perbaikan.tanggal_mulai' => 'required|date',
            'perbaikan.keterangan' => 'nullable|string',
            'perbaikan.status' => 'sometimes|in:dalam_perbaikan,selesai,gagal_diperbaiki',
            'perbaikan.tanggal_selesai' => 'nullable|date',
        ])['perbaikan'];

        try {
            DB::beginTransaction();

            if (!$perbaikan->exists) {
                // --- CREATE LOGIC ---
                $barang = Barang::findOrFail($data['barang_id']);
                if ($barang->stok < $data['jumlah']) {
                    throw new \Exception('Stok barang tidak mencukupi untuk dikirim perbaikan.');
                }
                $barang->decrement('stok', $data['jumlah']);
                $perbaikan->fill($data)->save();
                Toast::info('Barang berhasil dikirim untuk perbaikan dan stok telah dikurangi.');
            } else {
                // --- UPDATE LOGIC ---
                $originalStatus = $perbaikan->status;
                $perbaikan->fill($data);

                // Jika status diubah dari 'dalam_perbaikan' menjadi 'selesai', kembalikan stok.
                if ($originalStatus === 'dalam_perbaikan' && $perbaikan->status === 'selesai') {
                    $perbaikan->barang()->increment('stok', $perbaikan->jumlah);
                }
                $perbaikan->save();
                Toast::info('Catatan perbaikan berhasil diperbarui.');
            }

            DB::commit();
        } catch (\Exception $e) {
            DB::rollBack();
            Toast::error('Terjadi kesalahan: ' . $e->getMessage());
            return back();
        }

        return redirect()->route('platform.perbaikan_barang.list');
    }
}
