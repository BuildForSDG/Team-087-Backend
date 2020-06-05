<?php

namespace App\Http\Controllers;

use App\User;
use Illuminate\Http\Request;

/**
 * User Controller
 * 
 * @author Emma NWAMAIFE <emadimabua@gmail.com>
 */
class UserController extends Controller
{
    public function fetch(Request $request)
    {
        if (!auth()->user()->is_admin) {
            return response()->json([
                'status' => false, 'message' => 'User(s) could not be fetched',
                'errors' => ['error' => "You don't have permission to do use this feature"]
            ], 401);
        }

        try {
            $filters = [];
            if ($request->has('active')) {
                $filters['is_active'] = boolval($request->query('active', 0));
            }

            if ($request->has('patient')) {
                $filters['is_patient'] = boolval($request->query('patient', 0));
            }

            if ($request->has('specialist')) {
                $filters['is_specialist'] = boolval($request->query('specialist', 0));
            }

            if ($request->has('admin')) {
                $filters['is_admin'] = boolval($request->query('admin', 0));
            }

            $perPage = $request->query('chunk', 10); //chunk-size of fetched-data
            return response()->json([
                'status' => true, 'data' => User::with(['patient', 'specialist'])->where($filters)->paginate($perPage)
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'status' => false, 'message' => 'User(s) could not be fetched', 'errors' => ['error' => $e->getMessage()]
            ],  400);
        }
    }
}
