<?php

namespace App\Http\Controllers\User;

use App\Http\Controllers\Controller;
use App\Models\SystemPay\PayHistory;
use Illuminate\Http\Request;
use App\Http\Requests\UserRequestUpdateInfo;
use App\Models\User;

class UserManagementTransaction extends Controller
{
    public function index()
    {
        $payHistories = PayHistory::where([
            'ph_user_id' => get_data_user('web')
        ])->orderByDesc('id')
            ->paginate(10);
        $viewData     = [
            'payHistories' => $payHistories
        ];

        return view('user.management_transaction', $viewData);
    }

    public function saveUpdateInfo(Request $request)
    {
        $data = $request->except('_token','avatar');
        $user = User::find(\Auth::id());

        if ($request->avatar) {
            $image = upload_image('avatar');
            if ($image['code'] == 1)
                $data['avatar'] = $image['name'];
        }

        $user->update($data);

        \Session::flash('toastr', [
            'type'    => 'success',
            'message' => 'Cập nhật thành công'
        ]);

        return redirect()->back();
    }
}
