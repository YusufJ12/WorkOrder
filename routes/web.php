<?php

use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Auth;

use App\Http\Controllers\HomeController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\RoleController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\ManagerController;
use App\Http\Controllers\OperatorController;
use App\Http\Controllers\WorkOrderController;
use App\Http\Controllers\ProductController;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/

// Route untuk mengekspor laporan
Route::get('/workorders/export', [WorkOrderController::class, 'export'])->name('workorders.export');


Route::get('/', function () {
    return view('../auth/login');
});

Auth::routes();

Route::get('/check-session-status', function () {
    if (auth()->check()) {
        // Pengguna masih terotentikasi
        return response()->json(['status' => 'active']);
    } else {
        // Sesinya telah habis
        return response()->json(['status' => 'expired']);
    }
})->name('check-session-status');

Route::middleware(['middleware' => 'auth'])->group(function () {
    Route::get('/home', [HomeController::class, 'index'])->name('home');
    Route::get('/profile', [ProfileController::class, 'index'])->name('profile');
    Route::put('/profile', [ProfileController::class, 'update'])->name('profile.update');


    Route::middleware(['type:1'])->group(function () {
        Route::resource('roles', RoleController::class);
        Route::resource('users', UserController::class);
    });

    Route::get('/users/password/{id}', [UserController::class, 'password'])->name('users.password');
    Route::post('/users/updatepassword/{id}', [UserController::class, 'updatepassword'])->name('users.updatepassword');

    Route::get('user-data', [UserController::class, 'getUserData']);
    Route::post('/user/save', [UserController::class, 'store'])->name('user.save');
    Route::delete('/user/delete/{id}', [UserController::class, 'destroy'])->name('user.destroy');
    Route::get('/user/edit/{id}', [UserController::class, 'edit'])->name('user.edit');
    Route::post('/user/update/{id}', [UserController::class, 'update'])->name('user.update');

    Route::get('/role-data', [RoleController::class, 'getRoleData']);
    Route::post('/roles/store', [RoleController::class, 'store']);
    Route::put('/roles/update/{id}', [RoleController::class, 'update']);
    Route::delete('/roles/delete/{id}', [RoleController::class, 'destroy']);
    Route::get('/roles/{id}', [RoleController::class, 'show']);

    Route::resource('products', ProductController::class);
    Route::get('/products-data', [ProductController::class, 'getData'])->name('products.data');
    Route::delete('/products/delete/{id}', [ProductController::class, 'destroy'])->name('products.destroy');

    Route::get('/manager', [ManagerController::class, 'index'])->name('manager.index');

    Route::get('/workorders-data', [ManagerController::class, 'getDataManager'])->name('workorders.dataManager');
    Route::get('/workorders/generateNomor', [ManagerController::class, 'generateNomor'])->name('workorder.generateNomor');
    Route::get('/workorders/getNamaProduk', [ManagerController::class, 'getNamaProduk'])->name('workorder.getNamaProduk');
    Route::get('/workorders/getOperator', [ManagerController::class, 'getOperator'])->name('workorder.getOperator');
    Route::post('/workorders', [ManagerController::class, 'store'])->name('workorder.store');
    Route::delete('/workorders/{id}', [ManagerController::class, 'destroy'])->name('workorder.destroy');
    Route::get('/workorders/{id}/edit', [ManagerController::class, 'edit'])->name('workorder.edit');
    Route::put('/workorders/{id}', [ManagerController::class, 'update'])->name('workorder.update');

    Route::get('/operator', [OperatorController::class, 'index'])->name('operator.index');
    Route::get('/operator/workorders', [OperatorController::class, 'getDataOperator'])->name('operator.getDataOperator');
    Route::post('/operator/workorders/{id}/update', [OperatorController::class, 'updateStatus'])->name('operator.updateStatus');
    Route::get('/operator/workorders-counts', [OperatorController::class, 'getCounts'])->name('workorders.counts');
    Route::get('/operator/workorders/{id}/detail', [WorkOrderController::class, 'getWorkOrderDetail']);
});
