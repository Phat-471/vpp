<?php

namespace App\Http\Controllers;

use App\Http\Requests\PosLoginRequest;
use App\Services\StaffLogin;
use App\Support\StorefrontSettings;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Gate;
use Illuminate\Validation\ValidationException;

class PosAuthController extends Controller
{
    public function create()
    {
        if (Auth::guard('web')->check()) {
            Gate::authorize('use-pos');

            return redirect()->route('pos.index');
        }

        return view('pos.login', ['store' => app(StorefrontSettings::class)->all()]);
    }

    public function store(PosLoginRequest $request, StaffLogin $login)
    {
        $user = $login->findUser($request->validated('identifier'));
        $credentials = ['email' => $user?->email ?? '', 'password' => $request->validated('password')];
        if (! Auth::guard('web')->attempt($credentials)) {
            throw ValidationException::withMessages(['identifier' => 'Email, số điện thoại hoặc mật khẩu không chính xác.']);
        }
        if (! Gate::allows('use-pos')) {
            Auth::guard('web')->logout();
            throw ValidationException::withMessages(['identifier' => 'Tài khoản chưa được cấp quyền sử dụng quầy bán hàng.']);
        }
        $request->session()->regenerate();

        return redirect()->route('pos.index');
    }

    public function destroy(Request $request)
    {
        Auth::guard('web')->logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect()->route('pos.login');
    }
}
