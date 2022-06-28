<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class UserFollowController extends Controller
{
    /**
     * ユーザをフォローするアクション。
     * 
     * @param  $id 相手ユーザのid
     * @return \Illuminate\Http\Response
     */
    public function store($id)
    {
        \Auth::user()->follow($id);
        return back();
    }
    
    /**
     * ユーザをアンフォローするアクション。
     *
     * @param  $id 相手ユーザのid
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        \Auth::user()->unfollow($id);
        return back();
    }
}
