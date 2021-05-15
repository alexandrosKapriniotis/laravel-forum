<?php

namespace App\Http\Controllers;

use Illuminate\Contracts\Foundation\Application;
use Illuminate\Contracts\Routing\ResponseFactory;
use Illuminate\Http\Request;
use Illuminate\Http\Response;

class UserAvatarController extends Controller
{
    /**
     * @param Request $request
     * @return Application|ResponseFactory|Response
     */
    public function store(Request $request)
    {
        $request->validate([
            'avatar' => 'required|image'
        ]);

        $request->user()->update([
            'avatar_path' => request()->file('avatar')->store('avatars','public')
        ]);

        return response([],204);
    }
}
