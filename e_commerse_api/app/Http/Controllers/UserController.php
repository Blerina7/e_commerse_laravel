<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Validation\Rules\Password;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;
use App\Models\User;
use Carbon\Carbon;

class UserController extends Controller
{
   //te gjithe userat
    public function index()
    {
        $users =User::all();
        return response()->json($users,200);
    }

    //1 user specifik
    public function show(User $user)
    {
        return response()->json($user);
    }
    
    //kush user eshte i loguar(perdore te profili)
     public function profile(Request $request)
    {
        $user = $request->user();
        return response()->json([
        'message' => 'Profili u ngarkua me sukses',
        'user' => $user
    ], 200);
    }


   //updeto userat
    public function update(Request $request, User $user)
    {   
        $currentUser = $request->user();
        if ($currentUser->role === 'manager') {
       
           if ($user->role === 'admin' || $user->role === 'manager') {
             return response()->json([
                'message' => 'A manger can change only their workers!'
            ], 403);
           }
        } 
        elseif ($currentUser->role !== 'admin') {
        
            if ($currentUser->id !== $user->id) {
             return response()->json([
                'message' => 'U can not update this user'
            ], 403);
            }
        }


         $maxDate = Carbon::now()->subYears(18)->format('Y-m-d');
         $data = $request->validate([
            'name' => 'required|string|min:3',
            'last_name' => 'required|string|min:3',
            'email' => ['required', 'email', 'max:255', 'unique:users,email,' . $user->id],
            'photo' => 'nullable|image|mimes:jpeg,png,jpg|max:2048',
            'role' => 'required|string',
            'password' => [
                'string',
                'confirmed',
                Password::min(8)->letters()->mixedCase()->numbers()->symbols()
            ],
            'birth_date' => 'required|date|before_or_equal:' . $maxDate
            ]
        );

        
        if ($user->role === 'admin') {
            if ($request->input('role') !== 'admin') {
                return response()->json([
                'message' => 'U can not change the role of an admin!'
                ], 403);
            }
        }

        if ($request->hasFile('photo')) {
            if ($user->photo) {
                Storage::disk('public')->delete($user->photo);
            }
            $path = $request->file('photo')->store('photos', 'public');

            $data['photo'] = $path;
        } else {
            unset($data['photo']);
        }

        if (!empty($data['password'])) {
            $data['password'] = Hash::make($data['password']);
        } else {
            unset($data['password']);
        }

        $user ->update($data);
        return response()->json([
        'message'  => 'User updated',  
        'user'=>$user
        ]);
    }

   //fshi 1 user
    public function destroy(User $user)
    {
        if($user->role =='admin'){
            return response()->json([
                'message' =>'U cant delete a user'
            ] , 403 );
        }
        $user ->delete();
            return response()->json([
                'mesage'=>'User deleted'
            ],204);

    }
}
