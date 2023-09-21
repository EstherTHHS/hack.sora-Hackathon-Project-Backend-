<?php

namespace App\Http\Controllers\API;

use Exception;
use Throwable;
use App\Models\User;
use Illuminate\Http\Request;
use App\Services\AuthService;
use App\Http\Requests\LoginRequest;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use App\Http\Requests\RegisterRequest;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\ValidationException;



class AuthController extends Controller
{
    private AuthService $authService;

    // protected $authService;

    /**
     * @param AuthService $service
     */


    public function __construct(AuthService $authService)
    {
        $this->authService = $authService;
    }



    public function login(LoginRequest $request)
    {
        try {
            $startTime = microtime(true);
            $user = User::where('email', $request->email)->first();

            if (Auth::attempt(['email' => $request->email, 'password' => $request->password, 'provider'== null,'key'==null])   ) {
                $user = Auth::user();
                $success['token'] =  $user->createToken('User API')->plainTextToken;

                $success['id'] = $user->id;
                $success['name'] =  $user->name;
                $success['email'] =  $user->email;
                $userRoles = $user->getRoleNames();
                $success['role'] = $userRoles->first();
                $success['permission'] = $user->getPermissionsViaRoles()->pluck('name');
                return response()->success($request, $success, 'User Login Successfully', 200, $startTime, 1);
            } else {
                Log::channel('sora_error_log')->error('Login Error' . "Email & Password does not match  with our record.");

                return response()->error($request, null, 'Email & Password does not match with our record.', 401, $startTime);
            }
        } catch (Exception $e) {
            Log::channel('sora_error_log')->error('Login Error' . $e->getMessage());

            return response()->error($request, null, $e->getMessage(), 500, $startTime);
        }
    }




    public function logout(Request $request)
    {
        $startTime = microtime(true);
        $request->user()->currentAccessToken()->delete();
        return response()->success($request,[], 'Log out Successfully', 200, $startTime, 1);


    }
}
