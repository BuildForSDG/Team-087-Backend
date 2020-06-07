<?php

namespace App\Http\Controllers;

use App\User;
use Illuminate\Database\Eloquent\ModelNotFoundException;
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
                'errors' => ['error' => "You cannot use this feature"]
            ], 401);
        }

        try {
            $filters = [];
            if ($request->has('active')) {
                $filters['is_active'] = (bool) ($request->query('active', 0));
            }

            if ($request->has('patient')) {
                $filters['is_patient'] = (bool) ($request->query('patient', 0));
            }

            if ($request->has('specialist')) {
                $filters['is_specialist'] = (bool) ($request->query('specialist', 0));
            }

            if ($request->has('admin')) {
                $filters['is_admin'] = (bool) ($request->query('admin', 0));
            }

            $perPage = $request->query('chunk', 10); //chunk-size of fetched-data
            return response()->json([
                'status' => true, 'data' => User::with(['patient', 'specialist'])->where($filters)->paginate($perPage)
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'status' => false, 'message' => 'User(s) could not be fetched', 'errors' => ['error' => $e->getMessage()]
            ], 400);
        }
    }

    public function view($id = 0)
    {
        try {
            $authUser = auth()->user();
            $viewedUser = User::findOrFail(empty($id) ? $authUser->id : $id);

            if ($authUser->id !== $viewedUser->id) {
                if (!$authUser->is_admin && $viewedUser->is_admin) {
                    throw new \Exception("You cannot view this profile");
                }

                if ($authUser->is_patient && $viewedUser->is_patient) {
                    throw new \Exception("You cannot view a patient as a patient");
                }
            }

            $attachment = [];
            if ($viewedUser->is_patient) {
                $attachment = [
                    'patient' => $viewedUser->patient,
                    'appointments' => empty($viewedUser->patient) ? null : $viewedUser->patient->appointments
                ];
            } else if ($viewedUser->is_specialist) {
                $attachment = [
                    'specialist' => $viewedUser->specialist,
                    'appointments' => empty($viewedUser->specialist) ? null : $viewedUser->specialist->appointments
                ];
            }

            $viewedUserArray = array_merge($viewedUser->toArray(), $attachment);
            return response()->json(['status' => true, 'data' => $viewedUserArray]);
        } catch (\Exception | ModelNotFoundException $e) {
            return response()->json([
                'status' => false, 'message' => 'User could not be retrieved',
                'errors' => ['error' => $e->getMessage()]
            ], ($e instanceof ModelNotFoundException ? 404 : 400));
        }
    }
}
