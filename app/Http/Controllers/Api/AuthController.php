<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Repositories\UserRepositoryInterface;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;

class AuthController extends Controller
{
    protected $userRepository;

    public function __construct(UserRepositoryInterface $userRepository)
    {
        $this->userRepository = $userRepository;
    }

    // CHECK user is existe or not
    public function login(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'email' => 'required|email',
            'password' => 'required',
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }

        $email = $request->email;

        $user = $this->userRepository->searchByEmail($email, true)->first() ?? $this->userRepository->searchByEmail($email)->first();

        if (!$user || !Hash::check($request->password, $user->password)) {
            return response()->json(['errors' => 'The provided credentials are incorrect.'], 422);
        }
        
        return response()->json([
            "token" => $user->createToken("api_token")->plainTextToken,
            "user" => $user
        ], 200);
    }
    //get authenticate user data
    public function userProfile() {
        return response()->json(auth()->user());
    }
    // log out from apps 
    public function logout() {
        Auth::user()->tokens()->delete();
        Auth::guard('web')->logout();
        return response()->json(['message' => 'User successfully signed out']);
    }

    public function getLogs()
    {
        $logs = [];
        $logFile = storage_path('logs/activity.log');
        
        if (file_exists($logFile)) {
            $logContents = file_get_contents($logFile);
            preg_match_all('/(?<=local\.INFO: ).*/', $logContents, $matches);
            $logs = $matches[0];
    
            // Remove any backslashes and surrounding whitespace in the logs
            $logs = array_map(function($log) {
                return preg_replace('/\s*\\\\+\s*|"/', '', $log);
            }, $logs);
        }
        
        return response()->json(['logs' => $logs]);
    }
    
}
