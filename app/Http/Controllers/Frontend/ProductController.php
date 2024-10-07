<?php

namespace App\Http\Controllers\Frontend;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Attribute;
use App\Models\Product;

class ProductController extends FrontendController
{
    public function index(Request $request)
    {
        

        $products       = Product::where('pro_active',1);



        if ($name = $request->k) $products->where('pro_name','like','%'.$name.'%');


        if ($request->price) {
            $price =  $request->price;
            if ($price == 6) {
                $products->where('pro_price','>', 1000000);
            }else{
                $products->where('pro_price','<=', 200000 * $price);
            }
        }

        if ($request->k) $products->where('pro_name','like','%'.$request->k.'%');
        if ($request->rv) $products->where('pro_review_star','>',$request->rv);
        if ($request->sort) $products->orderBy('id',$request->sort);

        $products =  $products->select('id','pro_name','pro_slug','pro_sale','pro_avatar','pro_price','pro_review_total','pro_review_star')
            ->paginate(12);

        $attributes =  $this->syncAttributeGroup();


        $viewData = [
            'attributes'    => $attributes,
            'products'      => $products,
            'query'         => $request->query(),
            'link_search'   => request()->fullUrlWithQuery(['k' => \Request::get('k')])
        ];

        return view('frontend.pages.product.index', $viewData);
    }
}
