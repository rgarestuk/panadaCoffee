<?php

namespace App\Orchid\Screens;

use App\Models\Barang;
use App\Models\PermintaanBarang;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Orchid\Screen\Actions\Button;
use Orchid\Screen\Fields\Input;
use Orchid\Screen\Fields\Relation;
use Orchid\Screen\Fields\Select;
use Orchid\Screen\Fields\TextArea;
use Orchid\Screen\Screen;
use Orchid\Support\Facades\Layout;
use Orchid\Support\Facades\Toast;


class PermintaanBarangEditScreen extends Screen
{
    /**
     * @var PermintaanBarang
     */
    public $permintaan;

    /**
     * Fetch data to be displayed on the screen.
     *
     * @return array
     */
    public function query(PermintaanBarang $permintaan): iterable
    {
        $permintaan->load('barang');

        return [
            'permintaan' => $permintaan,
        ];
    }

    /**
     * The name of the screen displayed in the header.
     *
     * @return string|null
     */
    public function name(): ?string
    {
        return $this->permintaan->exists ? 'Edit Permintaan Barang' : 'Buat Permintaan Baru';
    }

    public function description(): ?string
    {
        return 'Formulir untuk membuat atau mengubah data permintaan barang.';
    }

    /**
     * The screen's action buttons.
     *
     * @return \Orchid\Screen\Action[]
     */
    public function commandBar(): iterable
    {
        return [
            Button::make('Buat Permintaan')
                ->icon('bs.check-circle')
                ->method('save')
                ->canSee(!$this->permintaan->exists),

            Button::make('Perbarui Permintaan')
                ->icon('bs.arrow-repeat')
                ->method('save')
                ->canSee($this->permintaan->exists),

            Button::make('Hapus')
                ->icon('bs.trash3')
                ->method('remove')
                ->canSee($this->permintaan->exists)
                ->confirm('Apakah Anda yakin ingin menghapus permintaan barang ini?'),
        ];
    }

    /**
     * The screen's layout elements.
     *
     * @return \Orchid\Screen\Layout[]|string[]
     */
    public function layout(): iterable
    {
        return [
            Layout::rows([
                Relation::make('permintaan.barang_id')
                    ->title('Pilih Barang')
                    ->fromModel(Barang::class, 'nama_barang')
                    ->required()
                    ->help('Pilih barang yang ingin Anda minta.'),

                Input::make('permintaan.jumlah')
                    ->title('Jumlah yang Diminta')
                    ->required()
                    ->min(1)
                    ->help('Masukkan jumlah barang yang dibutuhkan.'),

                TextArea::make('permintaan.keterangan')
                    ->title('Keterangan')
                    ->rows(3)
                    ->placeholder('Contoh: Untuk keperluan departemen IT.'),

                Select::make('permintaan.status')
                    ->title('Status Permintaan')
                    ->options([
                        'pending' => 'Pending',
                        'approved' => 'Approved',
                        'rejected' => 'Rejected',
                    ])
                    ->help('Ubah status permintaan (hanya untuk admin).')
                    ->canSee($this->permintaan->exists),
            ]),
        ];
    }

    public function save(PermintaanBarang $permintaan, Request $request)
    {
        $data = $request->validate([
            'permintaan.barang_id' => 'required|exists:barangs,id',
            'permintaan.jumlah' => 'required|integer|min:1',
            'permintaan.keterangan' => 'nullable|string',
            'permintaan.status' => 'sometimes|in:pending,approved,rejected',
        ]);

        $barang = Barang::findOrFail($data['permintaan']['barang_id']);
        $jumlahDiminta = $data['permintaan']['jumlah'];

        if (!$permintaan->exists || ($permintaan->jumlah !== $jumlahDiminta && ($data['permintaan']['status'] ?? 'pending') === 'approved')) {
            if ($barang->stok < $jumlahDiminta) {
                Toast::error('Stok barang tidak mencukupi untuk jumlah yang diminta.');
                return back();
            }
        }

        try {
            DB::beginTransaction();

            if (!$permintaan->exists) {
                $data['permintaan']['status'] = 'pending';
            }

            $permintaan->fill($data['permintaan']);

            if ($permintaan->isDirty('status') && $permintaan->status === 'approved') {
                if ($barang->stok < $permintaan->jumlah) {
                    throw new \Exception('Stok barang tidak mencukupi.');
                }
                $barang->stok -= $permintaan->jumlah;
                $barang->save();
            }

            $permintaan->save();

            DB::commit();
            Toast::info(__('Permintaan barang berhasil disimpan.'));
        } catch (\Exception $e) {
            DB::rollBack();
            Toast::error('Terjadi kesalahan: ' . $e->getMessage());
            return back();
        }

        return redirect()->route('platform.permintaan.list');
    }

    public function remove(PermintaanBarang $permintaan) {
        $permintaan->delete();

        Toast::info(__('Permintaan barang berhasil dihapus.'));

        return redirect()->route('platform.permintaan.list');
    }
}
