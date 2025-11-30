<?php

use App\Http\Controllers\DashboardController;
use App\Http\Controllers\Menu\MenuGroupController;
use App\Http\Controllers\Menu\MenuItemController;
use App\Http\Controllers\RoleAndPermission\AssignPermissionController;
use App\Http\Controllers\RoleAndPermission\AssignUserToRoleController;
use App\Http\Controllers\RoleAndPermission\PermissionController;
use App\Http\Controllers\RoleAndPermission\RoleController;
use App\Http\Controllers\Set\ActivityLogController;
use App\Http\Controllers\UserController;
use Spatie\Permission\Models\Role;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "web" middleware group. Make something great!
|
*/

Route::get('/', function () {
    return view('welcome');
});

Route::get('/register/thanks', function () {
    return view('auth.thanks');
})->name('register.thanks');

Route::group(['middleware' => ['auth']], function () {
    Route::get('sidebar', [DashboardController::class, 'sidebar'])->name('sidebar');
    Route::get('dashboard', [DashboardController::class, 'index'])->name('dashboard');
    Route::post('/profile/update', [DashboardController::class, 'update'])->name('profile.update');

    Route::prefix('user-management')->group(function () {
        Route::resource('users', UserController::class);
        Route::post('/users/list', [UserController::class, 'list'])->name('users.list');
    });

    Route::prefix('menu-management')->group(function () {
        Route::resource('menu-groups', MenuGroupController::class);
        Route::post('/menu-groups/list', [MenuGroupController::class, 'list'])->name('menu-group.list');

        Route::get('menu-groups/{id}/menu-items', [MenuItemController::class, 'index'])->name('menu-items.index');
        Route::post('menu-groups/{id}/menu-items/list', [MenuItemController::class, 'list'])->name('menu-items.list');
        Route::post('menu-groups/{id}/menu-items', [MenuItemController::class, 'store'])->name('menu-items.store');
        Route::get('menu-groups/{id}/menu-items/{item}/edit', [MenuItemController::class, 'edit'])->name('menu-items.edit');
        Route::put('menu-groups/{id}/menu-items/{item}', [MenuItemController::class, 'update'])->name('menu-items.update');
        Route::delete('menu-groups/{id}/menu-items/{item}', [MenuItemController::class, 'destroy'])->name('menu-items.destroy');
    });

    Route::group(['prefix' => 'role-and-permission'], function () {
        //role
        Route::resource('roles', RoleController::class);
        Route::post('/roles/list', [RoleController::class, 'list'])->name('role.list');

        //permission
        Route::resource('permissions', PermissionController::class);
        Route::post('/permissions/list', [PermissionController::class, 'list'])->name('permission.list');

        //assign permission
        Route::get('assign', [AssignPermissionController::class, 'index'])->name('assign.index');
        Route::get('assign/{role}/edit', [AssignPermissionController::class, 'edit'])->name('assign.edit');
        Route::put('assign/{role}', [AssignPermissionController::class, 'update'])->name('assign.update');
        Route::post('/assign/list', [AssignPermissionController::class, 'list'])->name('assign.list');



        //assign user to role
        Route::get('assign-user', [AssignUserToRoleController::class, 'index'])->name('assign.user.index');
        Route::get('assign-user/create', [AssignUserToRoleController::class, 'create'])->name('assign.user.create');
        Route::post('assign-user', [AssignUserToRoleController::class, 'store'])->name('assign.user.store');
        Route::get('assign-user/{user}/edit', [AssignUserToRoleController::class, 'edit'])->name('assign.user.edit');
        Route::put('assign-user/{user}', [AssignUserToRoleController::class, 'update'])->name('assign.user.update');
        Route::post('/assign-user/list', [AssignUserToRoleController::class, 'list'])->name('assign.user.list');
    });

    Route::prefix('setting-management')->group(function () {
        Route::resource('log-activity', ActivityLogController::class);
        Route::post('/log-activity/list', [ActivityLogController::class, 'list'])->name('log-activity.list');
    });
});
