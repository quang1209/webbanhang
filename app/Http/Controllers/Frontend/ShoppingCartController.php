<?php

namespace App\Http\Controllers\Frontend;

use App\Http\Controllers\Controller;
use App\Models\DiscountCode;
use App\Models\Order;
use App\Models\Transaction;
use App\Services\ShoppingCartService\PayManager;
use Illuminate\Http\Request;
use App\Models\Product;
use Carbon\Carbon;
use Gloudemans\Shoppingcart\Facades\Cart;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Session;

class ShoppingCartController extends Controller
{
    private $vnp_TmnCode = "M0BKWT0Y"; //Mã website tại VNPAY
    private $vnp_HashSecret = "JNDBAWSROHDXUQVKHHQZOXQZBHYXNXTI"; //Chuỗi bí mật
    private $vnp_Url = "https://sandbox.vnpayment.vn/paymentv2/vpcpay.html";
    private $vnp_Returnurl = 'http://127.0.0.1:8000/shopping/hook';
    protected $idTransaction = 0;
    protected $total;
    public function coupon(Request $request){

        $data = $request->all();
        $today = Carbon::now('Asia/Ho_Chi_Minh')->format('y-m-d');
        $coupon = DiscountCode::where('d_code',$data['tst_coupon'])->where('d_date_end','>=',$today)->first();
        //kiem tra moi khach chi dung ma giam gia 1 lan
        $check = \Session::get('check');
        for($i =0 ; $i < $check; $i++){
            if($check[$i]['d_code'] ==  $coupon->d_code and $check[$i]['customer_id']==Auth::user()->id){
                \Session::flash('toastr', [
                    'type'    => 'error',
                    'message' => 'Bạn đã sử dụng mã này'
                ]);
                return redirect()->back();
            }
        }
        if($coupon){
            $discountCode = DiscountCode::find($coupon->id);
            $discountCode->d_number_code = $discountCode->d_number_code-1;
            $discountCode->save();
            $count_coupon = $coupon->count();
            if ($count_coupon>0){
                $coupon_session = \Session::get('coupon');
                if ($coupon_session == true){
                    $is_avaiable = 0;
                    if($is_avaiable == 0){
                        $num = \Cart::subtotal(0);
                        $total = filter_var($num, FILTER_SANITIZE_NUMBER_INT);
                        $newTotal = $total - ($total * $coupon->d_percentage)/100;

                        $cou[] = array(
                            'd_code'=> $coupon->d_code,
                            'd_number_code'=> $coupon->d_number_code,
                            'd_percentage' => $coupon->d_percentage,
                            'total' => $newTotal
                        );
                        $check[]= array(
                            'd_code'=> $coupon->d_code,
                            'customer_id'=> Auth::user()->id

                    );
                        Session::put('coupon',$cou);
                        Session::put('check',$check);
                    }}
            else{
                $num = \Cart::subtotal(0);
                $total = filter_var($num, FILTER_SANITIZE_NUMBER_INT);
                $newTotal = $total - ($total * $coupon->d_percentage)/100;
                $cou[] = array(
                    'd_code'=> $coupon->d_code,
                    'd_number_code'=> $coupon->d_code,
                    'd_percentage' => $coupon->d_percentage,
                    'total' => $newTotal
                );
                \Session::put('coupon',$cou);
                }
                \Session::save();
                return redirect()->back();
            }

            }
        else{
            \Session::flash('toastr', [
                'type'    => 'error',
                'message' => 'Sai mã giảm giá'
            ]);

            return redirect()->back();
        }
    }
    public function index()
    {
        $shopping = Cart::content();
        $viewData = [
            'title_page' => 'Danh sách giỏ hàng',
            'shopping'   => $shopping
        ];
        return view('frontend.pages.shopping.index', $viewData);
    }

