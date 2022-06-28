<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class FavoritesController extends Controller
{
    /**
     * 特定の投稿をfavoriteするアクション。
     * 
     * @param  $id 特定の投稿のid
     * @return \Illuminate\Http\Response
     */
    public function store($id)
    {
        \Auth::user()->favorite($id);
        return back();
    }
    
    /**
     * 特定の投稿をunfavoriteするアクション。
     * 
     * @param  $id 特定の投稿のid
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        \Auth::user()->unfavorite($id);
        return back();
    }
}
