<?php

namespace App\Orchid\Screens;

use App\Models\Barang;
use Illuminate\Http\Request;
use Orchid\Screen\Actions\Button;
use Orchid\Screen\Fields\Input;
use Orchid\Screen\Fields\TextArea;
use Orchid\Screen\Screen;
use Orchid\Support\Facades\Layout;
use Orchid\Support\Facades\Toast;

class BarangEditScreen extends Screen
{
    /**
     * @var Barang
     */
    public $barang;

    /**
     * Fetch data to be displayed on the screen.
     *
     * @return array
     */
    public function query(Barang $barang): iterable
    {
        return [
            'barang' => $barang,
        ];
    }

    /**
     * The name of the screen displayed in the header.
     *
     * @return string|null
     */
    public function name(): ?string
    {
        return $this->barang->exists ? 'Edit Barang' : 'Tambah Barang Baru';
    }

    /**
     * The description of the screen displayed in the header.
     *
     * @return string|null
     */
    public function description(): ?string
    {
        return "Formulir untuk mengelola data barang.";
    }

    /**
     * The screen's action buttons.
     *
     * @return \Orchid\Screen\Action[]
     */
    public function commandBar(): iterable
    {
        return [
            Button::make('Simpan')
                ->icon('bs.check-circle')
                ->method('save')
                ->canSee(!$this->barang->exists),

            Button::make('Perbarui')
                ->icon('bs.arrow-repeat')
                ->method('save')
                ->canSee($this->barang->exists),

            Button::make('Hapus')
                ->icon('bs.trash3')
                ->method('remove')
                ->canSee($this->barang->exists)
                ->confirm('Apakah Anda yakin ingin menghapus barang ini?'),
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
                Input::make('barang.nama_barang')
                    ->title('Nama Barang')
                    ->placeholder('Masukkan nama barang')
                    ->help('Nama barang yang akan dikelola.')
                    ->required(),

                Input::make('barang.stok')
                    ->title('Stok')
                    ->placeholder('Masukkan jumlah stok')
                    ->help('Jumlah stok barang yang tersedia saat ini.')
                    ->required(),

                TextArea::make('barang.deskripsi')
                    ->title('Deskripsi')
                    ->rows(2)
                    ->placeholder('Masukkan deskripsi barang'),
            ])
        ];
    }

    public function save(Barang $barang, Request $request) {
        $request->validate([
            'barang.nama_barang' => 'required|string|max:255',
            'barang.stok' => 'required|integer|min:0',
        ]);

        $barang->fill($request->get('barang'))->save();

        Toast::info(__('Barang berhasil disimpan.'));

        return redirect()->route('platform.barang.list');
    }

    public function remove(Barang $barang) {
        $barang->delete();

        Toast::info(__('Barang berhasil dihapus.'));

        return redirect()->route('platform.barang.list');
    }
}
