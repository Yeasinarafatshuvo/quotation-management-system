<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Wastage extends Model
{
    use HasFactory;

    public function products()
    {
        return $this->hasOne(Product::class, 'id','product_id');
    }

    public function users()
    {
        return $this->hasOne(User::class, 'id','created_by');
    }
}
