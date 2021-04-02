<?php

namespace App\Http\Controllers;

use App\Http\Requests\ProductFilterRequest;
use App\Http\Requests\StoreProductRequest;
use App\Models\Product;
use App\Models\ProductVariant;
use App\Models\ProductVariantPrice;
use App\Models\Variant;
use App\Traits\UploadFile;
use Illuminate\Http\Request;

class ProductController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Contracts\Foundation\Application|\Illuminate\Contracts\View\Factory|\Illuminate\Http\Response|\Illuminate\View\View
     */
    public function index()
    {
        return view('products.index');
    }

    public function productFilter(ProductFilterRequest $request)
    {
        $product = Product::with(['productVariants', 'variantPrice'])
            ->whereDate('created_at',$request->date)
            ->whereHas('variantPrice', function ($query) use ($request){
                $query->whereBetween('price',[$request->price_from, $request->price_to]);
            })
            ->get();

        return view('product.index',compact('product'));
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Contracts\Foundation\Application|\Illuminate\Contracts\View\Factory|\Illuminate\Http\Response|\Illuminate\View\View
     */
    public function create()
    {
        $variants = Variant::all();
        return view('products.create', compact('variants'));
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param \Illuminate\Http\Request $request
     * @return \Illuminate\Http\RedirectResponse
     */
    public function store(StoreProductRequest $request)
    {
            $product = Product::create($request->only(['title','sku', 'description']));

            foreach ( $request->images  as $image){
                $file_path = UploadFile::uploadFile($image->file);
                $product->productImages()->create([
                   'file_path' => 'storage/'.$file_path,
                    'thumbnail' => $image->thumbnail
                ]);
            }

            foreach ($request->product_variants as $variant){
                $product->productVariants()->create([
                   'variant' => $variant->variant,
                   'variant_id' => $variant->variant_id,
                ]);
            }

            foreach ($request->product_variant_prices as $variant_price){
                $product->productVariantPrices()->create([
                   'price'  => $variant_price->price,
                   'stock'  => $variant_price->stock,
                ]);
            }

            return redirect()->intended(route('product.create'))->with(['message' => "Product Created Successfully"]);
    }


    /**
     * Display the specified resource.
     *
     * @param \App\Models\Product $product
     * @return \Illuminate\Http\Response
     */
    public function show($product)
    {

    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param \App\Models\Product $product
     * @return \Illuminate\Http\Response
     */
    public function edit(Product $product)
    {
        $variants = Variant::all();
        $product->load(['productVariants', 'productImages','productVariantPrices']);
        return view('products.edit', compact('variants','product'));
    }

    /**
     * Update the specified resource in storage.
     *
     * @param \Illuminate\Http\Request $request
     * @param \App\Models\Product $product
     * @return \Illuminate\Http\RedirectResponse
     */
    public function update(Request $request, Product $product)
    {
        $product = $product->update($request->only(['title','sku', 'description']));

        foreach ( $request->images  as $image){
            $file_path = UploadFile::uploadFile($image->file);
            $product->productImages()->update([
                'file_path' => 'storage/'.$file_path,
                'thumbnail' => $image->thumbnail
            ]);
        }

        foreach ($request->product_variants as $variant){
            $product->productVariants()->update([
                'variant' => $variant->variant,
                'variant_id' => $variant->variant_id,
            ]);
        }

        foreach ($request->product_variant_prices as $variant_price){
            $product->productVariantPrices()->update([
                'price'  => $variant_price->price,
                'stock'  => $variant_price->stock,
            ]);
        }

        return redirect()->intended(route('product.edit'))->with(['message' => "Product Created Successfully"]);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param \App\Models\Product $product
     * @return \Illuminate\Http\Response
     */
    public function destroy(Product $product)
    {
        //
    }
}
