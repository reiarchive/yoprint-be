<?php

namespace App\Http\Controllers;

use App\Models\Upload;
use Illuminate\Http\Request;

class uploadPage extends Controller
{
    
    public function index() {
        $uploads = Upload::all();

        return view('welcome', ['uploads' => $uploads]);
    }

}
