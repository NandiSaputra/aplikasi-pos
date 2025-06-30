<?php

use App\Http\Controllers\LaporanController;
use App\Http\Controllers\MidtransController;
use App\Http\Controllers\PaymentController;
use App\Http\Controllers\ProfileController;

use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Route;
use App\Exports\LaporanExport;
use Illuminate\Http\Request;
use Maatwebsite\Excel\Excel as ExcelExcel;
use Maatwebsite\Excel\Facades\Excel;



// Default home, bisa redirect berdasarkan role
Route::get('/', function () {
    return redirect('/dashboard');
});



    
Route::middleware('auth')->group(function () {
     Route::get('/dashboard', function () {
        // Hanya kasir yang bisa
        if (Auth::user()->role === 'cashier') {
            return view('dashboard');
        }else{
            Auth::logout();
            
            //hancurkan session
            request()->session()->invalidate(); 
            request()->session()->regenerateToken();

            return redirect('/login')->with('status', 'Anda tidak memiliki akses ke halaman ini. Silakan login dengan akun kasir.');
        }
        
    })->name('dashboard');


    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
  

    Route::get('/checkout/{order}', [MidtransController::class, 'checkout'])->name('midtrans.checkout');
    Route::get('/pending', [PaymentController::class, 'pending'])->name('payment.pending');
    Route::post('/cancel-payment', [PaymentController::class, 'cancel'])->name('payment.cancel');
    Route::get('/transaksi', function () {
        return view('transaksi'); // view ini berisi <livewire:transaksi />
    })->name('transaksi');
    // web.php
    Route::get('/midtrans/unfinish', function () {
        return redirect('/transaksi')->with('status', 'Pembayaran dibatalkan oleh pengguna.');
    });
    Route::get('/pending', function (Request $request) {
        $invoice = $request->query('invoice');
        return view('pending', compact('invoice'));
    })->name('pending.transaksi');
    
    



});

require __DIR__.'/auth.php';
