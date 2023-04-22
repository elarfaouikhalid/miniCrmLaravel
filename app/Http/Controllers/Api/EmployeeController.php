<?php

namespace App\Http\Controllers\Api;

use App\Events\EmployeeConfirmed;
use App\Events\EmployeeInvitationAccepted;
use App\Http\Controllers\Controller;
use App\Models\Invitation;
use App\Repositories\EmployeeRepositoryInterface;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class EmployeeController extends Controller
{
    protected $employeeRepository;

    public function __construct(EmployeeRepositoryInterface $employeeRepository)
    {
        $this->employeeRepository = $employeeRepository;
    }

    public function validateInvitation(Request $request, $token)
    {
        $invitation = Invitation::where('token', $token)
            ->whereIn('status', ['pending', 'confirmed'])
            ->first();

        if (!$invitation) {
            return response()->json([
                "errors" => "This link is expired"
            ], 422);
        }

        event(new EmployeeInvitationAccepted($invitation, Carbon::now()));

        return response()->json([
            "success" => "Confirm your account",
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

        if ($validator->fails()) {
            return response()->json([
                "errors" => $validator->errors()
            ], 400);
        }

        try {
            $employee = $this->employeeRepository->create($validator->validated());

            event(new EmployeeConfirmed($employee, now()));

            return response()->json([
                "success" => "Your account is confirmed"
            ], 200);
        } catch (\Exception $e) {
            return response()->json([
                "errors" => $e->getMessage()
            ], 422) ;
        }
    }

}
