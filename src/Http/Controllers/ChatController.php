<?php

namespace App\Http\Controllers;

use App\Appointment;
use App\Chat;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Http\Request;

/**
 * Chat Controller
 * 
 * @author Emma NWAMAIFE <emadimabua@gmail.com>
 */
class ChatController extends Controller
{
    public function add(Request $request, $id)
    {
        $data = $this->validate($request, ['message' => 'required|string|max:160']);

        try {
            $userId = auth()->user()->id;
            $appointment = Appointment::where('specialist_id', $userId)->orWhere('patient_id', $userId)->findOrFail($id);

            $chat = new Chat($data);
            $chat->appointment_id = $appointment->id;
            $chat->user_id = $userId;
            $chat->save();

            return response()->json([
                'status' => true, 'message' => 'Chat saved successfully', 'data' => $chat
            ], 201);
        } catch (\Exception | ModelNotFoundException $e) {
            return response()->json([
                'status' => false, 'message' => 'Chat could not be saved', 'errors' => ['error' => $e->getMessage()]
            ], ($e instanceof ModelNotFoundException ? 404 : 400));
        }
    }
}
