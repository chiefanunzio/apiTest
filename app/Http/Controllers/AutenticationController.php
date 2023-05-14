<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Auth\Events\Registered;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;


class AutenticationController extends Controller
{
    public function store(Request $request)
    {
        $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'surname' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'email', 'max:255', 'unique:users'],
            'password' => ['required', 'string', 'min:8'],
        ]);

        $user = User::create([
            "name" => $request->name,
            "surname" => $request->surname,
            "email" => $request->email,
            "password" => Hash::make($request->password),
            "data_nascita" => $request->data_nascita,
            "nome_dispositivo" => $request->nome_dispositivo,
            "azienda" => $request->azienda,
        ]);

        event(new Registered($user));

        return response()->json($user);
    }

    function login(Request $request)
    {
        $credentials = $request->only('email', 'password');
        if (Auth::attempt($credentials)) {
            $token = $request->user()->createToken('authAppToken')->plainTextToken;
            $user = $request->user();
            $authUser = auth()->user();
            return response()->json([
                'token' => $token,
                'user' => $authUser,
                'message' => 'log in effettuato'
            ]);
        }

        abort(401, "credenziali non riconosciute");
    }

    public function logout(Request $request)
    {
        $request->user()->currentAccessToken()->delete();
        return response()->json([
            'message' => 'logout effettuato'
        ]);
    }
}
