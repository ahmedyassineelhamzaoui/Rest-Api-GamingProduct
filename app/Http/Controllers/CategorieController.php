<?php

namespace App\Http\Controllers;

use App\Models\Categorie;
use Illuminate\Http\Request;

class CategorieController extends Controller
{
   public function __construct()
   {
      $this->middleware('auth:api', ['except' => ['getAllCategorie']]);
   }
   /**
    * Add a new category
    *
    * @OA\Post(
    *     path="/api/add-categorie",
    *     summary="Add a new category",
    *     description="Adds a new category with the given name",
    *     tags={"Category"},
    *     @OA\RequestBody(
    *         required=true,
    *         description="The category information",
    *         @OA\JsonContent(
    *             required={"name"},
    *             @OA\Property(property="name", type="string", maxLength=100)
    *         )
    *     ),
    *     @OA\Response(
    *         response=200,
    *         description="Category created successfully",
    *         @OA\JsonContent(
    *             @OA\Property(property="status", type="string", example="success"),
    *             @OA\Property(property="message", type="string", example="Category created successfully"),
    *             @OA\Property(
    *                 property="Categorie",
    *             )
    *         )
    *     ),
    *     @OA\Response(
    *         response=409,
    *         description="Category already exists",
    *         @OA\JsonContent(
    *             @OA\Property(property="status", type="string", example="error"),
    *             @OA\Property(property="message", type="string", example="Category already exists")
    *         )
    *     )
    * )
    */
   public function addCategorie(Request $request)
   {
      $request->validate([
         'name' => 'required|string|max:100'
      ]);
      $categorie = Categorie::where('name', $request->name)->first();
      if ($categorie) {
         return response()->json([
            'status' => 'error',
            'message' => 'Category already exists'
         ], 409);
      } else {
         $categorie = Categorie::create([
            'name' => $request->name
         ]);
         return response()->json([
            'status' => 'success',
            'message' => 'categorie created successfuly',
            'Categorie' => $categorie
         ], 200);
      }
   }
}
