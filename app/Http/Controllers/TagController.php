<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Tag;

class TagController extends Controller
{
    /**
     * dev list all tags
     * GET /api/tags
     */
    public function index()
    {
        $tags = Tag::all();

        return response()->json([
            'status'  => 'success',
            'message' => 'Tags fetched successfully',
            'data'    => $tags
        ], 200);
    }

    /**
     * dev create tag
     * POST /api/tags
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:50|unique:tags,name',
        ]);

        $tag = Tag::create($validated);

        return response()->json([
            'status'  => 'success',
            'message' => 'Tag created successfully',
            'data'    => $tag
        ], 201);
    }
}
