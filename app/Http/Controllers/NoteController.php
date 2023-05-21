<?php

namespace App\Http\Controllers;

use App\Models\Note;
use Illuminate\Http\Request;

class NoteController extends Controller
{
    public function index()
    {
        $notes = Note::all();

        if(!$request->user()){
            $message = array( "message" => "Unauthorized");
            $error = Error::create(["errors" => $message]);
            
            return response()->json(['user' => $request->user()], 401)->header('Content-Type', "application/json");
        }

        return response()->json(['message' => "Déconnecté.", "notes" => $notes], 200)->header('Content-Type', "application/json");
    }

    public function create(Request $request)
    {
        if(!$request->user()){
            $message = array( "message" => "Unauthorized");
            $error = Error::create(["errors" => $message]);
            
            return response()->json(['user' => $request->user()], 401)->header('Content-Type', "application/json");
        }

        $validated = $request->validate([
            'content' => 'required',
            'user_id' => 'required',
        ]);

        if(!$validated){
            $message = array( "message" => "Messages d'erreurs de validation");
            $error = Error::create(["errors" => $message]);
            
            return response()->json(['user' => $request->user()], 422)->header('Content-Type', "application/json");
        }

        $note = Note::create($validated);

        return response()->json(['message' => "Created.", "note" => $note], 201)->header('Content-Type', "application/json");
    }

    public function show($id)
    {
        if(!$request->user()){
            $message = array( "message" => "Accès à la note non autorisé");
            $error = Error::create(["errors" => $message]);
            
            return response()->json(['user' => $request->user()], 403)->header('Content-Type', "application/json");
        }

        $note = Note::find($id);

        if (!$note) {
            $message = array( "message" => "La note n'existe pas");
            $error = Error::create(["errors" => $message]);
            
            return response()->json(['user' => $request->user()], 404)->header('Content-Type', "application/json");
        }

        return response()->json(['user' => $request->user()], 200)->header('Content-Type', "application/json");
    }

    public function update(Request $request, $id)
    {

        if(!$request->user()){
            $message = array( "message" => "Unauthorized");
            $error = Error::create(["errors" => $message]);
            
            return response()->json(['user' => $request->user()], 401)->header('Content-Type', "application/json");
        }

        $note = Note::find($id);

        if (!$note) {
            $message = array( "message" => "La note n'existe pas");
            $error = Error::create(["errors" => $message]);
            
            return response()->json(['user' => $request->user()], 404)->header('Content-Type', "application/json");
        }

        $validatedData = $request->validate([
            'content' => 'required',
            'user_id' => 'required',
        ]);

        $note->update($validatedData);
        return response()->json(['user' => $request->user()], 200)->header('Content-Type', "application/json");
    }

    public function destroy($id)
    {
        if(!$request->user()){
            $message = array( "message" => "Unauthorized");
            $error = Error::create(["errors" => $message]);
            
            return response()->json(['user' => $request->user()], 401)->header('Content-Type', "application/json");
        }

        $note = Note::find($id);

        if (!$note) {
            $message = array( "message" => "La note n'existe pas");
            $error = Error::create(["errors" => $message]);
            
            return response()->json(['user' => $request->user()], 404)->header('Content-Type', "application/json");
        }

        $note->delete();
        return response()->json(['user' => $request->user()], 200)->header('Content-Type', "application/json");
    }
}
