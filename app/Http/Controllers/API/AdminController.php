<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Admin;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Mail;

class AdminController extends Controller
{
    public function create(Request $request)
{
    if ($request->isMethod('post')) {
        $data = $request->input();

        $validator = Validator::make($data, [
            'name' => 'required|string',
            'email' => 'required|email|unique:admins,email',
            'mobile' => 'required|string|unique:admins,mobile',
            'password' => 'required|min:8',
            'address' => 'nullable|string',
            'city' => 'nullable|string',
            'postal_code' => 'nullable|string',
            'status' => 'integer|in:0,1', // Assuming status should be 0 or 1
            // Add other validation rules as needed
        ]);

        if ($validator->fails()) {
            return response()->json([
                'status' => false,
                'code' => 400,
                'message' => 'Validation failed',
                'errors' => $validator->errors()
            ]);
        }

        $admin = new Admin;
        $admin->name = $data['name'];
        $admin->email = $data['email'];
        $admin->mobile = $data['mobile'];
        $admin->password = bcrypt($data['password']);
        $admin->address = $data['address'] ?? null;
        $admin->city = $data['city'] ?? null;
        $admin->postal_code = $data['postal_code'] ?? null;
        $admin->status = $data['status'] ?? 0;

        $admin->save();

        return response()->json([
            'status' => true,
            'code' => 201,
            'message' => 'Admin created successfully',
            'data' => $admin
        ]);
    } else {
        return response()->json([
            'status' => false,
            'code' => 400,
            'message' => 'Invalid request method'
        ]);
    }
}

public function authenticate(Request $request)
{
    if ($request->isMethod('post')) {
        $data = $request->input();

        $adminDetail = Admin::where('email', '=', $data['email'])->first();

        $rules = [
            "email" => "required|email|exists:admins,email",
            "password" => "required|min:8",
        ];

        $customMessage = [
            "email.required" => "Email is required",
            "email.exists" => "Email does not exist",
            "password.required" => "Password is required",
            "password.min" => "Password must be at least 8 characters long",
        ];

        $validator = Validator::make($data, $rules, $customMessage);

        if ($validator->fails()) {
            return response()->json($validator->errors(), 422);
        }

        if ($adminDetail && password_verify($data['password'], optional($adminDetail)->password)) {
            return response()->json([
                'adminDetail' => $adminDetail,
                'status' => true,
                'code' => 200,
                'message' => 'Login successful',
                'data' => $adminDetail
            ]);
        } else {
            return response()->json([
                'status' => false,
                'code' => 400,
                'message' => 'Invalid email or password'
            ]);
        }
    }
}

    public function updateUser(Request $request)
{
    if ($request->isMethod('post')) {
        $data = $request->input();

        $rules = [
            "name" => "required",
            "email" => "email|unique:admins,email," . $data['id'],
            "mobile" => "string|unique:admins,mobile," . $data['id'],
            "password" => "min:8",
        ];

        $customMessage = [
            "name.required" => "Name is required",
            "email.email" => "Invalid email format",
            "email.unique" => "Email is already taken",
            "mobile.string" => "Invalid mobile format",
            "mobile.unique" => "Mobile is already taken",
            "password.min" => "Password must be at least 8 characters long",
        ];

        $validator = Validator::make($data, $rules, $customMessage);

        if ($validator->fails()) {
            return response()->json($validator->errors(), 422);
        }

        $adminDetail = Admin::find($data['id']);

        if (!$adminDetail) {
            return response()->json([
                'status' => false,
                'code' => 404,
                'message' => 'Admin not found'
            ]);
        }

        $adminDetail->fill($data); // Assign all attributes at once

        if (isset($data['password'])) {
            $adminDetail->password = bcrypt($data['password']);
        }

        $adminDetail->save();

        return response()->json([
            'adminDetail' => $adminDetail,
            'status' => true,
            'code' => 200,
            'message' => 'Admin updated successfully',
            'data' => $adminDetail
        ]);
    } else {
        return response()->json([
            'status' => false,
            'code' => 400,
            'message' => 'Invalid request method'
        ]);
    }
}

    public function sendResetEmail(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'email' => 'required|email|exists:admins,email',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'status' => false,
                'code' => 400,
                'message' => 'Validation failed',
                'errors' => $validator->errors()
            ]);
        }

        $admin = Admin::where('email', $request->email)->first();

        $resetToken = Str::random(32);
        $hashedToken = hash('sha256', $resetToken);
        $admin->update(['reset_token' => $hashedToken]);

        $resetLink = url("/admin-reset-password/{$resetToken}");
        Mail::to($admin->email)->send(new \App\Mail\ResetPasswordMail($resetLink));

        return response()->json([
            'status' => true,
            'code' => 200,
            'message' => 'Reset email sent successfully',
        ]);
    }
}
