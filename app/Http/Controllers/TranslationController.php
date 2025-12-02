<?php


namespace App\Http\Controllers;

/**
 * @OA\Info(
 *      version="1.0.0",
 *      title="Translation Management API",
 *      description="API for managing translations, locales, and tags"
 * )
 */

/**
 * @OA\Get(
 *      path="/api/translations",
 *      summary="List translations",
 *      description="Retrieve list of translations with optional filters",
 *      @OA\Parameter(
 *          name="locale",
 *          in="query",
 *          description="Locale code (en, fr, es, etc.)",
 *          required=false,
 *          @OA\Schema(type="string")
 *      ),
 *      @OA\Parameter(
 *          name="tag",
 *          in="query",
 *          description="Filter by tag",
 *          required=false,
 *          @OA\Schema(type="string")
 *      ),
 *      @OA\Parameter(
 *          name="q",
 *          in="query",
 *          description="Search term for key or value",
 *          required=false,
 *          @OA\Schema(type="string")
 *      ),
 *      @OA\Response(response=200, description="List of translations")
 * )
 */


use Illuminate\Http\Request;
use App\Models\Translation;
use Illuminate\Support\Facades\Cache;

class TranslationController extends Controller
{
    // dev list/search translations
    public function index(Request $request)
    {
        $locale = $request->query('locale'); 
        $tag = $request->query('tag');       
        $search = $request->query('q');      
        $perPage = (int) $request->query('per_page', 20);

        $query = Translation::with(['locale', 'tags']);

        if ($locale) {
            $query->whereHas('locale', function ($q) use ($locale) {
                $q->where('code', $locale);
            });
        }

        if ($tag) {
            $query->whereHas('tags', function ($q) use ($tag) {
                $q->where('name', $tag);
            });
        }

        if ($search) {
            $query->where(function ($q) use ($search) {
                $q->where('key', 'ilike', "%{$search}%")
                  ->orWhere('value', 'ilike', "%{$search}%");
            });
        }

        $translations = $query->orderBy('id', 'desc')->paginate($perPage);

        return response()->json($translations);
    }

    // dev view single translation
    public function show($id)
    {
        $translation = Translation::with(['locale', 'tags'])->find($id);

        if (!$translation) {
            return response()->json(['message' => 'Translation not found'], 404);
        }

        return response()->json($translation);
    }

    // dev create new translation
    public function store(Request $request)
    {
        $validated = $request->validate([
            'key' => 'required|string|max:255',
            'value' => 'required|string',
            'locale_code' => 'required|string|exists:locales,code',
            'tags' => 'sometimes|array',
            'tags.*' => 'string|exists:tags,name',
            'meta' => 'sometimes|array',
        ]);

        $locale = \App\Models\Locale::where('code', $validated['locale_code'])->first();

        $translation = Translation::create([
            'key' => $validated['key'],
            'value' => $validated['value'],
            'locale_id' => $locale->id,
            'meta' => $validated['meta'] ?? null,
        ]);

        if (!empty($validated['tags'])) {
            $tagIds = \App\Models\Tag::whereIn('name', $validated['tags'])->pluck('id');
            $translation->tags()->sync($tagIds);
        }

        // Clear cached exports
        Cache::forget("translations_export_all");
        Cache::forget("translations_export_{$locale->code}");

        return response()->json([
            'message' => 'Translation created successfully',
            'data' => $translation->load(['locale', 'tags'])
        ], 201);
    }

    // dev update translation
    public function update(Request $request, $id)
    {
        $validated = $request->validate([
            'key' => 'sometimes|string|max:255',
            'value' => 'sometimes|string',
            'locale_code' => 'sometimes|string|exists:locales,code',
            'tags' => 'sometimes|array',
            'tags.*' => 'string|exists:tags,name',
            'meta' => 'sometimes|array',
        ]);

        $translation = Translation::find($id);

        if (!$translation) {
            return response()->json(['message' => 'Translation not found'], 404);
        }

        if (!empty($validated['locale_code'])) {
            $locale = \App\Models\Locale::where('code', $validated['locale_code'])->first();
            $translation->locale_id = $locale->id;
        }

        if (isset($validated['key'])) $translation->key = $validated['key'];
        if (isset($validated['value'])) $translation->value = $validated['value'];
        if (isset($validated['meta'])) $translation->meta = $validated['meta'];

        $translation->save();

        if (!empty($validated['tags'])) {
            $tagIds = \App\Models\Tag::whereIn('name', $validated['tags'])->pluck('id');
            $translation->tags()->sync($tagIds);
        }

        // Clear cached exports
        Cache::forget("translations_export_all");
        if (isset($locale)) {
            Cache::forget("translations_export_{$locale->code}");
        }

        return response()->json([
            'message' => 'Translation updated successfully',
            'data' => $translation->load(['locale','tags'])
        ]);
    }

    // dev delete translation
    public function destroy($id)
    {
        $translation = Translation::find($id);

        if (!$translation) {
            return response()->json(['message' => 'Translation not found'], 404);
        }

        $translation->delete();

        // Clear cached exports
        Cache::forget("translations_export_all");
        if ($translation->locale) {
            Cache::forget("translations_export_{$translation->locale->code}");
        }

        return response()->json(['message' => 'Translation deleted successfully']);
    }

    // dev export translations JSON with Redis caching
    public function export(Request $request)
    {
        $localeCode = $request->query('locale') ?? 'all';
        $cacheKey = "translations_export_{$localeCode}";

        $translations = Cache::remember($cacheKey, 600, function () use ($localeCode) {
            $query = Translation::with('locale');

            if ($localeCode !== 'all') {
                $query->whereHas('locale', function ($q) use ($localeCode) {
                    $q->where('code', $localeCode);
                });
            }

            return $query->get()->mapWithKeys(function ($t) {
                $key = $t->locale->code . '.' . $t->key;
                return [$key => $t->value];
            });
        });

        return response()->json($translations);
    }
}
