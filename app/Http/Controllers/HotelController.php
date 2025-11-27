<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class HotelController extends Controller
{
    public function store(Request $request) {
        $validated = $request->validate([
            'google_map_url' => 'required|url'
        ]);

        return response()->json([
            'message' => 'Hotel created successfully',
            'data' => $validated['google_map_url']
        ], 201);
    }
}