    /**
     * Thêm giỏ hàng
     * */
    public function add($id)
    {
        $product = Product::find($id);

        //1. Kiểm tra tồn tại sản phẩm
        if (!$product) return redirect()->to('/');

        // 2. Kiểm tra số lượng sản phẩm
        if ($product->pro_number < 1) {
            //4. Thông báo
            \Session::flash('toastr', [
                'type'    => 'error',
                'message' => 'Số lượng sản phẩm không đủ'
            ]);

            return redirect()->back();
        }

        $check = $this->searchItemByIdCart($product->id);
        if (($check + 1) > $product->pro_number )
		{
			\Session::flash('toastr', [
				'type'    => 'error',
				'message' => 'Số lượng sản phẩm không đủ'
			]);

			return redirect()->back();
		}

		/*if (($check + 1) > 10 )
		{
			\Session::flash('toastr', [
				'type'    => 'error',
				'message' => 'Mỗi sản phẩm chỉ mua được tối đa 10 sản phẩm'
			]);

			return redirect()->back();
		}*/

        // 3. Thêm sản phẩm vào giỏ hàng
        \Cart::add([
            'id'      => $product->id,
            'name'    => $product->pro_name,
            'qty'     => 1,
            'price'   => number_price($product->pro_price, $product->pro_sale),
            'weight'  => '1',
            'options' => [
                'sale'      => $product->pro_sale,
                'price_old' => $product->pro_price,
                'image'     => $product->pro_avatar
            ]
        ]);

        //4. Thông báo
        \Session::flash('toastr', [
            'type'    => 'success',
            'message' => 'Thêm giỏ hàng thành công'
        ]);

        return redirect()->back();
    }

    public function postPay(Request $request)
    {
        $data = $request->except("_token");
        if (!\Auth::user()->id) {
            //4. Thông báo
            \Session::flash('toastr', [
                'type'    => 'error',
                'message' => 'Đăng nhập để thực hiện tính năng này'
            ]);

            return redirect()->back();
        }
        $coupon_session = \Session::get('coupon');
        if ($coupon_session == true){
            $data['tst_user_id'] = \Auth::user()->id;
            $data['tst_total_money'] = (string)$coupon_session[0]['total'];
            $data['created_at']      = Carbon::now();
        }
        else{
          $data['tst_user_id'] = \Auth::user()->id;
          $data['tst_total_money'] = str_replace(',', '', Cart::subtotal(0));
          $data['created_at']      = Carbon::now();
        }


        // Lấy thông tin đơn hàng
        $shopping = \Cart::content();
        $data['options']['orders'] = $shopping;

        $options['drive'] = $request->pay;
        if($request->pay == 'transfer')
        {
            $data['tst_type'] = 2;
            return $this->payOnline($request, $data,$shopping, $options);
        }else{
            try{
                \Cart::destroy();
                new PayManager($data, $shopping, $options);
            }catch (\Exception $exception){
                Log::error("[Errors pay shopping cart]" .$exception->getMessage());
            }

            \Session::flash('toastr', [
                'type'    => 'success',
                'message' => 'Đơn hàng của bạn đã được lưu'
            ]);
        }

        \Session::flash('toastr', [
            'type'    => 'success',
            'message' => 'Đơn hàng của bạn đã được lưu'
        ]);
        \Session::forget('coupon');
        return redirect()->to('/');
    }

    public function hookCallback(Request $request)
    {

        $transactionID = $request->vnp_TxnRef;
        $transaction = Transaction::find($transactionID);

        if ($request->vnp_ResponseCode == '00')
        {
            if ($transaction)
            {
                \Cart::destroy();
                $transaction->tst_status = Transaction::STATUS_SUCCESS;
                $transaction->save();
                \Session::flash('toastr', [
                    'type'    => 'success',
                    'message' => 'Thanh toán thành công'
                ]);

                return redirect()->to('/');
            }

            return redirect()->to('/')->with('danger','Mã đơn hàng không tồn tại');
        }

        if ($transaction)  $transaction->delete();

        return  redirect()->to('/');
    }

    public function update(Request $request, $id)
    {
        if ($request->ajax()) {

            //1.Lấy tham số
            $qty       = $request->qty ?? 1;
            $idProduct = $request->idProduct;
            $product   = Product::find($idProduct);

            //2. Kiểm tra tồn tại sản phẩm
            if (!$product) return response(['messages' => 'Không tồn tại sản sản phẩm cần update']);

            //3. Kiểm tra số lượng sản phẩm còn ko
            if ($product->pro_number < $qty) {
                return response([
                    'messages' => 'Số lượng cập nhật không đủ',
                    'error'    => true
                ]);
            }

            //4. Update
            Cart::update($id, $qty);

            return response([
                'messages'   => 'Cập nhật thành công',
                'totalMoney' => Cart::subtotal(0),
                'totalItem'  => number_format(number_price($product->pro_price, $product->pro_sale) * $qty, 0, ',', '.')
            ]);
        }
    }

