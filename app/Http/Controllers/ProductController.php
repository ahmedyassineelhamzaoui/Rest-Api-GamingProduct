<?php

namespace App\Http\Controllers;

use App\Models\Categorie;
use App\Models\Product;
use Illuminate\Http\Request;

class ProductController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth:api', ['except' => ['getAllProducts']]);
    }
    /**
     * Add a new product
     *
     * @OA\Post(
     *     path="/api/add-product",
     *     summary="Add a new product",
     *     description="Adds a new product with the given details",
     *     tags={"Product"},
     *     security={{"bearerAuth":{}}},
     *     @OA\RequestBody(
     *         required=true,
     *         description="The product information",
     *         @OA\JsonContent(
     *             required={"title", "description", "price", "categorie_id"},
     *             @OA\Property(property="title", type="string", maxLength=200),
     *             @OA\Property(property="description", type="string", minLength=10),
     *             @OA\Property(property="price", type="number"),
     *             @OA\Property(property="categorie_id", type="integer")
     *         )
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Product created successfully",
     *         @OA\JsonContent(
     *             @OA\Property(property="status", type="string", example="success"),
     *             @OA\Property(property="message", type="string", example="Product created successfully"),
     *             @OA\Property(
     *                 property="product",
     *                 type="object",
     *                 @OA\Property(property="id", type="integer"),
     *                 @OA\Property(property="title", type="string"),
     *                 @OA\Property(property="description", type="string"),
     *                 @OA\Property(property="price", type="number"),
     *                 @OA\Property(property="categorie_id", type="integer"),
     *                 @OA\Property(property="user_id", type="integer"),
     *                 @OA\Property(property="created_at", type="string", format="date-time"),
     *                 @OA\Property(property="updated_at", type="string", format="date-time")
     *             )
     *         )
     *     ),
     *     @OA\Response(
     *         response=400,
     *         description="Invalid request parameters",
     *         @OA\JsonContent(
     *             @OA\Property(property="message", type="string", example="The given data was invalid.")
     *         )
     *     ),
     *     @OA\Response(
     *         response=401,
     *         description="Unauthorized",
     *         @OA\JsonContent(
     *             @OA\Property(property="message", type="string", example="Unauthenticated.")
     *         )
     *     ),
     *     @OA\Response(
     *         response=404,
     *         description="Category not found",
     *         @OA\JsonContent(
     *             @OA\Property(property="status", type="string", example="error"),
     *             @OA\Property(property="message", type="string", example="Category not found")
     *         )
     *     ),
     *     @OA\Response(
     *         response=409,
     *         description="Product already exists",
     *         @OA\JsonContent(
     *             @OA\Property(property="status", type="string", example="error"),
     *             @OA\Property(property="message", type="string", example="This title of product already exists. Please enter another one.")
     *         )
     *     ),
     *     @OA\Response(
     *         response=500,
     *         description="Internal server error",
     *         @OA\JsonContent(
     *             @OA\Property(property="message", type="string", example="Internal server error.")
     *         )
     *     )
     * )
     */

    public function addProduct(Request $request)
    {
        $user = auth()->user();
        $validateData = $request->validate([
            'title'        => 'required|string|max:200',
            'description'  => 'required|min:10|string',
            'price'        => 'required|numeric',
            'categorie_id' => 'required|numeric'
        ]);
        if (Categorie::count() === 0) {
            return response()->json([
                'status' => 'error',
                'messgae' => 'you should add a categorie before adding a product'
            ]);
        }
        $product = Product::where('title', $request->title)->first();
        if ($product) {
            return response()->json([
                'message' => 'this title of product already exist please enter another one'
            ]);
        }
        $categorie = Categorie::where('id', $request->categorie_id)->first();
        if (!$categorie) {
            return response()->json([
                'status' => 'error',
                'message' => 'categorie that you entered dosn\'t exsit please enter avlaid categorie'
            ]);
        }
        $validateData['user_id'] = $user->id;
        $product = Product::create($validateData);
        return response()->json([
            'status'  => 'success',
            'message' => 'product has been created successfuly',
            'product' => $product
        ]);
    }
}
