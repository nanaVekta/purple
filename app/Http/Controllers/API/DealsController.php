<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Models\Deals;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use App\Models\Items;
use Illuminate\Support\Facades\Auth;

class DealsController extends Controller
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
     *      path="/deals/create",
     *      operationId="createDeal",
     *      tags={"Deals"},
     *      summary="Create deal",
     *      description="Return string",
     *      @OA\Response(
     *          response=201,
     *          description="Success",
     *          @OA\JsonContent(
     *              @OA\Property(property="message", type="string", example="Deal successfully created"),
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
     *              @OA\Property(property="message", type="string", example="You have already bid for this item"),
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
     *              @OA\Property(property="message", type="string", example="You can only create a deal for an item you own"),
     *          ),
     *      ),
     *      @OA\Response(
     *          response=406,
     *          description="Ownership error",
     *          @OA\JsonContent(
     *              @OA\Property(property="message", type="string", example="Your item's value is less than the value of the item you are bidding for"),
     *          ),
     *      ),
     * )
     */
    public function createDeal(Request $request){
        $validator = Validator::make($request->all(), [
            'bid_item_id' => 'required',
            'item_to_bid_with_id' => 'required'
        ]);

        if($validator->fails()){
            return response()->json($validator->errors(), 422);
        }

        $item = Items::find($request->bid_item_id)->first();
        $item_to_bid_with = Items::find($request->item_to_bid_with_id)->first();

        $user = Auth::user()->id;
        if(!$item || !$item_to_bid_with){
            return response()->json(['message' => 'Item not found'], 404);
        }
        if($item_to_bid_with->users_id !== $user){
            return response()->json(['message' => 'You can only create a deal with an item you own'. $item_to_bid_with->name], 405);
        }

        if($item_to_bid_with->value < $item->value){
            return response()->json(['message' => "Your item's value is less than the value of the item you are bidding for"], 406);
        }

        // check if user has a deal
        $deal = Deals::where('bider_id', $user)->first();
        if($deal){
            return response()->json(['message' => 'You have already bid for this item'], 403);
        }

        $deal = new Deals();
        $deal->bid_item_id = $request->bid_item_id;
        $deal->item_to_bid_with_id = $request->item_to_bid_with_id;
        $deal->bider_id = $user;

        return response()->json(['message' => 'Deal successfully created'], 201);
    }

    /**
     * @OA\Post(
     *      path="/deals/accept",
     *      operationId="acceptDeal",
     *      tags={"Deals"},
     *      summary="Accept deal",
     *      description="Return string",
     *      @OA\Response(
     *          response=201,
     *          description="Success",
     *          @OA\JsonContent(
     *              @OA\Property(property="message", type="string", example="Deal accepted successfully"),
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
     *          response=422,
     *          description="Validation error",
     *          @OA\JsonContent(
     *              @OA\Property(property="message", type="string", example="{'deal_id' => 'deal_id required'}"),
     *          ),
     *      ),
     *      @OA\Response(
     *          response=404,
     *          description="Deal not found",
     *          @OA\JsonContent(
     *              @OA\Property(property="message", type="string", example="Deal not found"),
     *          ),
     *      ),
     * )
     */
    public function acceptDeal(Request $request){
        $validator = Validator::make($request->all(), [
            'deal_id' => 'required',
        ]);

        if($validator->fails()){
            return response()->json($validator->errors(), 422);
        }

        $deal = Deals::find($request->deal_id);

        if(!$deal){
            return response()->json(['message' => 'Deal not found'], 404);
        }

        $bider = $deal->bider;

        // exchange items

        $bid_item = Items::find($deal->bid_item_id)->get();
        $item_owner = $bid_item->users_id;
        $bid_item->users_id = $bider;
        $bid_item->save();

        $item_to_bid_with = Items::find($deal->item_to_bid_with_id)->get();
        $item_to_bid_with->users_id = $item_owner;
        $item_to_bid_with->save();

        $deal->delete();

        return response()->json(['message' => 'Deal accepted successfully'], 201);

    }
}
