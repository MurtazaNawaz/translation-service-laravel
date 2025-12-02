<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Locale;

class LocaleController extends Controller
{
    /**
     * dev list locales
     * GET /api/locales
     */
    public function index()
    {
        // dev: fetch all locales (simple, no pagination needed now)
        $locales = Locale::all();

        return response()->json([
            'status'  => 'success',
            'message' => 'Locales fetched successfully',
            'data'    => $locales
        ], 200);
    }

    /**
     * dev create locale
     * POST /api/locales
     */
    public function store(Request $request)
    {
        // dev: validate input
        $validated = $request->validate([
            'code' => 'required|string|max:10|unique:locales,code',
            'name' => 'required|string|max:50',
        ]);

        // dev: create locale
        $locale = Locale::create($validated);

        return response()->json([
            'status'  => 'success',
            'message' => 'Locale created successfully',
            'data'    => $locale
        ], 201);
    }
}
