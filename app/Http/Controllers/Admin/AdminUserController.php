<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Transaction;
use App\Models\User as ModelsUser;
use Illuminate\Http\Request;
use App\User;
use Illuminate\Foundation\Auth\User as AuthUser;

class AdminUserController extends Controller
{
    public function index()
    {
        $users = ModelsUser::paginate(10);

        $viewData = [
            'users' => $users
        ];

        return view('admin.user.index', $viewData);
    }

    public function transaction(Request $request, $id)
	{
		if ($request->ajax())
		{
			$transactions = Transaction::where([
				'tst_user_id' => $id,
			])->whereIn('tst_status',[1,2])
				->orderByDesc('id')
				->paginate(10);

			$view = view('admin.user.transaction', compact('transactions'))->render();

			return response()->json(['html' => $view]);
		}
	}

    public function delete($id)
    {
        $user = AuthUser::find($id);
        if ($user) $user->delete();

        return redirect()->back();
    }
}
