<?php

declare(strict_types=1);

namespace App\Orchid;

use Orchid\Platform\Dashboard;
use Orchid\Platform\ItemPermission;
use Orchid\Platform\OrchidServiceProvider;
use Orchid\Screen\Actions\Menu;
use Orchid\Support\Color;

class PlatformProvider extends OrchidServiceProvider
{
    /**
     * Bootstrap the application services.
     *
     * @param Dashboard $dashboard
     *
     * @return void
     */
    public function boot(Dashboard $dashboard): void
    {
        parent::boot($dashboard);

        // ...
    }

    /**
     * Register the application menu.
     *
     * @return Menu[]
     */
    public function menu(): array
    {
        return [
            Menu::make('Manajemen Barang')
                ->icon('bs.box-seam')
                ->title('Manajemen Inventaris')
                ->route('platform.barang.list')->permission('platform.barang.list'),

            Menu::make('Permintaan Barang')
                ->icon('bs.cart-plus')
                ->route('platform.permintaan.list')->permission('platform.permintaan.list'),

            Menu::make('Barang Masuk')
                ->icon('bs.box-arrow-in-down')
                ->route('platform.barang_masuk.list')->permission('platform.barang_masuk.list'),

            Menu::make('Barang Keluar')
                ->icon('bs.box-arrow-up-right')
                ->route('platform.barang_keluar.list')->permission('platform.barang_keluar.list'),

            Menu::make('Perbaikan Barang')
                ->icon('bs.wrench-adjustable-circle')
                ->route('platform.perbaikan_barang.list')->permission('platform.perbaikan_barang.list'),

            Menu::make(__('Users'))
                ->icon('bs.people')
                ->route('platform.systems.users')
                ->permission('platform.systems.users')
                ->title(__('Access Controls')),

            Menu::make(__('Roles'))
                ->icon('bs.shield')
                ->route('platform.systems.roles')
                ->permission('platform.systems.roles')
                ->divider(),
        ];
    }

    /**
     * Register permissions for the application.
     *
     * @return ItemPermission[]
     */
    public function permissions(): array
    {
        return [
            ItemPermission::group(__('System'))
                ->addPermission('platform.systems.roles', __('Roles'))
                ->addPermission('platform.systems.users', __('Users'))
                ->addPermission('platform.barang.list', __('Manajemen Barang'))
                ->addPermission('platform.permintaan.list', __('Permintaan Barang'))
                ->addPermission('platform.barang_masuk.list', __('Barang Masuk'))
                ->addPermission('platform.barang_keluar.list', __('Barang Keluar'))
                ->addPermission('platform.perbaikan_barang.list', __('Perbaikan Barang')),
        ];
    }
}
