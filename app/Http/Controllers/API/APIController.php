<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\User;
use Illuminate\Support\Facades\Validator;

class APIController extends Controller
{
    public function create(Request $request)
{
    if ($request->isMethod('post')) {
        $data = $request->input();

        $validator = Validator::make($data, [
            'name' => 'required|string',
            'email' => 'required|email|unique:users,email',
            'mobile' => 'required|string|unique:users,mobile',
            'password' => 'required|min:8',
            'address' => 'nullable|string',
            'city' => 'nullable|string',
            'postal_code' => 'nullable|string',
            'status' => 'integer|in:0,1',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'status' => false,
                'code' => 400,
                'message' => 'Validation failed',
                'errors' => $validator->errors()
            ]);
        }

        $user = new User;
        $user->name = $data['name'];
        $user->email = $data['email'];
        $user->mobile = $data['mobile'];
        $user->password = bcrypt($data['password']);
        $user->address = $data['address'] ?? null;
        $user->city = $data['city'] ?? null;
        $user->postal_code = $data['postal_code'] ?? null;
        $user->status = $data['status'] ?? 0;

        // Définir le rôle par défaut
        $user->role = 'user';

        $user->save();

        return response()->json([
            'status' => true,
            'code' => 201,
            'message' => 'User created successfully',
            'data' => $user
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

        $userDetail = User::where('email', '=', $data['email'])->first();

        $rules = [
            "email" => "required|email|exists:users,email",
            "password" => "required"
        ];

        $customMessage = [
            "email.required" =>"email is required",
            "email.exists" => "email  does not exists",
            "password.required" => "password is required"
        ];

        $validator = Validator::make($data,$rules,$customMessage);
        if ($validator->fails()){
            return response()->json($validator->errors(),422);
        }

        if ($userDetail && password_verify($data['password'], optional($userDetail)->password)) {
            return response()->json([
                'userDetail'=> $userDetail,
                'status' => true,
                'code' => 200,
                'message' => 'Login successful',
                'data' => [
                    'role' => $userDetail->role // Inclure le rôle de l'utilisateur dans la réponse
                ]
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
                "email" => "email|unique:users,email," . $data['id'],
                "mobile" => "string|unique:users,mobile," . $data['id'],
                "password" => "min:8",
                "address" => "nullable|string",
                "city" => "nullable|string",
                "postal_code" => "nullable|string",
                'profile_image' => 'image|mimes:jpeg,png,jpg,gif',

            ];

            $customMessage = [
                "name.required" => "name is required",
                "email.email" => "Invalid email format",
                "email.unique" => "Email is already taken",
                "mobile.string" => "Invalid mobile format",
                "mobile.unique" => "Mobile is already taken",
                "password.min" => "Password must be at least 8 characters long",
                "address.string" => "Invalid address format",
                "city.string" => "Invalid city format",
                "postal_code.string" => "Invalid postal code format",
            ];

            $validator = Validator::make($data, $rules, $customMessage);

        if ($validator->fails()) {
            return response()->json([
                'status' => false,
                'code' => 422,
                'message' => 'Validation failed',
                'errors' => $validator->errors()
            ]);
        }

        // Additional fields
        $updateData = [
            'name' => $data['name'],
            'email' => $data['email'],
            'mobile' => $data['mobile'],
            'status' => $data['status'] ?? 0,
            'address' => $data['address'] ?? null,
            'city' => $data['city'] ?? null,
            'postal_code' => $data['postal_code'] ?? null,
        ];

        if ($request->hasFile('profile_image')) {
            $file = $request->file('profile_image');
            $fileName = time() . '_' . $file->getClientOriginalName();
            $file->storeAs('public/photo', $fileName);
            $updateData['profile_image'] = $fileName;
        }

        // Update user details
        User::where(["id" => $data['id']])->update($updateData);

        // Retrieve the updated user
        $userDetail = User::find($data['id']);

        // Add the full image path to the response array
        $userDetail->profile_image_path = asset('storage/photo/' . $userDetail->profile_image);

        return response()->json([
            'userDetail' => $userDetail,
            'status' => true,
            'code' => 200,
            'message' => 'User updated successfully',
            'data' => $userDetail
        ]);
    } else {
        return response()->json([
            'status' => false,
            'code' => 400,
            'message' => 'Invalid request method'
        ]);
    }
}
    public function getUsers()
    {
        $users = User::all();
        return response()->json(['data' => $users], 200);
    }

    public function deleteUser($id)
    {
        $user = User::find($id);

        if (!$user) {
            return response()->json([
                'status' => false,
                'code' => 404,
                'message' => 'Utilisateur non trouvé',
            ], 404);
        }

        $user->delete();

        return response()->json([
            'status' => true,
            'code' => 200,
            'message' => 'Utilisateur supprimé avec succès',
        ]);
    }
}
