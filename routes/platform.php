<?php

declare(strict_types=1);

use App\Orchid\Screens\Examples\ExampleActionsScreen;
use App\Orchid\Screens\Examples\ExampleCardsScreen;
use App\Orchid\Screens\Examples\ExampleChartsScreen;
use App\Orchid\Screens\Examples\ExampleFieldsAdvancedScreen;
use App\Orchid\Screens\Examples\ExampleFieldsScreen;
use App\Orchid\Screens\Examples\ExampleGridScreen;
use App\Orchid\Screens\Examples\ExampleLayoutsScreen;
use App\Orchid\Screens\Examples\ExampleScreen;
use App\Orchid\Screens\Examples\ExampleTextEditorsScreen;
use App\Orchid\Screens\PlatformScreen;
use App\Orchid\Screens\BarangEditScreen;
use App\Orchid\Screens\BarangMasukEditScreen;
use App\Orchid\Screens\BarangKeluarEditScreen;
use App\Orchid\Screens\BarangKeluarListScreen;
use App\Orchid\Screens\PerbaikanBarangEditScreen;
use App\Orchid\Screens\PerbaikanBarangListScreen;
use App\Orchid\Screens\BarangMasukListScreen;
use App\Orchid\Screens\BarangListScreen;
use App\Orchid\Screens\PermintaanBarangEditScreen;
use App\Orchid\Screens\PermintaanBarangListScreen;
use App\Orchid\Screens\Role\RoleEditScreen;
use App\Orchid\Screens\Role\RoleListScreen;
use App\Orchid\Screens\User\UserEditScreen;
use App\Orchid\Screens\User\UserListScreen;
use App\Orchid\Screens\User\UserProfileScreen;
use Illuminate\Support\Facades\Route;
use Tabuna\Breadcrumbs\Trail;

/*
|--------------------------------------------------------------------------
| Dashboard Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the need "dashboard" middleware group. Now create something great!
|
*/

// Main
Route::screen('/main', PlatformScreen::class)
    ->name('platform.main');

// Platform > Profile
Route::screen('profile', UserProfileScreen::class)
    ->name('platform.profile')
    ->breadcrumbs(fn (Trail $trail) => $trail
        ->parent('platform.index')
        ->push(__('Profile'), route('platform.profile')));

// Platform > Barang > Edit
Route::screen('barang', BarangListScreen::class)
    ->name('platform.barang.list');
Route::screen('barang/create', BarangEditScreen::class)
    ->name('platform.barang.create');
Route::screen('barang/{barang}/edit', BarangEditScreen::class)
    ->name('platform.barang.edit');

// Platform > Permintaan Barang
Route::screen('permintaan', PermintaanBarangListScreen::class)
    ->name('platform.permintaan.list');
Route::screen('permintaan/create', PermintaanBarangEditScreen::class)
    ->name('platform.permintaan.create');
Route::screen('permintaan/{permintaan}/edit', PermintaanBarangEditScreen::class)
    ->name('platform.permintaan.edit');

// Platform > Barang Masuk
Route::screen('barang-masuk', BarangMasukListScreen::class)
    ->name('platform.barang_masuk.list');
Route::screen('barang-masuk/create', BarangMasukEditScreen::class)
    ->name('platform.barang_masuk.create');
// Route untuk edit sengaja dinonaktifkan.
// Pencatatan barang masuk bersifat historis, jika ada kesalahan lebih baik dihapus dan dibuat ulang.
// Route::screen('barang-masuk/{barang_masuk}/edit', BarangMasukEditScreen::class)->name('platform.barang_masuk.edit');

// Platform > Barang Keluar
Route::screen('barang-keluar', BarangKeluarListScreen::class)
    ->name('platform.barang_keluar.list');
Route::screen('barang-keluar/create', BarangKeluarEditScreen::class)
    ->name('platform.barang_keluar.create');
// Route untuk edit sengaja dinonaktifkan.
// Route::screen('barang-keluar/{barang_keluar}/edit', BarangKeluarEditScreen::class)->name('platform.barang_keluar.edit');

// Platform > Perbaikan Barang
Route::screen('perbaikan-barang', PerbaikanBarangListScreen::class)
    ->name('platform.perbaikan_barang.list');
Route::screen('perbaikan-barang/create', PerbaikanBarangEditScreen::class)
    ->name('platform.perbaikan_barang.create');
Route::screen('perbaikan-barang/{perbaikan}/edit', PerbaikanBarangEditScreen::class)
    ->name('platform.perbaikan_barang.edit');

// Platform > System > Users > User
Route::screen('users/{user}/edit', UserEditScreen::class)
    ->name('platform.systems.users.edit')
    ->breadcrumbs(fn (Trail $trail, $user) => $trail
        ->parent('platform.systems.users')
        ->push($user->name, route('platform.systems.users.edit', $user)));

// Platform > System > Users > Create
Route::screen('users/create', UserEditScreen::class)
    ->name('platform.systems.users.create')
    ->breadcrumbs(fn (Trail $trail) => $trail
        ->parent('platform.systems.users')
        ->push(__('Create'), route('platform.systems.users.create')));

// Platform > System > Users
Route::screen('users', UserListScreen::class)
    ->name('platform.systems.users')
    ->breadcrumbs(fn (Trail $trail) => $trail
        ->parent('platform.index')
        ->push(__('Users'), route('platform.systems.users')));

// Platform > System > Roles > Role
Route::screen('roles/{role}/edit', RoleEditScreen::class)
    ->name('platform.systems.roles.edit')
    ->breadcrumbs(fn (Trail $trail, $role) => $trail
        ->parent('platform.systems.roles')
        ->push($role->name, route('platform.systems.roles.edit', $role)));

// Platform > System > Roles > Create
Route::screen('roles/create', RoleEditScreen::class)
    ->name('platform.systems.roles.create')
    ->breadcrumbs(fn (Trail $trail) => $trail
        ->parent('platform.systems.roles')
        ->push(__('Create'), route('platform.systems.roles.create')));

// Platform > System > Roles
Route::screen('roles', RoleListScreen::class)
    ->name('platform.systems.roles')
    ->breadcrumbs(fn (Trail $trail) => $trail
        ->parent('platform.index')
        ->push(__('Roles'), route('platform.systems.roles')));

// Example...
Route::screen('example', ExampleScreen::class)
    ->name('platform.example')
    ->breadcrumbs(fn (Trail $trail) => $trail
        ->parent('platform.index')
        ->push('Example Screen'));

Route::screen('/examples/form/fields', ExampleFieldsScreen::class)->name('platform.example.fields');
Route::screen('/examples/form/advanced', ExampleFieldsAdvancedScreen::class)->name('platform.example.advanced');
Route::screen('/examples/form/editors', ExampleTextEditorsScreen::class)->name('platform.example.editors');
Route::screen('/examples/form/actions', ExampleActionsScreen::class)->name('platform.example.actions');

Route::screen('/examples/layouts', ExampleLayoutsScreen::class)->name('platform.example.layouts');
Route::screen('/examples/grid', ExampleGridScreen::class)->name('platform.example.grid');
Route::screen('/examples/charts', ExampleChartsScreen::class)->name('platform.example.charts');
Route::screen('/examples/cards', ExampleCardsScreen::class)->name('platform.example.cards');

// Route::screen('idea', Idea::class, 'platform.screens.idea');
