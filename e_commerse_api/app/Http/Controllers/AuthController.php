<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\Rules\Password;
use App\Mail\VerificationMail;
use App\Http\Requests\RegisterRequest;

class AuthController extends Controller
{
    //register
    public function register(RegisterRequest $request)
    {
       //validate
         $fields = $request->validated();

        // the code
        $verificationCode = rand(100000, 999999);

        // the user
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

        //code me mailtrap
        try {
            Mail::to($user->email)->send(new VerificationMail($verificationCode));
        } catch (\Exception $e) {
           
        }

        return response()->json([
            'message' => 'Register was successful. Check ur email for the verification code.',
            'verification_code_test' => $verificationCode 
        ], 201);
    }

    // verify
    public function verifyEmail(Request $request)
    {
        $request->validate([
            'email' => 'required|email',
            'code' => 'required|integer'
        ]);

        $user = User::where('email', $request->email)->first();

        
        if (!$user || $user->verification_code != $request->code) {
            return response()->json([
                'message' => 'Code is not correct'
            ], 400);
        }

        $user->is_verified = true;
        $user->verification_code = null;
        $user->save();

        // token
        $token = $user->createToken('main_token')->plainTextToken;

        return response()->json([
            'message' => 'U are verified',
            'user' => $user,
            'token' => $token
        ], 200);
    }

    // login
    public function login(Request $request)
    {
        $fields = $request->validate([
            'email' => 'required|string|email',
            'password' => 'required|string'
        ]);

        $user = User::where('email', $fields['email'])->first();

        if (!$user || !Hash::check($fields['password'], $user->password)) {
            return response()->json([
                'message' => 'Wrong password'
            ], 401);
        }

       
        if (!$user->is_verified) {
            return response()->json([
                'message' => 'U are not verified'
            ], 403);
        }
        
        //token per React
        $token = $user->createToken('main_token')->plainTextToken;

        return response()->json([
            'message' => 'U verifikua',
            'user' => $user,
            'token' => $token
        ], 200);
    }
    

   
  

    // forgot pass
    public function forgotPassword(Request $request)
    {
        $request->validate([
            'email' => 'required|email|exists:users,email'
        ]);

        $code = Str::random(6); 
        DB::table('password_reset_tokens')->updateOrInsert(
            ['email' => $request->email],
            [
                'email'      => $request->email,
                'token'      => $code,
                'created_at' => now()
            ]
        );

        
        Mail::raw("Your code: $code", function($message) use ($request) {
            $message->to($request->email)
                    ->subject('Reset Pass');
        });

        return response()->json(['message' => 'Your code was sent.'], 200);
    }

    // reset pass
    public function resetPassword(Request $request)
    {
        $request->validate([
            'email'    => 'required|email',
            'code'     => 'required|string',
            'password' => 'required|min:8|confirmed',
        ]);

       
        $record = DB::table('password_reset_tokens')
                    ->where('email', $request->email)
                    ->where('token', $request->code)
                    ->first();

        if (!$record) {
            return response()->json(['message' => 'Your code is wrong.'], 422);
        }

        if (now()->diffInMinutes($record->created_at) > 15) {
            return response()->json(['message' => '15 minutes have passed.'], 422);
        }

       
        User::where('email', $request->email)
            ->update(['password' => Hash::make($request->password)]);

       
        DB::table('password_reset_tokens')->where('email', $request->email)->delete();

        return response()->json(['message' => 'Your password was reset.'], 200);
    }




    //logout
    public function logout(Request $request)
    {
        $request->user()->currentAccessToken()->delete();

        return response()->json([
            'message' => 'Logout succsesfull!'
        ], 200);
    }
}