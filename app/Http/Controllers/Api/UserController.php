<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\Api\UserRequest;
use App\Http\Resources\UserResource;
use App\Models\User;

class UserController extends Controller
{
    public function show($id)
    {
        $user = User::findOrFail($id);

        return response(new UserResource($user));

    }

    public function update(UserRequest $request)
    {
        $data = $request->validated();
        auth()->user()->update($data);

        return response(['status' => 'success'], 200);
    }

    public function destroy()
    {
        auth()->user()->delete();
        auth()->user()->tokens->delete();

        return response('', 204);
    }
}
