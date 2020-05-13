<?php

namespace App\Http\Controllers;

use App\User;
use Illuminate\Http\Request;

class AuthController extends Controller
{
    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware('auth', ['except' => ['register']]);
    }

    /**
     * Register a user
     *
     * @param Request $request
     * @return Response
     */
    public function register(Request $request)
    {
        // validate the incoming request
        $this->validate($request, [
            'last_name' => 'required|string',
            'first_name' => 'required|string',
            'email' => 'required|email|unique:users',
            'password' => 'required|confirmed',
        ]);

        try {
            $user = new User();
            $user->last_name = $request->input('last_name');
            $user->first_name = $request->input('first_name');
            $user->email = $request->input('email');
            $user->is_patient = $request->input('is_patient', true);
            $user->is_specialist = $request->input('is_specialist', !$user->is_patient);
            $user->is_guest = $request->input('is_guest', true);
            $user->password = $request->input('password'); //app('hash')->make($request->input('password'));
            $user->save();


            // send mail notification
            $appName = env('APP_NAME', 'Mh-87');
            @mail($user->email, "[$appName] Welcome on-board!", "Your account has been created on [$appName]. Pls verify with the link sent to the email you provided.", []);

            return response()->json(['status' => true, 'data' => $user, 'message' => 'Profile created successfully'], 201);
        } catch (\Exception $e) {
            return response()->json(['status' => false, 'data' => [], 'message' => 'Profile registration failed - ' . $e->getMessage()], 409);
        }
    }
}
