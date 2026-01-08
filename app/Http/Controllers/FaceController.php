<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;

class FaceController extends Controller
{
    public function register(Request $request)
    {
        $request->validate([
            'image' => 'required|image|mimes:jpg,jpeg,png|max:2048'
        ]);

        $response = Http::attach(
            'file',
            fopen($request->file('image')->getRealPath(), 'r'),
            $request->file('image')->getClientOriginalName()
        )->post('http://127.0.0.1:8000/register-face');

        return response()->json($response->json());
    }

    public function verify(Request $request)
    {
        $request->validate([
            'image' => 'required|image|mimes:jpg,jpeg,png|max:2048'
        ]);

        $response = Http::attach(
            'file',
            fopen($request->file('image')->getRealPath(), 'r'),
            $request->file('image')->getClientOriginalName()
        )->post('http://127.0.0.1:8000/verify-face');

        return response()->json($response->json());
    }

    public function viewExamen()
    {
        return view('examen'); // resources/views/examen.blade.php
    }

}
