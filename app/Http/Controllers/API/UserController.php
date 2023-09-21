<?php

namespace App\Http\Controllers\API;

use Exception;
use App\Models\User;
use Illuminate\Http\Request;
use App\Services\UserService;
use Illuminate\Support\Facades\Log;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use App\Http\Requests\RegisterRequest;
use App\Http\Requests\SocialLoginRequest;

class UserController extends Controller
{

    protected UserService $service;

    public function __construct(UserService $service)
    {
        $this->service = $service;
    }
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        //
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(RegisterRequest $request)
    {

        try {

            $startTime = microtime(true);

            $validatedData = $request->validated();

            $data = $this->service->storeUser($validatedData);

            return response()->success($request, $data, 'User Register Successfully.', 201, $startTime, 1);
        } catch (Exception $e) {
            Log::channel('sora_error_log')->error('Register Error' . $e->getMessage());
            return response()->error($request, null, $e->getMessage(), 500, $startTime);
        }
    }

    // public function socialLogin(Request $request)
    // {


    //     try {
    //         $startTime = microtime(true);
    //         $user = User::where('provider', $request->provider)->where('key', $request->key)->first();

    //         if($user=null){
    //             $data=$this->service->socialLogin($request);
    //             $success['token'] = $user->createToken('User API')->plainTextToken;
    //             $success['user'] = $data;
    //             return response()->success($request, $success, 'User Login Successfully', 200, $startTime, 1);
    //         }
    //         if (Auth::attempt(['provider' => $request->provider, 'key' => $request->key])) {
    //             $user = Auth::user();
    //             $success['token'] =  $user->createToken('User API')->plainTextToken;

    //             return response()->success($request, $success, 'User Login and Create Successfully', 200, $startTime, 1);
    //         }


    //     } catch (Exception $e) {
    //         Log::channel('sora_error_log')->error('Login Error' . $e->getMessage());

    //         return response()->error($request, null, $e->getMessage(), 500, $startTime);
    //     }
    // }



    public function socialLogin(SocialLoginRequest $request)
    {


        $startTime = microtime(true);
        $user = User::where('provider', $request->provider)->where('key', $request->key)->first();
        try {
            if ($user == null) {
                $validatedData = $request->validated();
                $data = $this->service->socialLogin($validatedData);

                Auth::login($user);
                $success['token'] = $user->createToken('User API')->plainTextToken;
                return response()->success($request, $success, 'User  Create Successfully', 200, $startTime, 1);
            } else {
                Auth::login($user);
                $success['token'] = $user->createToken('User API')->plainTextToken;
                return response()->success($request, $success, 'User Login and Create Successfully', 200, $startTime, 1);
            }
        } catch (Exception $e) {
            Log::channel('sora_error_log')->error('Register Error' . $e->getMessage());
            return response()->error($request, null, $e->getMessage(), 500, $startTime);
        }
    }


    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        //
    }
}
