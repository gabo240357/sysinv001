<?php

use App\Http\Controllers\CashRegisterController;
use App\Http\Controllers\PaymentController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Route;

Route::middleware('guest')->group(function () {
    Route::view('/login', 'login')->name('login');

    Route::post('/login', function (Request $request) {
        $credentials = $request->validate([
            'email' => ['required', 'email'],
            'password' => ['required'],
        ]);

        if (Auth::attempt($credentials)) {
            $request->session()->regenerate();
            return redirect()->intended('/');
        }

        return back()->withInput($request->only('email'))
            ->withErrors(['email' => 'Credenciales no válidas, por favor verifica tu correo y contraseña.']);
    })->name('login.submit');
});

Route::post('/logout', function (Request $request) {
    Auth::logout();
    $request->session()->invalidate();
    $request->session()->regenerateToken();

    return redirect()->route('login');
})->name('logout');

Route::middleware('auth')->group(function () {
    Route::get('/', function () {
        return view('app');
    });

    Route::get('/web', function () {
        return view('app');
    });

    Route::prefix('web-api')->group(function () {
        Route::post('/cash-registers', [CashRegisterController::class, 'store']);
        Route::put('/cash-registers/{cashRegister}', [CashRegisterController::class, 'update']);
        Route::patch('/cash-registers/{cashRegister}', [CashRegisterController::class, 'update']);
        Route::post('/payments', [PaymentController::class, 'store']);
    });
});
