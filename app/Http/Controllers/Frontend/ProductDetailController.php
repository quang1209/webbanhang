<?php

namespace App\Http\Controllers\Frontend;

use App\Http\Controllers\Controller;
use App\Models\Attribute;
use App\Models\Comments;
use App\Models\Event;
use Illuminate\Http\Request;
use App\Models\Product;
use App\Services\ProcessViewService;
use App\Models\Rating;

class ProductDetailController extends FrontendController
{
	public function getProductDetail(Request $request, $slug)
	{
		$arraySlug = explode('-', $slug);
		$id        = array_pop($arraySlug);

		if ($id) {
			//1. Lấy thông tin sp
			$product = Product::with('category:id,c_name,c_slug')->findOrFail($id);

			//2. Xử lý view
			ProcessViewService::view('products', 'pro_view', 'product', $id);

			// 3. Lấy đánh giá
			$ratings = Rating::with('user:id,name')
				->where('r_product_id', $id)
				->orderByDesc('id')
				->limit(5)
				->get();

			$ratingsDashboard = Rating::groupBy('r_number')
				->where('r_product_id', $id)
				->select(\DB::raw('count(r_number) as count_number'), \DB::raw('sum(r_number) as total'))
				->addSelect('r_number')
				->get()->toArray();

			$ratingDefault = $this->mapRatingDefault();

			foreach ($ratingsDashboard as $key => $item) {
				$ratingDefault[$item['r_number']] = $item;
			}


			// Lấy album ảnh
			$images = \DB::table('product_images')
				->where('pi_product_id', $id)
				->get();

			//  4 Lấy comment
			$comments = Comments::with('user:id,name', 'reply')
				->where([
					'cmt_product_id' => $id,
					'cmt_parent_id'  => 0
				])
				->orderByDesc('id')
				->paginate(5);

			if ($request->ajax()) {
				$html = view('frontend.pages.product_detail.include._inc_list_comments', compact('comments', 'product'))->render();
				return response(['html' => $html]);
			}

			$attributeOld = \DB::table('products_attributes')
				->where('pa_product_id', $id)
				->pluck('pa_attribute_id')
				->toArray();


			// Lấy event hiển thị đầu
			$event1 = Event::where('e_position_1', 1)
				->first();

			// Lấy event hiển thị 2
			$event3 = Event::where('e_position_3', 1)
				->first();

			$viewData = [
				'isPopupCaptcha'   => 0,
//				'isPopupCaptcha'   => \Auth::user()->count_comment ?? 0,
				'ratingDefault'    => $ratingDefault,
				'product'          => $product,
				'ratings'          => $ratings,
				'images'           => $images,
				'event1'           => $event1,
				'event3'           => $event3,
				'attribute'        => $this->syncAttributeGroup(),
				'comments'         => $comments,
				'attributeOld'     => $attributeOld,
				'title_page'       => $product->pro_name,
				'productsSuggests' => $this->getProductSuggests($product->pro_category_id)
			];

			return view('frontend.pages.product_detail.index', $viewData);
		}

		return redirect()->to('/');
	}

	/**
	 * List đánh giá sản phẩm
	 * */
	public function getListRatingProduct(Request $request, $slug)
	{
		$arraySlug = explode('-', $slug);
		$id        = array_pop($arraySlug);
		if ($id) {

			//1.Lấy sản phẩm
			$product = Product::with('category:id,c_name,c_slug')->findOrFail($id);

			//2. Lấy đánh giá by ID và điều kiện lọc

			$ratings = Rating::with('user:id,name')
				->where('r_product_id', $id);
			if ($number = $request->s) $ratings->where('r_number', $number);

			$ratings = $ratings->orderByDesc('id')
				->paginate(5);

			if ($request->ajax()) {
				$query = $request->query();
				$html  = view('frontend.pages.product_detail.include._inc_list_reviews', compact('ratings', 'query'))->render();
				return response(['html' => $html]);
			}

			//3 Hiển thị thông kê
			$ratingsDashboard = Rating::groupBy('r_number')
				->where('r_product_id', $id)
				->select(\DB::raw('count(r_number) as count_number'), \DB::raw('sum(r_number) as total'))
				->addSelect('r_number')
				->get()->toArray();

			$ratingDefault = $this->mapRatingDefault();

			foreach ($ratingsDashboard as $key => $item) {
				$ratingDefault[$item['r_number']] = $item;
			}

			$viewData = [
				'product'       => $product,
				'ratings'       => $ratings,
				'ratingDefault' => $ratingDefault,
				'query'         => $request->query(),
				'title_page'    => "Review, đánh gía sản phẩm " . $product->pro_name,
			];

			return view('frontend.pages.product_detail.product_ratings', $viewData);
		}
		return redirect()->to('/');
	}

	private function mapRatingDefault()
	{
		$ratingDefault = [];
		for ($i = 1; $i <= 5; $i++) {
			$ratingDefault[$i] = [
				"count_number" => 0,
				"total"        => 0,
				"r_number"     => 0
			];
		}


		return $ratingDefault;
	}

	private function getProductSuggests($categoriID)
	{
		$products = Product::where([
			'pro_active'      => 1,
			'pro_category_id' => $categoriID
		])
			->orderByDesc('id')
			->select('id', 'pro_name', 'pro_slug', 'pro_sale', 'pro_avatar', 'pro_price', 'pro_review_total','pro_review_star')
			->limit(12)
			->get();

		return $products;
	}

	public function syncAttributeGroup()
	{
		$attributes     = Attribute::get();
		$groupAttribute = [];

		foreach ($attributes as $key => $attribute) {
			$key                                  = $attribute->gettype($attribute->atb_type)['name'];
			$groupAttribute[$key][$attribute->id] = $attribute->toArray();
		}

		return $groupAttribute;
	}
}
