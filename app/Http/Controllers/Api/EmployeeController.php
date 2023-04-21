<?php

namespace App\Http\Controllers\Api;

use App\Events\EmployeeConfirmed;
use App\Events\EmployeeInvitationAccepted;
use App\Events\EmployeeProfileConfirmed;
use App\Events\InvitationClicked;
use App\Http\Controllers\Controller;
use App\Models\Invitation;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Str;

class EmployeeController extends Controller
{
    public function login(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'token' => 'required',
        ]);
        // dd($validator);
        $invitation = Invitation::where('token', $request->token)->first();
     
        if (! $invitation) {
            return response()->json([
                "errors" => "this link is exipred"
            ], 422);
        }

     
        return response()->json([
            "success" => "confirm your account"
        ], 200);
    }

    public function validateInvitation(Request $request, $token)
    {

        $invitation = Invitation::where('token', $token)->Where("status", "pending")->OrWhere("status", "confirmed")->first();
        
        if (! $invitation) {
            return response()->json([
                "errors" => "this link is exipred"
            ], 422);
        }
        
        event(new EmployeeInvitationAccepted($invitation, Carbon::now()));
     
        return response()->json([
            "success" => "confirm your account",
            "token" => $token,
            "email" => $invitation->email
        ], 200);
    }

    public function register(Request $request) 
    {
        $validator = Validator::make($request->all(), [
            'name' => 'required|string|between:4,100',
            'email' => 'required|string|email|max:100|unique:users',
            'address' => 'required|string',
            'phone' => 'required|string',
            'date_of_birth' => 'required|date',
            'password' => 'required|confirmed|min:8'
        ]);

        $validatedData = $validator->validated();

        if($validator->fails()){
             return response()->json([
                "errors" => $validator->errors()
            ], 400);
        }
        DB::beginTransaction();

        try {
            $employee = new User([
                'name' => $validatedData['name'],
                'email' => $validatedData['email'],
                'address' => $validatedData['address'],
                'phone' => $validatedData['phone'],
                'date_of_birth' => $validatedData['date_of_birth'],
                'password' => Hash::make($validatedData['password']),
            ]);
            $employee->save();
            event(new EmployeeConfirmed($employee, now()));
            DB::commit();
            return response()->json([
                "success" => "Your Acoount is Confirmed"
            ], 200);
        } catch (\Exception $e) {
            DB::rollback();

            return response()->json([
                "errors" => $e
            ], 422);
        }
    }

    public function checkpassword(Request $request) 
    {
        $validator = Validator::make($request->all(), [
            'password' => 'required|confirmed|min:8'
        ]);


        if($validator->fails()){
             return response()->json([
                "errors" => $validator->errors()
             ], 422);
        }

        try {

            return response()->json([
                "success" => "next"
            ], 200);
        } catch (\Exception $e) {

            return response()->json([
                "error" => "password dosn't match",
            ], 422);
        }
    }

}
