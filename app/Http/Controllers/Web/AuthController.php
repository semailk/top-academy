<?php

namespace App\Http\Controllers\Web;

use App\Jobs\MailSendJob;
use App\Jobs\VerifyMailSendJob;
use App\Mail\WelcomeRegisterMail;
use App\Models\Role;
use App\Models\User;
use Illuminate\Auth\Events\Registered;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Validator;

class AuthController extends Controller
{
    public function showLogin()
    {
        return view('auth.login');
    }

    public function showRegister()
    {
        return view('auth.register');
    }

    public function register(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'username' => 'required|string|max:255|unique:users',
            'email' => 'required|string|email|max:255|unique:users',
            'password' => 'required|string|min:6|confirmed',
        ]);

        if ($validator->fails()) {
            return redirect()->back()->withErrors($validator)->withInput();
        }

        $user = User::create([
            'username' => $request->username,
            'email' => $request->email,
            'password' => Hash::make($request->password),
            'status' => 'user',
            'role_id' => Role::query()
                ->whereRaw('LOWER(name) = ?', [strtolower('uSeR')])
                ->first()?->id
        ]);

        dispatch(new VerifyMailSendJob($user));

        Auth::login($user);

        return redirect()->route('posts.index');
    }

    public function login(Request $request)
    {
        // Позволяем логин по username или email
        $loginType = filter_var($request->input('login'), FILTER_VALIDATE_EMAIL) ? 'email' : 'username';

        $request->merge([$loginType => $request->input('login')]);

        $credentials = $request->validate([
            'username' => 'required_without:email|string|exists:users,username',
            'email' => 'required_without:username|email|exists:users,email',
            'password' => 'required|string',
        ]);

        if (Auth::attempt($credentials)) {
            $request->session()->regenerate();
            return redirect()->route('posts.index');
        }

        return back()->withErrors([
            'login' => 'Неверные учетные данные.',
        ])->onlyInput('login');
    }

    public function logout(Request $request)
    {
        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();
        return redirect()->route('login');
    }
}
