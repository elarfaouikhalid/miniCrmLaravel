<?php

namespace App\Http\Controllers\Api;

use App\Events\InvitationSent;
use App\Events\TrackProcessed;
use App\Http\Controllers\Controller;
use App\Models\Company;
use App\Models\Invitation;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Str;

class AdminstratorController extends Controller
{
    public function CreateCompany(Request $request) {
            $validator = Validator::make($request->all(), [
                'name' => 'required',
            ]);
    
            $validatedData = $validator->validated();
    
            if($validator->fails()) {
                return response()->json([
                    "error" => "Please fill out all data correctly"
                ], 422);
            }
            DB::beginTransaction();
    
            try {
                $company = new Company([
                    'name' => $validatedData['name']
                ]);
                $company->save();
    
                DB::commit();
                return response()->json([
                    "success" => "Company created successfully"
                ], 200);
            } catch (\Exception $e) {
                DB::rollback();
    
                return response()->json([
                    "error" => "Failed to create company",
                ], 422);
            }
    }

    public function DeleteCompany(Request $request, $id) {
        $company = Company::findOrFail($id);
        $company->delete();
        return response()->json([
            "success" => "Company deleted successfully"
        ]);
    }

    public function CreateAnotherAdmin(Request $request) {
        $validator = Validator::make($request->all(), [
            'name' => 'required|string|between:4,100',
            'email' => 'required|string|email|max:100|unique:users',
            'password' => 'required|confirmed|min:8'
        ]);

        $validatedData = $validator->validated();

        if($validator->fails()){
             return response()->json($validator->errors(), 400);
        }
        
        DB::beginTransaction();

        try {
            $Adminstrator = new User([
                'name' => $validatedData['name'],
                'email' => $validatedData['email'],
                'password' => Hash::make($validatedData['password']),
            ]);
            $Adminstrator->save();

            DB::commit();
            return response()->json([
                "success" => "Adminstrator created successfully"
            ], 200);
        } catch (\Exception $e) {
            DB::rollback();

            return response()->json([
                "error" => "Failed to create Adminstrator",
            ], 422);
        }
    }

    public function sendInvitation(Request $request)
    {
        // Validate input
        $validator = Validator::make($request->all(), [
            'name' => 'required|string|between:4,100',
            'email' => 'required|email',
        ]);
        DB::beginTransaction();
        $token = Str::random(40);

        // Create invitation record in the database
        try {
            $invitation = new Invitation([
                'name' => $request->name,
                'company_id' => $request->company_id,
                'invited_by' => $request->invited_by,
                'email' => $request->email,
                'token' => $token
            ]);
            $invitation->save();
            $companyName = Company::findOrFail($request->company_id);
            $adminId = User::findOrFail($request->invited_by);
            // Send invitation email
            event(new TrackProcessed($invitation, now(), $companyName->name, $adminId->name));
            DB::commit();
            return response()->json([
                "success" => "Invitation sent successfully!"
            ], 200);
        } catch (\Exception $e) {
            DB::rollback();

            return response()->json([
                "error" => $e->getMessage(),
            ], 422);
        }
    }

    public function ControlStatusEmployee(Request $request, $id) 
    {
        $product = User::findOrFail($id);
        DB::beginTransaction();
    }

    public function getAllCompanies(Request $request) {
        $companies = Company::paginate(10);
        $lastPage = $companies->lastPage();

        return response()->json([
            "companies" => $companies,
            "lastPage" => $lastPage
        ]);
    }

    public function getAllEmployeeWithPendingStatus(Request $request) {
        $invitations = Invitation::where("status", "=", "pending")->paginate(10);
        $lastPage = $invitations->lastPage();

        return response()->json([
            "invitations" => $invitations,
            "lastPage" => $lastPage
        ]);
    }

    public function updateInvitationStatus(Request $request, $id)
    {
        try {
            DB::beginTransaction();
            $invitation = Invitation::find($id);
            if ($invitation) {
                $invitation->status = "cancelled";
                $invitation->token = null;
                $invitation->save();
                DB::commit();
                return response()->json(['message' => 'Invitation status cancelled successfully.']);
            } else {
                return response()->json(['message' => 'Invitation not found.'], 404);
            }
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json([
                'message' => 'An error occurred while cancelling the invitation status.'
            ], 500);
        }
    }
}
