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
        if (!auth()->user()->is_admin && !($request->routeIs('users.recommend') && $request->input('byrr'))) {
            return response()->json([
                'status' => false, 'message' => 'User(s) could not be fetched',
                'errors' => ['error' => "You cannot use this feature"]
            ], 403);
        }

        try {
            $filters = [];
            if ($request->has('active')) {
                $filters['is_active'] = (bool) ($request->input('active', 0));
            }

            if ($request->has('patient')) {
                $filters['is_patient'] = (bool) ($request->input('patient', 0));
            }

            if ($request->has('specialist')) {
                $filters['is_specialist'] = (bool) ($request->input('specialist', 0));
            }

            if ($request->has('admin')) {
                $filters['is_admin'] = (bool) ($request->input('admin', 0));
            }

            $perPage = $request->query('chunk', 10); //chunk-size of fetched-data
            return response()->json([
                'status' => true, 'data' => User::with(['patient', 'specialist'])->where($filters)->orderBy('id')->paginate($perPage)
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'status' => false, 'message' => 'User(s) could not be fetched', 'errors' => ['error' => $e->getMessage()]
            ], 400);
        }
    }

    /**
     * Recommend a specialist to the current user [based on their location - W.I.P]
     * (should work like a recommendation-engine for mental-health specialists like Youtube e.t.c)
     * 
     * @see https://medium.com/@sirajul.anik/laravel-lumen-manipulating-route-controller-parameters-5f3cbcb521b4
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function recommend(Request $request)
    {
        return $this->fetch($request->merge([
            'specialist' => 1, 'active' => 1, 'patient' => 0, 'admin' => 0, 'byrr' => true // byrr => by-recommend-route
        ]));
    }

    public function view($id = 0)
    {
        try {
            $authUser = auth()->user();
            $viewedUser = User::findOrFail(empty($id) ? $authUser->id : $id);

            if ($authUser->id !== $viewedUser->id) {
                if (!$authUser->is_admin && $viewedUser->is_admin) {
                    abort(403, "You cannot view this profile");
                }

                if ($authUser->is_patient && $viewedUser->is_patient) {
                    abort(403, "You cannot view a patient as a patient");
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

    /**
     * Edit Personal Photo
     * 
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function editPhoto(Request $request)
    {
        try {
            $this->validate($request, ['photo_url' => 'required']);
            User::find(auth()->user()->id)->update(['photo' => $request->input('photo_url')]);

            return response()->json([
                'status' => true, 'message' => 'Photo updated successfully', 'data' => $request->input()
            ]);
        } catch (\Exception | ModelNotFoundException $e) {
            return response()->json([
                'status' => false, 'message' => "User could not be retrieved for photo-change",
                'errors' => ['error' => $e->getMessage()]
            ], ($e instanceof ModelNotFoundException ? 404 : 400));
        }
    }
}
