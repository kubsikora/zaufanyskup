<?php

namespace App\Http\Controllers;

use App\Models\Product;
use Crypt;
use DB;
use Illuminate\Http\Request;

class ProductController extends Controller
{
    public function index(Request $request)
    {
        $query = Product::query();

        if ($request->filled('name')) {
            $query->where('name', 'like', '%' . $request->name . '%');
        }

        if ($request->filled('prodCode')) {
            $query->where('prodCode', $request->prodCode);
        }

        if ($request->filled('price_min')) {
            $query->where('price', '>=', $request->price_min);
        }

        if ($request->filled('price_max')) {
            $query->where('price', '<=', $request->price_max);
        }

        if ($request->filled('type')) {
            $query->where('type', $request->type);
        }

        $uniqueTypes = Product::whereNotNull('type')
            ->distinct()
            ->pluck('type');

        $uniqueTypes = Product::whereNotNull('type')
            ->distinct()
            ->pluck('type');


        $perPage = $request->get('per_page', 15);
        $page = $request->get('page', 1);

        $products = $query->with('photos')
            ->orderBy('id', 'desc')
            ->paginate($perPage, ['*'], 'page', $page);

        return response()->json([
            'data' => $products->items(),
            'total' => $products->total(),
            'current_page' => $products->currentPage(),
            'last_page' => $products->lastPage(),
            'per_page' => $products->perPage(),
            'unique_types' => $uniqueTypes,
        ]);
    }
    public function store(Request $request)
    {
        $data = $request->all();

        if ($request->has('active')) {
            $data['active'] = filter_var($request->active, FILTER_VALIDATE_BOOLEAN);
            $data['addeb_by'] = auth()->user()->id;
        }

        $product = Product::create($data);

        if ($request->hasFile('photos')) {
            foreach ($request->file('photos') as $photo) {
                $path = $photo->store('products', 'public');
                $product->photos()->create([
                    'path' => $path,
                    'addeb_by' => auth()->id(),
                ]);
            }
        }

        return response()->json($product->load('photos'), 201);
    }

    public function update(Request $request)
    {

        $product = Product::findOrFail($request->id);


        $data = $request->all();
        if ($request->has('active')) {
            $data['active'] = filter_var($request->active, FILTER_VALIDATE_BOOLEAN);
        }


        $product->update(collect($data)->except(['photos', 'id'])->toArray());


        if ($request->hasFile('photos')) {
            foreach ($request->file('photos') as $photo) {
                if ($photo->isValid()) {
                    $path = $photo->store('products', 'public');

                    $product->photos()->create([
                        'path' => $path,
                        'addeb_by' => auth()->id(),
                    ]);
                }
            }
        }

        return response()->json($product->load('photos'), 200);
    }

    public function destroy(Request $request)
    {
        $id = $request->input('id');

        $product = Product::where('id', $id)->delete();
        return response()->json('done');
    }

    public function destroyPicture(Request $request)
    {
        $path = $request->input('path');
        DB::table('product_photos')->where('path', $path)->delete();
        return;
    }

    public function get(Request $request)
    {
        $cookieToken = $request->cookie('form_verify_token');

        if (!$cookieToken) {
            return response()->json(['error' => 'Brak lub wygasłe ciasteczko bezpieczeństwa.'], 403);
        }

        try {
            $expiresAt = Crypt::decryptString($cookieToken);
            if (time() > (int) $expiresAt) {
                return response()->json(['error' => 'Token w ciasteczku wygasł.'], 403);
            }
        } catch (\Exception $e) {
            return response()->json(['error' => 'Błędne ciasteczko.'], 403);
        }

        $products = Product::with('photos')->where('active', 1)->get();

        $formattedData = $products->map(function ($product) {
            return [
                'id' => $product->id,
                'title' => $product->name,
                'location' => $product->prodCode, 
                'cost' => (float) $product->price,
                'description' => $product->description,
                'photos' => $product->photos->pluck('path')->toArray(),
                'date' => $product->created_at->format('Y-m-d'),
                'type' => $product->type,
            ];
        });

        return response()->json($formattedData);
    }
}
