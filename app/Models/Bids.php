<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Bids extends Model
{
    use HasFactory, SoftDeletes;

    protected $table = "bids";

    protected $fillable = [
        'item_id',
        'user_id',
    ];

    public function item(){
        return $this->hasOne('App\Models\Items');
    }

    public function user(){
        return $this->hasOne('App\Models\User');
    }
}
