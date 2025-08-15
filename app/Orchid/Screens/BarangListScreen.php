<?php

namespace App\Orchid\Screens;

use App\Models\Barang;
use Illuminate\Http\Request;
use Orchid\Screen\Actions\Link;
use Orchid\Screen\Actions\Button;
use Orchid\Screen\Actions\Dropdown;
use Orchid\Screen\Screen;
use Orchid\Screen\TD;
use Orchid\Support\Facades\Layout;
use Orchid\Support\Facades\Toast;

class BarangListScreen extends Screen
{
    /**
     * Fetch data to be displayed on the screen.
     *
     * @return array
     */
    public function query(): iterable
    {
        return [
            'barangs' => Barang::filters()->defaultSort('created_at', 'desc')->paginate(),
        ];
    }

    /**
     * The name of the screen displayed in the header.
     *
     * @return string|null
     */
    public function name(): ?string
    {
        return 'Manajemen Barang';
    }


    /**
     * The description of the screen displayed in the header.
     *
     * @return string|null
     */
    public function description(): ?string
    {
        return 'Daftar barang yang tersedia dalam inventaris.';
    }

    /**
     * The screen's action buttons.
     *
     * @return \Orchid\Screen\Action[]
     */
    public function commandBar(): iterable
    {
        return [
            Link::make('Tambah Barang Baru')
                ->icon('bs.plus-circle')
                ->route('platform.barang.create'),
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
            Layout::table('barangs', [
                TD::make('nama_barang', 'Nama Barang')
                    ->sort()->filter(TD::FILTER_TEXT),

                TD::make('stok', 'Stok')
                    ->sort(),

                TD::make('created_at', 'Tanggal Dibuat')
                    ->sort()
                    ->render(fn (Barang $barang) => $barang->created_at->toDateTimeString()),

                TD::make(__('Actions'))
                    ->align(TD::ALIGN_CENTER)
                    ->width('150px')
                    ->render(fn (Barang $barang) => Dropdown::make()
                        ->icon('bs.three-dots-vertical')
                        ->list([
                            Link::make(__('Edit'))
                                ->route('platform.barang.edit', $barang->id)
                                ->icon('bs.pencil-square'),

                            Button::make(__('Delete'))
                                ->icon('bs.trash3')
                                ->confirm(__('Setelah barang dihapus, semua data akan hilang. Apakah Anda yakin?'))
                                ->method('remove', [
                                    'id' => $barang->id,
                                ]),
                        ])),
            ])
        ];
    }

    public function remove(Request $request): void{
        Barang::findOrFail($request->get('id'))->delete();

        Toast::info(__('Barang berhasil dihapus.'));
    }
}
