<?php

namespace App\Http\Controllers;

use App\Models\Categorie;
use Illuminate\Http\Request;

class CategorieController extends Controller
{
   public function __construct()
   {
      $this->middleware('auth:api', ['except' => ['getAllCategories']]);
   }
   /**
      * @OA\Post(
      *     path="/api/categories",
      *     summary="Add a new category",
      *     description="Creates a new category with the given name",
      *     tags={"Category"},
      *     security={{"bearerAuth":{}}},
      *     @OA\RequestBody(
      *         required=true,
      *         @OA\JsonContent(
      *             @OA\Property(property="name", type="string", example="Category name")
      *         )
      *     ),
      *     @OA\Response(
      *         response=200,
      *         description="Category created successfully",
      *         @OA\JsonContent(
      *             @OA\Property(property="status", type="string", example="success"),
      *             @OA\Property(property="message", type="string", example="Category created successfully"),
      *             @OA\Property(property="Categorie", type="object")
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
      *         response=403,
      *         description="Forbidden",
      *         @OA\JsonContent(
      *             @OA\Property(property="status", type="string", example="error"),
      *             @OA\Property(property="message", type="string", example="You dont have permission to add categorie")
      *         )
      *     ),
      *     @OA\Response(
      *         response=409,
      *         description="Category already exists",
      *         @OA\JsonContent(
      *             @OA\Property(property="status", type="string", example="error"),
      *             @OA\Property(property="message", type="string", example="Category already exists")
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
   public function addCategorie(Request $request)
   {
      $user = auth()->user();
        if(!$user->hasPermissionTo('categorie-create')){
            return response()->json([
                'status' => 'error',
                'message' => 'You dont have permission to add categorie'
            ], 200);
        }
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
   /**
    *
    * @OA\Delete(
    *     path="/api/delete-categorie",
    *     summary="Delete a category",
    *     description="Deletes the category with the given ID",
    *     tags={"Category"},
    *     security={{"bearerAuth":{}}},
    *     @OA\Parameter(
    *         name="id",
    *         in="query",
    *         description="The ID of the category to delete",
    *         required=true,
    *         @OA\Schema(
    *             type="integer",
    *             format="int64"
    *         )
    *     ),
    *     @OA\Response(
    *         response=200,
    *         description="Category deleted successfully",
    *         @OA\JsonContent(
    *             @OA\Property(property="status", type="string", example="success"),
    *             @OA\Property(property="message", type="string", example="Category has been deleted successfully")
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
    *         response=403,
    *         description="Forbidden",
    *         @OA\JsonContent(
    *             @OA\Property(property="status", type="string", example="error"),
    *             @OA\Property(property="message", type="string", example="You dont have permission to delete categorie")
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
    *         response=500,
    *         description="Internal server error",
    *         @OA\JsonContent(
    *             @OA\Property(property="message", type="string", example="Internal server error.")
    *         )
    *     )
    * )
   */
   public function deleteCategorie(Request $request)
   {
      $user = auth()->user();
        if(!$user->hasPermissionTo('categorie-delete')){
            return response()->json([
                'status' => 'error',
                'message' => 'You dont have permission to delete categorie'
            ], 200);
        }
      $request->validate([
         'id'  => 'required|integer',
      ]);
      $categorie = Categorie::find($request->id);
      if ($categorie) {
         $categorie->delete();
         return response()->json([
            'status' => 'success',
            'message' => 'categorie has been deleted successfuly'
         ], 200);
      } else {
         return response()->json([
            'status'  => 'error',
            'message' => 'categorie not found'
         ], 404);
      }
   }
   /**
    * @OA\Put(
    *     path="/api/update-categorie",
    *     summary="Update a category",
    *     description="Updates the category with the given ID",
    *     tags={"Category"},
    *     security={{"bearerAuth":{}}},
    *     @OA\Parameter(
    *         name="id",
    *         in="query",
    *         description="The ID of the category to update",
    *         required=true,
    *         @OA\Schema(
    *             type="integer",
    *             format="int64"
    *         )
    *     ),
    *     @OA\Parameter(
    *         name="name",
    *         in="query",
    *         description="The name of the category",
    *         required=false,
    *         @OA\Schema(
    *             type="string"
    *         )
    *     ),
    *     @OA\Response(
    *         response=200,
    *         description="Category updated successfully",
    *         @OA\JsonContent(
    *             @OA\Property(property="status", type="string", example="success"),
    *             @OA\Property(property="message", type="string", example="Category updated successfully"),
    *             @OA\Property(property="categorie", type="object")
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
    *         response=403,
    *         description="Forbidden",
    *         @OA\JsonContent(
    *             @OA\Property(property="status", type="string", example="error"),
    *             @OA\Property(property="message", type="string", example="You dont have permission to update product")
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
    *         response=500,
    *         description="Internal server error",
    *         @OA\JsonContent(
    *             @OA\Property(property="message", type="string", example="Internal server error.")
    *         )
    *     )
    * )
   */
   public function updateCategorie(Request $request)
   {
      $user = auth()->user();
        if(!$user->hasPermissionTo('categorie-update')){
            return response()->json([
                'status' => 'error',
                'message' => 'You dont have permission to update product'
            ], 200);
        }
      $request->validate([
         'id' => 'required|integer '
      ]);
      $categorie = Categorie::find($request->id);
      if ($categorie) {
         if ($request->has('name')) {
            $categorie->name = $request->name;
         }
         $categorie->save();
         return response()->json([
            'status' => 'success',
            'message' => 'categorie updated successfuly',
            'categorie' => $categorie
         ], 200);
      } else {
         return response()->json([
            'status' => 'error',
            'message' => 'categorie not found'
         ], 404);
      }
   }
   /**
    * @OA\Get(
    *     path="/api/categories",
    *     summary="Get all categories",
    *     description="Retrieve all available categories.",
    *     tags={"Category"},
    *     @OA\Response(
    *         response="200",
    *         description="Categories retrieved successfully.",
    *         @OA\JsonContent(
    *             @OA\Property(property="status", type="string", example="success"),
    *             @OA\Property(
    *                 property="categories",
    *                 type="array",
    *                 @OA\Items(
    *                     @OA\Property(property="id", type="integer", example="1"),
    *                     @OA\Property(property="name", type="string", example="Category 1")
    *                 )
    *             )
    *         )
    *     ),
    *     @OA\Response(
    *         response="404",
    *         description="No categories available.",
    *         @OA\JsonContent(
    *             @OA\Property(property="message", type="string", example="No categories available.")
    *         )
    *     ),
    *     @OA\Response(
    *         response="500",
    *         description="Internal server error.",
    *         @OA\JsonContent(
    *             @OA\Property(property="message", type="string", example="Internal server error.")
    *         )
    *     )
    * )
   */
   public function getAllCategories()
   {
      $categories = Categorie::all('id', 'name');
      if ($categories->count() > 0) {
         return response()->json([
            'status' => 'success',
            'categories' => $categories
         ]);
      } else {
         return response()->json([
            'message' => 'no ctegorie available'
         ]);
      }
   }
}
