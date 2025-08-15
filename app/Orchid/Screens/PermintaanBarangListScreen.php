<?php

namespace App\Orchid\Screens;

use App\Models\PermintaanBarang;
use Illuminate\Http\Request;
use Orchid\Screen\Actions\Button;
use Orchid\Screen\Actions\Dropdown;
use Orchid\Screen\Actions\Link;
use Orchid\Screen\Screen;
use Orchid\Screen\TD;
use Orchid\Support\Facades\Layout;
use Orchid\Support\Facades\Toast;


class PermintaanBarangListScreen extends Screen
{
    /**
     * Fetch data to be displayed on the screen.
     *
     * @return array
     */
    public function query(): iterable
    {
        return [
            'permintaans' => PermintaanBarang::with(['barang'])
                ->filters()
                ->defaultSort('created_at', 'desc')
                ->paginate(),
        ];
    }

    /**
     * The name of the screen displayed in the header.
     *
     * @return string|null
     */
    public function name(): ?string
    {
        return 'Daftar Permintaan Barang';
    }

    /**
     * The description of the screen displayed in the header.
     *
     * @return string|null
     */
    public function description(): ?string
    {
        return 'Kelola permintaan barang yang telah diajukan.';
    }

    /**
     * The screen's action buttons.
     *
     * @return \Orchid\Screen\Action[]
     */
    public function commandBar(): iterable
    {
        return [
            Link::make('Buat Permintaan Baru')
                ->icon('bs.plus-circle')
                ->route('platform.permintaan.create'),
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
            Layout::table('permintaans', [
                TD::make('barang.nama_barang', 'Nama Barang')->render(fn (PermintaanBarang $model) => $model->barang->nama_barang ?? 'N/A'),

                TD::make('jumlah', 'Jumlah'),

                TD::make('status', 'Status')->render(fn (PermintaanBarang $model) => "<span class='badge bg-".['pending' => 'warning', 'approved' => 'success', 'rejected' => 'danger'][$model->status]."'>".ucfirst($model->status)."</span>"),

                TD::make('created_at', 'Tanggal Dibuat')->render(fn (PermintaanBarang $model) => $model->created_at->toDateTimeString()),

                TD::make(__('Actions'))
                    ->align(TD::ALIGN_CENTER)
                    ->width('100px')
                    ->render(fn (PermintaanBarang $permintaan) => DropDown::make()
                        ->icon('bs.three-dots-vertical')
                        ->list([
                            Link::make(__('Edit'))->route('platform.permintaan.edit', $permintaan->id)->icon('bs.pencil'),
                            
                            Button::make(__('Delete'))->icon('bs.trash3')->confirm(__('Permintaan ini akan dihapus secara permanen.'))->method('remove', ['id' => $permintaan->id,]),
                        ])),
            ])
        ];
    }

    /**
     * Handle the removal of a permintaan.
     *
     * @param Request $request
     * @return \Illuminate\Http\RedirectResponse
     */
    public function remove(Request $request): void
    {
        PermintaanBarang::findOrFail($request->get('id'))->delete();

        Toast::info(__('Permintaan barang berhasil dihapus.'));
    }
}
