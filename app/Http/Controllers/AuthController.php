<?php
namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class AuthController extends Controller
{
    public function showLogin()
    {
        return view('auth.login');
    }

    public function login(Request $request)
    {
        $request->validate([
            'email'    => 'required|email',
            'password' => 'required|min:8',
        ]);

        if (Auth::attempt($request->only('email', 'password'))) {
            $role = Auth::user()->role;

            return match($role) {
                'admin'   => redirect()->route('admin.dashboard'),
                'teacher' => redirect()->route('teacher.dashboard'),
                'student' => redirect()->route('student.dashboard'),
                default   => redirect('/'),
            };
        }

        return back()->withErrors([
            'email' => 'អ៊ីមែល ឬ លេខសម្ងាត់មិនត្រឹមត្រូវ!',
        ])->withInput();
    }

    public function logout(Request $request)
    {
        Auth::logout();
        return redirect()->route('login');
    }
}