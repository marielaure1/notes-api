<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use App\Models\User;
// use App\Models\Error;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;


class AuthController extends Controller
{
    public function login(Request $request)
    {

        $validator = Validator::make($request->all(), [
            'email' => 'required|email',
            'password' => 'required'
        ]);
    
        if ($validator->fails()) {
            return response()->json([
                'errors' => $validator->errors()
            ], 401);
        }  

        if(!Auth::attempt($request->only(['email', 'password']))){
            $message = "Identifiants incorrect";
            // $error = Error::create(["errors" => $message]);

            return response()->json(["errors" => $message], 422)->header('Content-Type', "application/json");
        }

        $user = User::where('email', $request->email)->first();
              
        $token = $user->createToken($request->email)->plainTextToken;

        return response()->json(['token' => $token, 'user' => $user], 200)->header('Content-Type', "application/json");
    }

    public function logout(Request $request){

        if(!$request->user()->currentAccessToken()){
            return response()->json(['message' => "Unauthenticated."], 401);
        }
        
        $request->user()->currentAccessToken()->delete();
        return response()->json(['message' => "Déconnecté."], 204)->header('Content-Type', "application/json");
    }

    public function register(Request $request){

            // Valider les données
            $validator = Validator::make($request->all(), [
                'name' => 'required',
                'email' => 'required|email|unique:users,email',
                'password' => 'required|min:8'
            ]);
        
            if ($validator->fails()) {
                return response()->json([
                    'errors' => $validator->errors()
                ], 422);
            }

            $user = User::create([
                'name' => $request->name,
                'email' => $request->email,
                'password' => Hash::make($request->password)
            ]);

            $token = $user->createToken("API TOKEN")->plainTextToken;

            $user->remember_token = $token;

            $user->save();

           

            return response()->json([
                'token' => $token,
                'user' => $user
            ], 201);
    }

    public function user(Request $request){
        if(!$request->user()){
            
            return response()->json(['user' => "Unauthenticated"], 401)->header('Content-Type', "application/json");
        }
        return response()->json(['user' => $request->user()], 200)->header('Content-Type', "application/json");
    }

}
