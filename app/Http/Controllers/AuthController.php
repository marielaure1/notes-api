<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use App\Models\User;
use App\Models\Error;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;


class AuthController extends Controller
{
    public function login(Request $request){

        $validated = $request->validate([
            'email' => 'required|email',
            'password' => 'required',
        ]);

        if(!$validated){
            $message = array( "message" => "Cet identifiant est inconnu");
            $error = Error::create(["errors" => $message]);

            return response()->json(['message' => 'Cet identifiant est inconnu'], 401)->header('Content-Type', "application/json");
        }

        $user = User::where('email', $request->email)->first();

        if (!Hash::check($request->password, $user->password)) {
            $message = array( "message" => "Messages d'erreurs de validation");

            $error = Error::create(["errors" => $message]);
            return response()->json(['message' => "Messages d'erreurs de validation"], 422)->header('Content-Type', "application/json");
        }
              
        $token = $user->createToken($request->email)->plainTextToken;

        return response()->json(['token' => $token], 200)->header('Content-Type', "application/json");
    }

    public function logout(Request $request){

        if(!$request->user()->currentAccessToken()){
            return response()->json(['message' => "Unauthenticated."], 401)->header('Content-Type', "application/json");
        }
        $request->user()->currentAccessToken()->delete();
        return response()->json(['message' => "Déconnecté."], 204)->header('Content-Type', "application/json");
    }

    public function register(Request $request){
        $validated = $request->validate([
            'name' => 'required',
            'email' => 'required|email|unique:users',
            'password' => 'required|min:8',
        ]);

        if(!$validated){
            $message = array( "message" => "Veuillez remplir tous les champs.");
            $error = Error::create(["errors" => $message]);

            return response()->json(['message' => 'Veuillez remplir tous les champs.'], 422)->header('Content-Type', "application/json");
        }

        $validated['password'] = Hash::make($request->password);

        $user = User::create($validated);
        $token = $user->createToken($request->email)->plainTextToken;

        return response()->json(['token' => $token], 201)->header('Content-Type', "application/json");
    }

    public function user(Request $request){
        if(!$request->user()){
            $message = array( "message" => "Unauthorized");
            $error = Error::create(["errors" => $message]);
            
            return response()->json(['user' => $request->user()], 401)->header('Content-Type', "application/json");
        }
        return response()->json(['user' => $request->user()], 200)->header('Content-Type', "application/json");
    }

}
