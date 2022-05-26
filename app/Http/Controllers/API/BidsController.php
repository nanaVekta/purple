<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use App\Models\Items;
use Illuminate\Support\Facades\Auth;
use App\Models\Bids;

class BidsController extends Controller
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
     *      path="/bids/create",
     *      operationId="createBid",
     *      tags={"Bids"},
     *      summary="Create bit",
     *      description="Return string",
     *      @OA\Response(
     *          response=201,
     *          description="Success",
     *          @OA\JsonContent(
     *              @OA\Property(property="message", type="string", example="Bid successfully created"),
     *          ),
     *      ),
     *      @OA\Response(
     *          response=401,
     *          description="Unauthorized",
     *          @OA\JsonContent(
     *              @OA\Property(property="message", type="string", example="Unauthorized"),
     *          ),
     *      ),
     *      @OA\Response(
     *          response=403,
     *          description="Already have an item",
     *          @OA\JsonContent(
     *              @OA\Property(property="message", type="string", example="You already have a bid for this item"),
     *          ),
     *      ),
     *      @OA\Response(
     *          response=404,
     *          description="Item not found",
     *          @OA\JsonContent(
     *              @OA\Property(property="message", type="string", example="Item not found"),
     *          ),
     *      ),
     *      @OA\Response(
     *          response=405,
     *          description="Ownership error",
     *          @OA\JsonContent(
     *              @OA\Property(property="message", type="string", example="You can only create a bid for an item you own"),
     *          ),
     *      ),
     * )
     */

    public function createBid(Request $request){
        $validator = Validator::make($request->all(), [
            'item_id' => 'required',
        ]);

        if($validator->fails()){
            return response()->json($validator->errors(), 422);
        }

        $item = Items::find($request->item_id)->first();
        // check if user has item
        $user = Auth::user()->id;
        if(!$item){
            return response()->json(['message' => 'Item not found'], 404);
        }
        if($item->users_id !== $user){
            return response()->json(['message' => 'You can only create a bid for an item you own'], 405);
        }

        if($item->bids){
            return response()->json(['message' => 'You already have a bid for this item'], 403);
        }

        $bid = new Bids;
        $bid->items_id = $request->item_id;
        $bid->users_id = $user;
        $bid->save();

        return response()->json(['message' => 'Bid successfully created'], 201);
    }
}
