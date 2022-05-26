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

    public function createItem(Request $request)
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
            $item->user_id = $user_id;
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