    public function payOnline(Request $request, $data, $shopping, $options)
    {
        // Sau khi xử lý xong bắt đầu xử lý online
        error_reporting(E_ALL & ~E_NOTICE & ~E_DEPRECATED);

        $dataTransaction     = $this->getDataTransaction($data);

        $this->idTransaction = Transaction::insertGetId($dataTransaction);

        $orders              = $data['options']['orders'] ?? [];
        if ($this->idTransaction)
            $this->syncOrder($orders, $this->idTransaction);

        // tham so dau vao
        $inputData = array(
            "vnp_Version"    => "2.0.0",
            "vnp_TmnCode"    => $this->vnp_TmnCode,
            "vnp_Amount"     => $dataTransaction['tst_total_money'] * 100, // so tien thanh toan,
            "vnp_Command"    => "pay",
            "vnp_CreateDate" => date('YmdHis'),
            "vnp_CurrCode"   => "VND",
            "vnp_IpAddr"     => $_SERVER['REMOTE_ADDR'], // IP
            "vnp_Locale"     => 'vi', // ngon ngu,
            "vnp_OrderInfo"  => 'Thanh toán Onlinr', // noi dung thanh toan,
            "vnp_OrderType"  => 'billpayment',    // loai hinh thanh toan
            "vnp_ReturnUrl"  => $this->vnp_Returnurl,   // duong dan tra ve
            "vnp_TxnRef"     => $this->idTransaction, // ma don hang,
        );

        if ($request->bank_code) {
            $inputData['vnp_BankCode'] = $request->bank_code;
        }
        ksort($inputData);
        $query    = "";
        $i        = 0;
        $hashdata = "";
        foreach ($inputData as $key => $value) {
            if ($i == 1) {
                $hashdata .= '&' . $key . "=" . $value;
            } else {
                $hashdata .= $key . "=" . $value;
                $i        = 1;
            }
            $query .= urlencode($key) . "=" . urlencode($value) . '&';
        }


        $vnp_Url = $this->vnp_Url . "?" . $query;
        if ($this->vnp_HashSecret) {
            $vnpSecureHash = hash('sha256', $this->vnp_HashSecret . $hashdata);
            $vnp_Url       .= 'vnp_SecureHashType=SHA256&vnp_SecureHash=' . $vnpSecureHash;
        }

        $returnData = array(
            'code'    => '00',
            'message' => 'success',
            'data'    => $vnp_Url
        );

        return redirect()->to($returnData['data']);
    }


    protected function searchItemByIdCart($productID)
	{
		$shopping = \Cart::content();

		foreach ($shopping as $item)
		{
			if ($item->id == $productID)
				return $item->qty;
		}

		return 0;
	}

    /**
     *  Xoá sản phẩm đơn hang
     * */
    public function delete(Request $request, $rowId)
    {
        if ($request->ajax())
        {
            \Cart::remove($rowId);
            return response([
                'totalMoney' => \Cart::subtotal(0),
                'type'       => 'success',
                'messages'    => 'Xoá thành công'
            ]);
        }
    }

    public function getDataTransaction($data)
    {
        return [
            "tst_name"        => Arr::get($data, 'tst_name'),
            "tst_phone"       => Arr::get($data, 'tst_phone'),
            "tst_address"     => Arr::get($data, 'tst_address'),
            "tst_email"       => Arr::get($data, 'tst_email'),
            "tst_note"        => Arr::get($data, 'tst_note'),
            "tst_user_id"     => Arr::get($data, 'tst_user_id'),
            "tst_total_money" => Arr::get($data, 'tst_total_money'),
            "tst_type"        => Arr::get($data, 'tst_type'),
            "created_at"      => Carbon::now()
        ];
    }

    /**
     * @param $productId
     * Tăn số lượng sản phẩm
     */
    public function incrementPayProduct($productId)
    {
        \DB::table('products')
            ->where('id', $productId)
            ->increment("pro_pay");
    }

    /**
     * @param $orders
     * @param $transactionID
     * Lưu chi tiết đơn hàng
     */
    public function syncOrder($orders, $transactionID)
    {
        if ($orders) {
            foreach ($orders as $key => $item) {
                $order               = $this->getDataOrder($item, $transactionID);
                $order['created_at'] = Carbon::now();
                //1. Lưu chi tiết đơn hàng
                Order::insert($order);

                //2. Tăng pay ( số lượt mua của sản phẩm dó)
                $this->incrementPayProduct($item->id);
            }
        }
    }

    /**
     * @param $order
     * @param $transactionID
     * @return array
     */
    public function getDataOrder($order, $transactionID)
    {
        return [
            'od_transaction_id' => $transactionID,
            'od_product_id'     => $order->id,
            'od_sale'           => $order->options->sale,
            'od_qty'            => $order->qty,
            'od_price'          => $order->price
        ];
    }
}
