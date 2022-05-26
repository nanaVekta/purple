<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Models\Items;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class ItemsController extends Controller
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
     *      path="/items/create",
     *      operationId="createItems",
     *      tags={"Items"},
     *      summary="Create items",
     *      description="Return created items",
     *      @OA\Response(
     *          response=200,
     *          description="Success",
     *          @OA\JsonContent(
     *              @OA\Property(property="message", type="string", example="Items successfully created"),
     *              @OA\Property(property="items", type="array", @OA\Items(
     *                  @OA\Property(property="name", type="string", example="Item 1"),
     *                  @OA\Property(property="value", type="integer", example="100"),
     *              )),
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
     *              @OA\Property(property="message", type="string", example="You already have an item"),
     *          ),
     *      ),
     * )
     */
    public function createItem()
    {

        $item_names = array('Water', 'Shirt', 'Pants', 'Dogs', 'BE Developer', 'Soup');

        $user_id = Auth::user()->id;

        $check_user_has_item = Items::where('user_id', $user_id)->count();

        if($check_user_has_item > 0){
            return response()->json([
                'message' => 'You already have an item',
            ], 403);
        }

        $i = 1;
        $items_arr = [];
        while($i <= 2){
            $item_name = $item_names[rand(0, count($item_names) - 1)];
            $item_value = rand(3, 20);
            array_push($items_arr, [
                'item_name' => $item_name,
                'item_value' => $item_value,
            ]);
            $item = new Items;
            $item->users_id = $user_id;
            $item->name = $item_name;
            $item->value = $item_value;
            $item->save();
            $i++;
        }

        return response()->json([
            'message' => 'Item successfully created',
            'items' => $items_arr,
        ], 201);
    }
}
