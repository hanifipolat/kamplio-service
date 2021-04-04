<?php

namespace App\Http\Controllers;

use App\Models\Interest;
use Illuminate\Http\Request;

class PostController extends Controller
{

    public function showPostList()
    {
        $user = auth()->user();
        foreach ($user->interests as $interest) {
            $posts[]=Interest::with(['posts'])->find($interest->id);
        }
        return response()->json($posts, 200);
    }
    //
}
