<?php

namespace App\Http\Controllers;

use App\User;

/**
 * User Controller
 * 
 * @author Emma NWAMAIFE <emadimabua@gmail.com>
 */
class UserController extends Controller
{
    public function fetch()
    {
        if (!auth()->user()->is_admin) {
            return response()->json([
                'status' => false, 'message' => 'User(s) could not be fetched',
                'errors' => ['error' => "You don't have permission to do use this feature"]
            ], 401);
        }

        try {
            return response()->json(['status' => true, 'data' => User::all()]);
        } catch (\Exception $e) {
            return response()->json([
                'status' => false, 'message' => 'User(s) could not be fetched', 'errors' => ['error' => $e->getMessage()]
            ],  400);
        }
    }
}
