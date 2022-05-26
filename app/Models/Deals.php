<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Deals extends Model
{
    use HasFactory, SoftDeletes;

    protected $table = "deals";

    protected $fillable = [
        'bid_item_id',
        'item_to_bid_with_id',
        'bider_id',
    ];

    public function item_to_bid_with(){
        return $this->hasOne('App\Models\Items', 'id', 'item_to_bid_with_id');
    }

    public function bid_item(){
        return $this->hasOne('App\Models\Items', 'id', 'bid_item_id');
    }

    public function bider(){
        return $this->hasOne('App\Models\User', 'id', 'bider_id');
    }
}
