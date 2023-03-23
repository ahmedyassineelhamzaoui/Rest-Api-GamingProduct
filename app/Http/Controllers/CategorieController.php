<?php

namespace App\Http\Controllers;

use App\Models\Categorie;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Redis;

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
    *     tags={"Categorie"},
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
   /**
    * @OA\Delete(
    *     path="/api/delete-categorie",
    *     summary="Delete a categorie",
    *     description="Deletes a specific categorie based on its ID",
    *     tags={"Categorie"},
    *     @OA\Parameter(
    *         name="id",
    *         in="path",
    *         description="The ID of the categorie to be deleted",
    *         required=true,
    *         @OA\Schema(
    *             type="integer",
    *             format="int64"
    *         )
    *     ),
    *     @OA\Response(
    *         response=200,
    *         description="Categorie deleted successfully",
    *         @OA\JsonContent(
    *             @OA\Property(
    *                 property="status",
    *                 type="string",
    *                 description="The status of the response",
    *                 example="success"
    *             ),
    *             @OA\Property(
    *                 property="message",
    *                 type="string",
    *                 description="The message to be returned",
    *                 example="Categorie deleted successfully"
    *             )
    *         )
    *     ),
    *     @OA\Response(
    *         response=404,
    *         description="Categorie not found",
    *         @OA\JsonContent(
    *             @OA\Property(
    *                 property="status",
    *                 type="string",
    *                 description="The status of the response",
    *                 example="error"
    *             ),
    *             @OA\Property(
    *                 property="message",
    *                 type="string",
    *                 description="The message to be returned",
    *                 example="Categorie not found"
    *             )
    *         )
    *     )
    * )
    */

   public function deleteCategorie(Request $request)
   {
      $request->validate([
         'id'  => 'required|integer'
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
    *     summary="Update a categorie",
    *     description="Updates a specific categorie based on its ID",
    *     tags={"Categorie"},
    *     @OA\Parameter(
    *         name="id",
    *         in="query",
    *         description="The ID of the categorie to be updated",
    *         required=true,
    *         @OA\Schema(
    *             type="integer",
    *             format="int64"
    *         )
    *     ),
    *     @OA\Parameter(
    *         name="name",
    *         in="query",
    *         description="The name of the categorie",
    *         required=false,
    *         @OA\Schema(
    *             type="string"
    *         )
    *     ),
    *     @OA\Response(
    *         response=200,
    *         description="Categorie updated successfully",
    *         @OA\JsonContent(
    *             @OA\Property(
    *                 property="status",
    *                 type="string",
    *                 description="The status of the response",
    *                 example="success"
    *             ),
    *             @OA\Property(
    *                 property="message",
    *                 type="string",
    *                 description="The message to be returned",
    *                 example="categorie updated successfully"
    *             ),
    *             @OA\Property(
    *                 property="categorie",
    *                 type="object",
    *                 description="The updated categorie object",
    *             )
    *         )
    *     ),
    *     @OA\Response(
    *         response=404,
    *         description="Categorie not found",
    *         @OA\JsonContent(
    *             @OA\Property(
    *                 property="status",
    *                 type="string",
    *                 description="The status of the response",
    *                 example="error"
    *             ),
    *             @OA\Property(
    *                 property="message",
    *                 type="string",
    *                 description="The message to be returned",
    *                 example="categorie not found"
    *             )
    *         )
    *     )
    * )
    */
   public function updateCategorie(Request $request)
   {
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
}
