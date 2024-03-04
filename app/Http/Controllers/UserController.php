<?php

namespace App\Http\Controllers;

use App\Http\Requests\ChangeUserPasswordRequest;
use App\Http\Requests\LoginUserRequest;
use App\Http\Requests\StoreUserRequest;
use App\Models\Level;
use App\Models\Role;
use App\Models\User;
use App\Services\GenerateToken;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;

class UserController extends Controller
{

    protected $queryWith = ['country', 'level', 'currency', 'role'];
    protected $generateTokenService;

    public function __construct(GenerateToken $generateTokenService)
    {
        $this->generateTokenService = $generateTokenService;
    }

    public function index(Request $request)
    {
        $queryBuilder = User::with($this->queryWith)->select('id', 'name', 'email', 'phone', 'role_id','level_id','address');
        if ($request->has('role')) {
            $role = Role::where("name", $request->role)->first();
            if ($role) {
                $queryBuilder->where("role_id", $role->id);
            } else {
                return response()->json(["message" => 'Role not found.'], 404);
            }
            return response()->json($queryBuilder->get());
        }
        return response()->json($queryBuilder->get());
    }

    public function register(StoreUserRequest $request)
    {
        $validated = $request->validated();
        $validated["password"] = Hash::make($validated["password"]);
        $user = User::create($validated);

        if ($request->has('role')) {
            $role = Role::where('name', $request->input('role'))->first();
        }
        if ($role) {
            $user->role()->associate($role);
            $user->save();
        }

        $level = Level::firstOrCreate(['name' => 'level 0']);
        $user->level()->associate($level);
        $user->save();

        $token = $this->generateTokenService->GenerateToken($user);
        return response()->json([
            'user' => User::with($this->queryWith)->find($user->id),
            'token' => $token,
        ]);
    }

    public function login(LoginUserRequest $request)
    {
        $user = User::with($this->queryWith)->where('email', $request->email)->first();

        if (!$user || !Hash::check($request->password, $user->password)) {
            return response()->json(["message" => 'The provided credentials are incorrect.'], 401);
        }
        $token = $this->generateTokenService->GenerateToken($user);
        return response()->json([
            'user' => $user,
            'token' => $token,
        ]);
    }

    public function destroy($id)
    {
        $user = User::find($id);
        if ($user) {
            $user->delete();
            return response()->json(['message' => 'Successfully Deleted.', 'id' => $user->id]);
        } else {
            return response()->json(['message' => "Not found id: " . $id], 404);
        }
    }

    public function changePassword(ChangeUserPasswordRequest $request)
    {
        if (!Hash::check($request->get('old_password'), $request->user()->password)) {
            return response()->json(["message" => "Old password is invalid. Please try again."], 422);
        }
        $user = User::find($request->user()->id);
        $user->password =  Hash::make($request->new_password);
        $user->update();
        return response()->json(["message" => "Successfully changed password."]);
    }

    public function logout(Request $request)
    {
        $request->user()->tokens()->delete();
        return response()->json(['message' => 'Successfully Logout!']);
    }
}
