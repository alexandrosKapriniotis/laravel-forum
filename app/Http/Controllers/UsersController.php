<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;

class UsersController extends Controller
{
    public function index()
    {
        $search = request('name');

        $val =  User::where('name', 'LIKE', "$search%")
            ->take(5)
            ->pluck('name');

        return $val->map(function ($name) {
            return ['value' => $name];
        });
    }
}
