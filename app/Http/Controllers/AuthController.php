<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Mail;
use Illuminate\Validation\Rules\Password;
use App\Mail\VerificationMail;
use Carbon\Carbon;

class AuthController extends Controller
{
    // 1. REGJISTRIMI (REGISTER)
    public function register(Request $request)
    {
        // Llogarisim datën maksimale që përdoruesi të jetë të paktën 18 vjeç
        $maxDate = Carbon::now()->subYears(18)->format('Y-m-d');

        // Validimi i të dhënave strikte sipas kërkesave të taskut
        $fields = $request->validate([
            'name' => 'required|string|min:3',
            'last_name' => 'required|string|min:3',
            'email' => 'required|string|email|unique:users,email',
            'password' => [
                'required',
                'string',
                'confirmed', // Kërkon që në React të vijnë 'password' dhe 'password_confirmation'
                Password::min(8)
                    ->letters()   // Të paktën një shkronjë
                    ->mixedCase() // Të paktën një kapitale dhe një të vogël
                    ->numbers()   // Të paktën një numër
                    ->symbols()   // Të paktën një karakter special (@$!%*#?&)
            ],
            'birth_date' => 'required|date|before_or_equal:' . $maxDate
        ], [
            'birth_date.before_or_equal' => 'Duhet të jeni mbi 18 vjeç për t\'u regjistruar.',
            'password' => 'Fjalëkalimi duhet të jetë të paktën 8 karaktere dhe të përmbajë shkronjë kapitale, numër dhe simbol.',
            'email.unique' => 'Ky email ekziston në sistem.'
        ]);

        // Gjenerojmë një kod verifikimi random prej 6 shifrash
        $verificationCode = rand(100000, 999999);

        // Krijojmë përdoruesin në databazë si të paverifikuar (is_verified = false)
        $user = User::create([
            'name' => $fields['name'],
            'last_name' => $fields['last_name'],
            'email' => $fields['email'],
            'password' => Hash::make($fields['password']),
            'birth_date' => $fields['birth_date'],
            'verification_code' => $verificationCode,
            'is_verified' => false,
            'role' => 'customer'
        ]);

        // Dërgimi i email-it automatik me kodin e verifikimit përmes Mailtrap
        try {
            Mail::to($user->email)->send(new VerificationMail($verificationCode));
        } catch (\Exception $e) {
            // Nëse dështon dërgimi i email-it (p.sh. s'kemi internet ose limite në Mailtrap), 
            // kodi prapë regjistrohet që mos të bllokohesh gjatë testimeve.
        }

        return response()->json([
            'message' => 'Regjistrimi u krye me sukses. Ju lutem kontrolloni email-in tuaj për kodin e verifikimit.',
            'verification_code_test' => $verificationCode // Sa për ta parë në Postman pa hapur Mailtrap-in
        ], 201);
    }

    // 2. VERIFIKIMI I EMAIL-IT (VERIFY)
    public function verifyEmail(Request $request)
    {
        $request->validate([
            'email' => 'required|email',
            'code' => 'required|integer'
        ]);

        $user = User::where('email', $request->email)->first();

        // Kontrollojmë nëse përdoruesi ekziston dhe nëse kodi përputhet
        if (!$user || $user->verification_code != $request->code) {
            return response()->json([
                'message' => 'Kodi i verifikimit është i gabuar ose ka skaduar!'
            ], 400);
        }

        // Aktivizojmë llogarinë dhe fshijmë kodin që mos të përdoret më
        $user->is_verified = true;
        $user->verification_code = null;
        $user->save();

        // Pasi u verifikua me sukses, e autentikojmë direkt dhe i japim Token-in e sigurisë
        $token = $user->createToken('main_token')->plainTextToken;

        return response()->json([
            'message' => 'Llogaria juaj u verifikua me sukses. Mirëseerdhët në platformë!',
            'user' => $user,
            'token' => $token
        ], 200);
    }

    // 3. LOGINI (LOGIN)
    public function login(Request $request)
    {
        $fields = $request->validate([
            'email' => 'required|string|email',
            'password' => 'required|string'
        ]);

        $user = User::where('email', $fields['email'])->first();

        // Kontrolli i kredencialeve bazë (email dhe fjalëkalim)
        if (!$user || !Hash::check($fields['password'], $user->password)) {
            return response()->json([
                'message' => 'Kredencialet janë të gabuara!'
            ], 401);
        }

        // KUSHTI I TASKUT: Nëse përdoruesi nuk e ka konfirmuar account-in, nuk e lejojmë të futet
        if (!$user->is_verified) {
            return response()->json([
                'message' => 'Llogaria juaj nuk është verifikuar ende. Ju lutem verifikoni email-in përpara se të kyçeni.'
            ], 403);
        }

        // Nëse është i verifikuar, gjenerohet token-i normalisht për React-in
        $token = $user->createToken('main_token')->plainTextToken;

        return response()->json([
            'message' => 'Kyçja u krye me sukses!',
            'user' => $user,
            'token' => $token
        ], 200);
    }

    // 4. LOGOUT
    public function logout(Request $request)
    {
        $request->user()->currentAccessToken()->delete();

        return response()->json([
            'message' => 'U loguat jashtë me sukses!'
        ], 200);
    }
}