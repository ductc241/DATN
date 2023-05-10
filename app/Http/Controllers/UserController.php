<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Http\Requests\UserRequest;
use App\Http\Requests\UpdateUserRequest;
use App\Http\Resources\UpdateUserResource;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;


class UserController extends Controller
{
    public function index(Request $request)
    {
        $user = User::query()->when($request->keyword, function (Builder $query, string $keyword) {
            $query->where('name', 'like', '%' . $keyword . '%')->orWhere('email', 'like', '%' . $keyword . '%');
        })->get();

        return $user;
    }

    public function save(UserRequest $request)
    {
        $data  = array_merge($request->all(), [
            'created_at' => date('Y-m-d H:i:s'),
            'status' => 1,
            'password' => Hash::make($request->password)
        ]);
        if (User::insertGetId($data)) {
            if ($request->file('image')) {
                $user['image'] = $this->uploadFile($request->file('image'));
            }
            return true;
        }
        return false;
    }

    public function uploadFile($file)
    {
        $filename =  time() . '_' . $file->getClientOriginalName();
        return $file->storeAs('imagesUser', $filename,  'public');
    }

    public function getUser($id)
    {
        return new UpdateUserResource(User::where('status', '=', 1)->find($id));
    }

    public function store($id, UpdateUserRequest $request)
    {
        if ($request->file('avatar')) {
            $user['avatar'] = $this->uploadFile($request->file('avatar'));
        }
        return User::query()->find($id)->update($request->all());
    }

    public function delete($id)
    {
        if (!empty($id)) {
            $User = User::where('id', '=', $id);
            $data = [
                'status' => 0
            ];
            $User->update($data);
            return true;
        }
    }
}
