<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\DB;


class UserController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index($flag)
    {
    $query = User::select('email' , 'name');
    if ($flag == 1) {
    $query->where('status', 1);

    }elseif ($flag == 0) {
        $query->where('status',0);
    }else{
        return response()->json([   
            'status'=> 0,
            'message'=> 'Invalid parameter passed'

        ],  400);
    }

    $users = $query->get();
   if (count($users) > 0) {
    //user exists
    $response = [
        'message' => count($users) .'users found',
        'status' => '1',
        'data'=> $users
    ];
    return response()->json($response , 200);
    }else{
        $response = [
            'message'=> 'users not found',
            'status'=> '0',
        ];
        return response()->json($response , 200);
    }
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
    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'name' => ['required'],
            'email'=> ['required', 'email', 'unique:users,email'],
            'password' => ['required', 'min:5', 'confirmed'],
            'password_confirmation' => ['required']
        ]);
    
        if ($validator->fails()) {
            return response()->json($validator->messages(), 400);
        }
    
        $data = [
            'name' => $request->name,
            'email' => $request->email,
            'contact' => $request->contact ?? null, // Optional field
            'password' => Hash::make($request->password),
        ];
    
        DB::beginTransaction();
    
        try {
            $user = User::create($data);
            DB::commit(); // Commit transaction if successful
            return response()->json(['message' => 'User Registered Successfully'], 200);
        } catch (\Exception $e) {
            DB::rollBack(); // Rollback transaction on error
            return response()->json([
                'message' => 'Internal Server Error',
                'error' => $e->getMessage() // Optional for debugging
            ], 500);
        }
    }
    

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        $user = user::find($id);
        if(is_null($user)){
            $response = [
            'message'=> 'User not found',
            'status' => 0,
        ];
        }else{
            $response = [
                'message'=> 'User found',
                'status'=> 1,
                'data' => $user,
            ];

        }
        return response()->json($response , 200);
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
    // public function update(Request $request, $id)
    // {
    //     $user = User::find($id);
    //     if(is_null($user)){
    //        return response()->json([
    //         'message'=> 'User does not exists',
    //         'status' => 0,
    //     ],404);
    //     }else{
    //         DB::beginTransaction();
    //         try {
    //             $user->name = $request->name;
    //             $user->email = $request->email;
    //             $user->contact = $request->contact;
    //             $user->pincode = $request->pincode;
    //             $user->address = $request->address;
    //             $user->save();
    //             DB::commit();
    //         }catch(\Exception $err){
    //             DB::rollBack();
    //             $user = null;
    //         }
    //             if(is_null($user)){
    //                 return response()->json([
    //                     'message'=> 'Internal server error',
    //                     'status'=> 0,
    //                     'error_msg' => $err->getMessage()
    //                 ],500
    //                     );}else
    //                     {
    //                         return response()->json([
    //                             'message'=> 'Data Updated Sucessfully',
    //                             'status'=> 1,
    //                         ],200
    //                     );
    //                     }
       
    //     }
    // }
    public function update(Request $request, $id)
{
    $user = User::find($id); // Fix case issue

    if (is_null($user)) {
        return response()->json([
            'message' => 'User does not exist',
            'status' => 0,
        ], 404);
    }

    DB::beginTransaction();
    try {
        $user->name = $request->name;
        $user->email = $request->email;
        $user->contact = $request->contact;
        $user->pincode = $request->pincode;
        $user->address = $request->address;
        $user->save();

        DB::commit();

        return response()->json([
            'message' => 'Data Updated Successfully',
            'status' => 1,
        ], 200);
    } catch (\Exception $err) {
        DB::rollBack();
        
        return response()->json([
            'message' => 'Internal Server Error',
            'status' => 0,
            'error_msg' => $err->getMessage()
        ], 500);
    }
}


    /**
     * Remove the specified resource from storage.
     */
    public function destroy($id)
    {
        // Find the user by ID
        $user = User::find($id);
        
        // If user does not exist
        if (is_null($user)) {
            return response()->json([
                'message' => "User doesn't exist",
                'status' => 0,
            ], 404);
        }
    
        // Attempt to delete the user
        try {
            $user->delete(); // Delete the user
            return response()->json([
                'message' => "User deleted successfully",
                'status' => 1,
            ], 200);
        } catch (\Exception $err) {
            // If any error occurs, return 500 status
            return response()->json([
                'message' => 'Internal server error',
                'status' => 0,
            ], 500);
        }
    }
   public function changePassword(Request $request, $id)
{
    $user = User::find($id);

    if (is_null($user)) {
        return response()->json([
            'status' => 0,
            'message' => 'User not found'
        ], 404);
    }

    // Validate request
    $request->validate([
        'old_password' => 'required',
        'new_password' => 'required|min:5|confirmed',
        'new_password_confirmation' => 'required'
    ]);

    // Check if old password is correct
    if (!Hash::check($request->old_password, $user->password)) {
        return response()->json([
            'status' => 0,
            'message' => 'Old password is incorrect'
        ], 400);
    }

    // Update password
    $user->password = Hash::make($request->new_password);
    $user->save();

    return response()->json([
        'status' => 1,
        'message' => 'Password changed successfully'
    ], 200);
}
    
}
