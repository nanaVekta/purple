<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;

class UserController extends Controller
{
    /**
     * Create a new AuthController instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware('auth:api');
    }

    /**
     * @OA\Post(
     *      path="/users/update",
     *      operationId="updateProfile",
     *      tags={"Users"},
     *      summary="Update Profile of authenticated User",
     *      description="Returns user data",
     *  @OA\RequestBody(
     *    required=true,
     *    description="Data from frontend",
     *       @OA\MediaType(
     *           mediaType="multipart/form-data",
     *           @OA\Schema(
     *       @OA\Property(property="name", type="string", example="John Doe"),
     *           ),
     *       ),
     *    @OA\JsonContent(
     *       required={"name"},
     *       @OA\Property(property="name", type="string", example="John Doe")
     *    ),
     * ),
     *     @OA\Response(
     *    response=200,
     *    description="Success",
     *    @OA\JsonContent(
     *       @OA\Property(property="message", type="string", example="User successfully updated"),
     *       @OA\Property(property="user", type="string", example="'user': {'id': 1, 'name': 'Hello world','email': 'test@one.com','email_verified_at': null,'created_at': '2022-05-26T06:56:01.000000Z','updated_at': '2022-05-26T07:12:19.000000Z'}"),
     *        )
     *     ),
     *      @OA\Response(
     *          response=403,
     *          description="Forbidden"
     *      )
     * )
     */
    public function updateProfile(Request $request){

        $validator = Validator::make($request->all(), [
            'name' => 'required|string|min:2|max:100'
        ]);

        if($validator->fails()) {
            return response()->json($validator->errors(), 422);
        }

        $auth_user = Auth::user();

        $user = User::find($auth_user->id);
        $user->name = $request->name;
        $user->save();

        return response()->json([
            'message' => 'User successfully updated',
            'user' => $user
        ], 200);

    }
}
