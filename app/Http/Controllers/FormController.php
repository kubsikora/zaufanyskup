<?php

namespace App\Http\Controllers;

use App\Models\Form;
use Crypt;
use Illuminate\Http\Request;
use Mail;

class FormController extends Controller
{
    public function index(Request $request)
    {
        $query = Form::query();

        if ($request->filled('name')) {
            $query->where('name', 'like', '%' . $request->name . '%');
        }

        if ($request->filled('email')) {
            $query->where('email', 'like', $request->email . '%');
        }

        if ($request->filled('tel')) {
            $query->where('tel', 'like', $request->tel . '%');
        }

        $perPage = $request->get('per_page', 5);
        $page = $request->get('page', 1);

        $products = $query
            ->orderBy('id', 'desc')
            ->paginate($perPage, ['*'], 'page', $page);

        return response()->json([
            'data' => $products->items(),
            'total' => $products->total(),
            'current_page' => $products->currentPage(),
            'last_page' => $products->lastPage(),
            'per_page' => $products->perPage(),
        ]);
    }

    public function store(Request $request)
    {
        $request->validate([
            'email' => 'required|email',
            'name' => 'required',
            'tel' => 'required'
        ]);

        $cookieToken = $request->cookie('form_verify_token');

        if (!$cookieToken) {
            return response()->json(['error' => 'Brak lub wygasłe ciasteczko bezpieczeństwa.'], 403);
        }

        try {
            $expiresAt = Crypt::decryptString($cookieToken);
            if (time() > $expiresAt) {
                return response()->json(['error' => 'Token w ciasteczku wygasł.'], 403);
            }
        } catch (\Exception $e) {
            return response()->json(['error' => 'Błędne ciasteczko.'], 403);
        }

        $data = $request->all();
        $data['ip'] = $request->ip();
        Form::create($data);

        $this->sendMail($data);
        return response()->json(['success' => true]);
    }


    private function sendMail($data)
    {
        Mail::send([], [], function ($message) use ($data) {
            $message->to('kubsikora@gmail.com')
                ->subject('Nowe zgłoszenie od: ' . $data['name'])
                ->from('formularz@zaufanyskup.pl', 'Zaufany Skup')
                ->html("
                    <h2>Nowa wiadomość z formularza</h2>
                    <p><strong>Imię:</strong> {$data['name']}</p>
                    <p><strong>E-mail:</strong> {$data['email']}</p>
                    <p><strong>Telefon:</strong> {$data['tel']}</p>
                    <p><strong>Rodzaj zgłoszenia:</strong> {$data['other']}</p>
                    <p><strong>IP:</strong> {$data['ip']}</p>
                    <hr>
                    <p><strong>Treść wiadomości:</strong></p>
                    <p>" . nl2br(e($data['message'] ?? 'Brak treści')) . "</p>
                ");
        });

        // 2. Potwierdzenie DLA KLIENTA (auto-responder)
        Mail::send([], [], function ($message) use ($data) {
            $message->to($data['email']) // Adres wpisany w formularzu
                ->subject('Dziękujemy za kontakt - Zaufany Skup')
                ->from('formularz@zaufanyskup.pl', 'Zaufany Skup')
                ->html("
                    <h2>Witaj {$data['name']}!</h2>
                    <p>Dziękujemy za przesłanie zgłoszenia do <strong>zaufanyskup.pl</strong>.</p>
                    <p>Otrzymaliśmy Twoją wiadomość i postaramy się odpowiedzieć tak szybko, jak to możliwe.</p>
                    <br>
                    <p>Pozdrawiamy,<br>Zespół Zaufany Skup</p>
                ");
        });
    }
}
