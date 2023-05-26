<?php

namespace App\Http\Controllers;

use App\Models\Note;
use App\Models\User;
use Illuminate\Http\Request;

use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;

class NoteController extends Controller
{
    public function index(Request $request)
    {
        if(!$request->user()){
            return response()->json(['message' => "Unauthorized."], 401);
        }
        
        $notes = Note::where('user_id', $request->user()->id)->orderBy('created_at', 'desc')->get();

        // if(!$notes->count() > 0){
        //     return response()->json(["notes" => "Il n'y a aucune note"], 404)->header('Content-Type', "application/json");
        // }

        return response()->json(["notes" => $notes], 200)->header('Content-Type', "application/json");
    }

    public function create(Request $request)
    {
        if(!$request->user()->currentAccessToken()){
            return response()->json(['message' => "Unauthenticated."], 401);
        }

        $validator = Validator::make($request->all(), [
            'content' => 'required'
        ]);
    
        if ($validator->fails()) {
            return response()->json([
                'errors' => $validator->errors()
            ], 401);
        }  

        $note = Note::create([
            "content" => $request->content,
            "user_id" => $request->user()->id
        ]);

        return response()->json(["note" => $note], 201)->header('Content-Type', "application/json");
    }

    public function show(Request $request, $id)
    {
        if(!$request->user()->currentAccessToken()){
            return response()->json(['message' => "Unauthenticated."], 401);
        }

        $note = Note::find($id);

        if (!$note) {
            
            return response()->json(['user' => "La note n'existe pas."], 404)->header('Content-Type', "application/json");
        }

        if($note->user_id !== auth()->user()->id) {
            return response()->json(['message' => '"Accès à la note non autorisé'], 403);
        }

        return response()->json(['note' => $note], 200)->header('Content-Type', "application/json");
    }

    public function update(Request $request, $id)
    {
        
        if(!$request->user()->currentAccessToken()){
            return response()->json(['message' => "Unauthorized."], 401);
        }

        $validator = Validator::make($request->all(), [
            'content' => 'required'
        ]);
    
        if ($validator->fails()) {
            return response()->json([
                'errors' => $validator->errors()
            ], 401);
        }  

        $note = Note::find($id);

        if (!$note) {
            
            return response()->json(['note' => "La note n'existe pas"], 404)->header('Content-Type', "application/json");
        }

        if($note->user_id !== auth()->user()->id) {
            return response()->json(['message' => '"Accès à la note non autorisé'], 403);
        }

        $note->update(["content" => $request->content]);
        
        return response()->json(['note' => $note], 200)->header('Content-Type', "application/json");
    }

    public function delete(Request $request, $id)
    {
        if(!$request->user()->currentAccessToken()){
            return response()->json(['message' => "Unauthorized."], 401);
        }

        $note = Note::find($id);

        if (!$note) {
            
            return response()->json(['note' => "La note n'existe pas"], 404)->header('Content-Type', "application/json");
        }

        if($note->user_id !== auth()->user()->id) {
            return response()->json(['message' => '"Accès à la note non autorisé'], 403);
        }

        $note->delete();

        return response()->json(['user' => $request->user()], 200)->header('Content-Type', "application/json");
    }
}
