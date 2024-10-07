<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Arr;
use App\Models\User;

class Product extends Model
{
    protected $guarded = [''];

    public function category()
    {
        return $this->belongsTo(Category::class,'pro_category_id');
    }
    public function attributes()
    {
        return $this->belongsToMany(Attribute::class,'products_attributes','pa_product_id','pa_attribute_id');
    }

    public function favourite()
    {
        return $this->belongsToMany(User::class,'user_favourite','uf_product_id','uf_user_id');
    }
}
