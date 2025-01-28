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
    public function store(Request $request)
    {
        $validator = validator::make($request->all(), [
            'name' => ['required'],
            'email'=> ['required','email','unique:users,email'],
            'password' => ['required','min:5', 'confirmed'],
            'password_confirmation' => ['required']
        ]);
        if($validator->fails()){
            return response()->json($validator->messages(), 400);
        }else{
            $data =[
                'name'=> $request->name,
                'email'=> $request->email,
                'contact'=> $request->contact,
                'password'=> Hash::make($request->password),

            ];
            DB::beginTransaction();
            try{
               user::create($data);
               DB::commit();
            }
            catch(\Exception $e){}
            DB::rollBack();
        }
        p($request->all());

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
