<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class UserController extends Controller
{
    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware('auth');
    }

    public function getInfo( Request $request )
    {
        return [
            'code'      => 1,
            'data'      => [
                'username' => auth()->user()->username,
                'usernick' => auth()->user()->nickname,
            ]
        ];
    }
}
