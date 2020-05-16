<?php

namespace App\Http\Controllers;

use App\User;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use Illuminate\Validation\ValidationException;

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
        try {
            // validate the incoming request
            $this->validate($request, [
                'last_name' => 'required|string',
                'first_name' => 'required|string',
                'gender' => 'required|in:male,female',
                'email' => 'required|email|unique:users',
                // 'marital_status' => 'required|in:single,married,divorced,complicated',
                'password' => 'required|confirmed',
                'is_patient' => 'required|boolean',
            ], [
                // 'in' => 'The :attribute must be one of the following: :values',
                'is_patient.required' => "Kindly specify if you're a (prospective) patient"
            ]);


            $user = new User();
            $user->last_name = $request->input('last_name');
            $user->first_name = $request->input('first_name');
            $user->gender = $request->input('gender');
            $user->birth_date = $request->input('birth_date');
            $user->email = $request->input('email');
            $user->marital_status = $request->input('marital_status', 'single');
            $user->is_patient = $request->input('is_patient', true);
            $user->is_specialist = !$user->is_patient;
            $user->password = $request->input('password'); //app('hash')->make($request->input('password'));
            $user->profile_code = hash('sha512', $user->email);
            $user->save();


            // send mail notification
            $appName = env('APP_NAME', 'Mh-87');
            $verificationUrl = 'http://' . env('APP_FRONTEND_URL') . '/auth/verify?code=' . $user->profile_code . '&email=' . $user->email;
            @mail($user->email, "[$appName] Welcome on-board!", "Your account has been created on [$appName]. Pls verify with the link provided below.\n\n{$verificationUrl}", []);

            return response()->json(['status' => true, 'message' => 'Profile created successfully', 'data' => $user], 201);
        } catch (ValidationException $e) {
            return response()->json(['status' => false, 'message' => $e->getMessage(), 'errors' => $e->errors()], 400);
        } catch (\Exception $e) {
            return response()->json(['status' => false, 'message' => 'Profile registration failed - ' . $e->getMessage(), 'data' => []], 409);
        }
    }
}
