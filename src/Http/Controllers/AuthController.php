<?php

namespace App\Http\Controllers;

use App\Events\UserRegisteredEvent;
use App\User;
use Illuminate\Http\Request;
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
        $this->middleware('auth', ['only' => ['signout', 'me', 'refresh']]);
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
                'password' => 'required|confirmed|min:8',
                'is_patient' => 'required|boolean',
            ], [
                // 'in' => 'The :attribute must be one of the following: :values',
                'is_patient.required' => "Kindly specify if you're a (prospective) patient"
            ]);


            $user = new User();
            $user->last_name = $request->post('last_name');
            $user->first_name = $request->post('first_name');
            $user->gender = $request->post('gender');
            $user->birth_date = $request->post('birth_date');
            $user->email = $request->post('email');
            $user->marital_status = $request->post('marital_status', 'single');
            $user->is_patient = $request->post('is_patient', true);
            $user->is_specialist = !$user->is_patient;
            $user->password = $request->post('password'); //app('hash')->make($request->input('password'));
            $user->profile_code = hash('sha512', $user->email);
            $user->save();


            // send mail notification
            event(new UserRegisteredEvent($user));

            return response()->json([
                'status' => true, 'message' => 'Profile created successfully', 'data' => $user
            ], 201);
        } catch (ValidationException $e) {
            return response()->json([
                'status' => false, 'message' => $e->getMessage(), 'errors' => $e->errors()
            ], 400);
        } catch (\Exception $e) {
            return response()->json([
                'status' => false, 'message' => 'Profile creation failed', 'errors' => ['error' => $e->getMessage()]
            ], 409);
        }
    }

    /**
     * Verify user account
     *
     * @param Request $request
     * @return Response
     */
    public function verify(Request $request)
    {
        $user = User::where(['profile_code' => $request->query('code')])->first();
        if (empty($user)) {
            return response()->json([
                'status' => false, 'message' => 'User verification failed',
                'errors' => ['code' => 'Verification code is invalid']
            ], 400);
        }

        if ($user->email !== $request->query('email')) {
            return response()->json([
                'status' => false, 'message' => 'User verification failed',
                'errors' => ['email' => 'User does not exist']
            ], 404);
        }

        if ($user->is_active) {
            return response()->json([
                'status' => false, 'message' => 'Invalid request',
                'errors' => ['user' => 'User has already been verified']
            ], 400);
        }

        $user->is_active = true;
        $user->is_guest = false;
        $user->profiled_at = date('Y-m-d h:i:s');
        $user->save();

        return response()->json([
            'status' => true, 'message' => 'User verification successful', 'data' => $user
        ]);
    }

    /**
     * Get a JWT via given credentials.
     *
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function signin(Request $request)
    {
        try {
            $this->validate($request, ['email' => 'required|email|exists:users', 'password' => 'required|min:8']);

            $credentials = array_merge($request->only(['email', 'password']), ['is_active' => true]);
            if (!($token = auth()->attempt($credentials))) {
                return response()->json([
                    'status' => false, 'message' => 'Authentication failed', 'errors' => ['error' => 'Invalid email-address/password']
                ], 401);
            }

            return $this->respondWithToken($token);
        } catch (ValidationException $e) {
            return response()->json([
                'status' => false, 'message' => 'Authentication failed', 'errors' => $e->errors()
            ], 400);
        } catch (\Exception $e) {
            return response()->json([
                'status' => false, 'message' => 'Authentication failed', 'errors' => ['error' => $e->getMessage()]
            ], 400);
        }
    }

    /**
     * Get the authenticated User.
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function me()
    {
        return response()->json(['status' => true, 'data' => auth()->user()]);
    }

    /**
     * Log the user out (Invalidate the token).
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function signout()
    {
        auth()->guard()->logout();
        return response()->json(['status' => true, 'message' => 'Signed out successfully']);
    }

    /**
     * Refresh a token.
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function refresh()
    {
        return $this->respondWithToken(auth()->guard()->refresh());
    }

    /**
     * Get the token array structure.
     *
     * @param  string $token
     * @return \Illuminate\Http\JsonResponse
     */
    protected function respondWithToken($token)
    {
        return response()->json([
            'status' => true, 'data' => auth()->user(), 'access_token' => $token, 'token_type' => 'bearer',
            'expires_in' => auth()->guard()->factory()->getTTL() * 60
        ]);
    }
}
