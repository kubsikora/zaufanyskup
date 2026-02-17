<?php

namespace App\Http\Controllers;

use App\Models\User;
use Carbon\Carbon;
use Crypt;
use Hash;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Mail;
use Str;

class AuthController extends Controller
{
    public function login(Request $request)
    {
        $request->validate([
            'email' => 'required|email',
            'password' => 'required'
        ]);

        $reset = $request->input('reset');
        $user = User::where('email', $request->email)->first();

        if ($user->password_reset == true && $reset == true && !$user) {
                return response()->json(['message' => 'Użytkownik nie istnieje'], 401);
            }

        if ($user->password_reset == true && $reset == true && $user->email == $request->email && $user->password == $request->password) {
            $token = $user->createToken('auth_token')->plainTextToken;

            return response()->json([
                'message' => 'Zalogowano pomyślnie',
                'user' => $user,
                'token' => $token,
                'reset' => true,
            ]);
        }
        if (!$user || !Hash::check($request->password, $user->password)) {
            return response()->json([
                'message' => 'Nieprawidłowy email lub hasło'
            ], 401);
        }


        $token = $user->createToken('auth_token')->plainTextToken;

        return response()->json([
            'message' => 'Zalogowano pomyślnie',
            'user' => $user,
            'token' => $token
        ]);
    }

    public function logout(Request $request)
    {
        $user = $request->user();

        if ($user) {
            $user->tokens()->delete();

            return response()->json([
                'message' => 'Wylogowano pomyślnie'
            ]);
        }

        return response()->json([
            'message' => 'Brak zalogowanego użytkownika'
        ], 401);
    }

    public function token(Request $request)
    {
        $expiresAt = time() + (30 * 60);
        $encryptedValue = Crypt::encryptString($expiresAt);


        return response()->json(['message' => 'Security initialized'])
            ->cookie('form_verify_token', $encryptedValue, 30);
    }

    public function forgotPassword(Request $request)
    {
        $request->validate([
            'email' => 'required|email',
        ]);
        $data = $request->all();

        $temp = Str::random(8);
        User::where('email', $data['email'])->update(['password' => $temp, 'password_reset' => true]);

        Mail::send([], [], function ($message) use ($data, $temp) {
            $message->to($data['email'])
                ->subject('Kod uwierzytelnia')
                ->from('formularz@zaufanyskup.pl', 'Zaufany Skup')
                ->html("
                    <h2>Wpisz poniższy kod w wyznaczonym miejscu na stronie</h2>
                    <h1>" . $temp . "</h1>
                ");
        });
    }
}
