<?php

namespace App\Http\Controllers;

use App\Models\Product;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class ProductController extends Controller
{
    public function __construct(Product $product)
    {
        $this->product = $product;

        $this->middleware('auth:api', ['except' => ['index']]);
        $this->middleware('auth.admin', ['except' => ['index']]);
    }
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $products = $this->product->all();

        return response()->json($products, 200);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $request->validate($this->product->rules(), $this->product->feedback());

        $image = $request->file('image');

        $image_urn = $image->store('images/product/banner', 'public');

        $product = $this->product->create([
            'name' => $request->name,
            'desc' => $request->desc,
            'price' => $request->price,
            'amount' => $request->amount,
            'image' => $image_urn,
        ]);

        return response()->json($product, 201);
    }

    /**
     * Display the specified resource.
     */
    public function show($id)
    {
        $product = $this->product->find($id);

        if ($product === null) {
            return response()->json(['error' => 'Recurso solicitado não existe'], 404);
        }

        return response()->json($product, 200);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, $id)
    {
        $product = $this->product->find($id);

        if ($product === null) {
            return response()->json(['error' => 'Impossível realizar a atualização. O recurso solicitado não existe'], 404);
        }

        $updateRules = $product->rules();
        $updateRules['name'] = 'required|min:3';

        if ($request->file('image')) {
            Storage::disk('public')->delete($product->image);

            $image = $request->file('image');
            $image_urn = $image->store('images/product/banner', 'public');
        } else {
            $image_urn = $product->image;
            $updateRules['image'] = '';
        }
        
        $request->validate($updateRules, $product->feedback());
        
        $product->update([
            'name' => $request->name,
            'desc' => $request->desc,
            'price' => $request->price,
            'amount' => $request->amount,
            'image' => $image_urn,
        ]);

        return response()->json($product, 200);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy($id)
    {
        $product = $this->product->find($id);

        if ($product === null) {
            return response()->json(['error' => 'Impossível realizar a exclusão. O recurso solicitado não existe'], 404);
        }

        if ($product->image) {
            Storage::disk('public')->delete($product->image);
        }

        $product->delete();

        return response()->json(['msg' => 'Produto removido com sucesso!'], 200);
    }
}
