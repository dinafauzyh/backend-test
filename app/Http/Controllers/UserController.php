<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Traits\ApiResponse;
use App\Http\Requests\UserRequest;

class UserController extends Controller
{
    use ApiResponse;
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $users = User::all();

        if ($users->isEmpty()) {
            return $this->error('Users data not found', 404);
        } else {
            return $this->success($users, 'Users data retrieved successfully');
        }
    }


    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(UserRequest $request)
    {
        try {
            $user = User::create([
                'fullname' => $request->fullname,
                'username' => $request->username,
                'email' => $request->email,
                'password' => bcrypt($request->password),
                'role' => $request->role,
            ]);

            return $this->success($user, 'User created successfully');
        } catch (\Throwable $th) {
            //throw $th;
            return $this->error('User creation failed', 500);
        }
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Models\User  $user
     * @return \Illuminate\Http\Response
     */
    public function show($user)
    {
        $userData = User::find($user);

        if ($userData) {
            return $this->success($userData, 'User data retrieved successfully');
        } else {
            return $this->error('User data not found', 404);
        }
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\User  $user
     * @return \Illuminate\Http\Response
     */
    public function update(UserRequest $request, $user)
    {
        try {
            $userData = User::find($user);
            
            if(!$userData) {
                return $this->error('User not found', 404);
            } else {
                $userData->update([
                    'fullname' => $request->fullname,
                    'username' => $request->username,
                    'email' => $request->email,
                    'password' => bcrypt($request->password),
                    'role' => $request->role,
                ]);
                return $this->success($userData, 'User updated successfully');
            }
        } catch (\Throwable $th) {
            //throw $th;
            return $this->error('User update failed', 500);
        }
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\User  $user
     * @return \Illuminate\Http\Response
     */
    public function destroy($user)
    {
        try {
            $user = User::find($user);

            if ($user) {
                $user->delete();
                return $this->success($user, 'User deleted successfully');
            } else {
                return $this->error('User not found', 404);
            }
        } catch (\Throwable $th) {
            //throw $th;
            return $this->error('User deletion failed', 500);
        }
    }
}
