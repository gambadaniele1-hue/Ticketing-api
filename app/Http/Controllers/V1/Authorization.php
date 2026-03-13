<?php

namespace App\Http\Controllers\V1;

use App\Http\Requests\V1\LoginRequest;
use App\Http\Resources\V1\UserResource;
use Illuminate\Auth\Events\Failed;
use Illuminate\Http\Request;
use App\Models\User;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\ValidationException;
use Firebase\JWT\JWT;

class Authorization
{
    public function login(LoginRequest $request)
    {
        // 1. Cerchiamo l'utente (usiamo first() per gestire noi l'errore)
        $user = User::where('email', $request->email)->first();

        // 2. Verifichiamo se l'utente esiste E se la password corrisponde
        // Hash::check confronta la password in chiaro ($request->password) 
        // con quella hashata nel DB ($user->password)
        if (!$user || !Hash::check($request->password, $user->password)) {
            // Lanciamo un'eccezione di validazione standard di Laravel
            throw ValidationException::withMessages([
                'email' => ['Le credenziali fornite non sono corrette.'],
            ]);
        }

        $key = config('app.key'); // Usa la tua chiave di sistema
        $payload = [
            'iss' => config('app.url'),          // Chi emette il token
            'iat' => time(),                     // Quando è stato creato
            'exp' => time() + (60 * 20),         // Scadenza (1 ora)
            'sub' => $user->user_id,             // ID dell'utente
        ];

        $jwt = JWT::encode($payload, $key, 'HS256');

        return response()->json([
            'message' => 'Login effettuato con successo',
            "data" => new UserResource($user) 
        ])
        ->cookie(
            'Authorization',     // Nome del cookie
            $jwt,                // Il tuo token JWT
            20,                  // Durata in minuti (es. 1 ora)
            '/',                 // Path (disponibile in tutto il sito)
            null,                // Domain (null = dominio corrente)
            true,                // Secure: invia il cookie SOLO su HTTPS
            true,                // HttpOnly: NON accessibile da JavaScript (Fondamentale!)
            false,               // Raw
            'Lax'                // SameSite: protezione base contro CSRF
        );
    }
}
