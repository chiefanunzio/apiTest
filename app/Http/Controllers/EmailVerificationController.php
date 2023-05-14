<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Nette\Utils\Json;
use App\Models\User;
use Illuminate\Support\Facades\Hash;

class EmailVerificationController extends Controller
{
    public function verify($id, $hash)
    {
        $user = User::findorfail($id);
        if ($hash == md5($user->email) . env('PASSWORD_TOKEN')) {
            $user->markEmailAsVerified();
            return response()->json(['message' => 'Email verified', 'at' => now()]);
        }else if ($user->hasVerifiedEmail()) {
            return response()->json(['message' => 'Email already verified']);
        }
        return response()->json(['message' => 'Email not verified']);
    }
}
