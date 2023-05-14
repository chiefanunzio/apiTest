<?php

namespace App\Http\Controllers;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;

class UserController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $users = User::all();
        return response()->json($users, 200);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create(Request $request)
    {
        // return view create?
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show(User $user)
    {
        if ($user) {
            return response()->json($user, 200);
        }
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\User  $user
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, User $user)
    {
        $request->validate([
            'name' => ['nullable', 'string', 'max:255'],
            'surname' => ['nullable', 'string', 'max:255'],
            'email' => ['nullable', 'string', 'email', 'max:255'],
            'data_nascita' => ['nullable'],
            'nome_dispositivo' => ['nullable', 'string', 'max:255'],
            'azienda' => ['nullable', 'string', 'max:255'],
        ]);

        // non aggiornare la password se non viene passata
        if ($request->password) {
            return response()->json([
                'message' => 'stai tentando di modificare la password con un metodo non consentito'
            ], 422);
        }
        $user->update($request->all());
        return response()->json([
            'message' => 'utente aggiornato',
            'user' => $user
        ], 200);
    }

    /**
     * update password of the specified resource in storage.
     * 
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\User $user
     * 
     */

    public function updatePassword(Request $request, User $user)
    {
        $request->validate([
            'old_password' => ['required', 'string', 'min:8'],
            'new_password' => ['required', 'string', 'min:8'],
            'confirm_password' => ['required', 'string', 'min:8'],
        ]);
        if (Hash::check($request->old_password, $user->password)) {
            if ($request->new_password == $request->confirm_password) {
                $user->update([
                    "password" => Hash::make($request->new_password),
                ]);
                return response()->json([
                    'message' => 'password aggiornata con successo',
                    'user' => $user
                ], 200);
            }
            abort(401, "password non corrispondenti");
        }
        abort(401, "la vecchia password non è corretta");
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\User  $user
     * @return \Illuminate\Http\Response
     */
    public function destroy(User $user)
    {
        if ($user) {
            $user->delete();
            return response()->json([
                'message' => 'utente eliminato'
            ], 200);
        }
    }
}
