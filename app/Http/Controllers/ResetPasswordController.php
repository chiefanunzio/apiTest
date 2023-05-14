<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Password;
use App\Models\PasswordReset;
use Illuminate\Support\Str;
use App\Models\User;
use App\Notifications\PasswordResetRequest;
use App\Notifications\PasswordResetSuccess;

class ResetPasswordController extends Controller
{
     /**
     * Create token password reset
     */
     public function create(Request $request)
     {
            $request->validate([
                'email' => 'required|string|email',
            ]);

            $user = User::where('email', $request->email)->first();
            if (!$user)
                return response()->json([
                    'message' => 'non ho trovato utenti con questa email'
                ], 404);
            $passwordReset = PasswordReset::updateOrCreate(
                [
                    'email' => $user->email,
                    'token' => Str::random(60)
                ]
            );
            if ($user && $passwordReset)
                $user->notify( new PasswordResetRequest($passwordReset->token));
            return response()->json([
                'message' => 'Abbiamo inviato la tua email di reset password'
            ]);
        }

        /**
         * Find token password reset
         */

        public function find($token)
        {
            $passwordReset = PasswordReset::where('token', $token)
                ->first();
            if (!$passwordReset)
                return response()->json([
                    'message' => 'Questo token di reset password non è valido'
                ], 404);
            return response()->json($passwordReset);
        }

        /**
         * Reset password
         */

        public function reset(Request $request)
        {
            $request->validate([
                'email' => 'required|string|email',
                'password' => 'required|string',
                'token' => 'required|string'
            ]);
            $passwordReset = PasswordReset::where([
                ['token', $request->token],
                ['email', $request->email]
            ])->first();
            if (!$passwordReset)
                return response()->json([
                    'message' => 'Questo token di reset password non è valido'
                ], 404);
            $user = User::where('email', $passwordReset->email)->first();
            if (!$user)
                return response()->json([
                    'message' => 'Non ho trovato utenti con questa email'
                ], 404);
            $user->password = bcrypt($request->password);
            $user->save();
            $passwordReset = PasswordReset::where([ ['token', $request->token], ['email', $request->email] ])->delete();
            $user->notify(new PasswordResetSuccess($user));
            return response()->json($user);
        }
}
