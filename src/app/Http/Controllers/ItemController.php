<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Redirect;
use App\Models\User;

class ItemController extends Controller
{
    public function index()
    {
        // ログインしている場合
        if (Auth::check()) {
            $user = Auth::user();

            // 初めてのログイン（プロフィールが未完了の場合）
            if ($user->profile_completed == false) {
                return Redirect::route('mypage.profile');
            }
        }

        // 誰でもアクセスできるindexページを表示
        return view('index');
    }
}
