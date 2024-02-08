<?php

namespace App\Http\Controllers;

use App\Enums\Product\Filter;
use App\Http\Requests\StoreProductRequest;
use App\Http\Requests\UpdateProductRequest;
use App\Http\Resources\ExpirationDatesResource;
use App\Http\Resources\ProductResource;
use App\Models\ExpirationDate;
use App\Models\Product;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Spatie\FlareClient\Http\Exceptions\NotFound;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

class ProductController extends Controller
{
    public function __construct()
    {
        $this->authorizeResource(Product::class, 'product');
    }

    public function index(Request $request): AnonymousResourceCollection|JsonResponse
    {
        $user = Auth::user();

        if (!$request->has('filter')) {
            return ProductResource::collection($user->products()->groupedByMinExpirationDate()->orderedBy('closest_expiration_date')->get());
        } else if (array_key_exists('category', $request->get('filter'))) {
            $category = $request->get('filter')['category'];

            return match ($request->get('filter')['category']) {
                Filter::WEEK->value => ProductResource::collection($user->products()->notFinished()->week()->get()),
                Filter::MONTH->value => ProductResource::collection($user->products()->notFinished()->month()->get()),
                Filter::YEARS->value => ProductResource::collection($user->products()->notFinished()->years()->get()),
                Filter::FINISHED->value => ProductResource::collection($user->products()->finished()->orderedBy('finished_at', false)->get()),
                Filter::TO_PURCHASE->value => ProductResource::collection($user->products()->toPurchase()->orderedBy('added_to_purchase_list_at')->get()),
                default => response()->json(['Filter value unknown'], 400)
            };
        }

        return response()->json(['Filter unknown'], 400);
    }

    public function store(StoreProductRequest $request): ProductResource
    {
        $product = Product::make($request->validated());

        DB::transaction(function () use ($product, $request) {
            $product->save();

            $expirationDates = $request->validated()['expiration_dates'];

            if (!empty($expirationDates) and !empty($expirationDates[0])) {
                foreach ($expirationDates as $expirationDate) {
                    ExpirationDate::create(['product_id' => $product->id, 'date' => $expirationDate['date']]);
                }
            }
        });

        return ProductResource::make($product->load('expirationDates'));
    }

    public function show(Request $request, Product $product): ProductResource
    {
        return ProductResource::make($product->load('expirationDates'));
    }

    public function update(UpdateProductRequest $request, Product $product): ProductResource
    {
        $product->update($request->validated());

        return ProductResource::make($product);
    }

    public function destroy(Product $product): Response
    {
        $product->delete();

        return response()->noContent();
    }
}
