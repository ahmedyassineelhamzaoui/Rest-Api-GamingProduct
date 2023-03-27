<?php
    
namespace App\Http\Controllers;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;

    
class RoleController extends Controller
{
    
    /**
     * @OA\Get(
     *     path="/api/roles",
     *     summary="Retrieve all roles",
     *     description="Retrieves a list of all roles",
     *     tags={"Role"},
     *     security={{"bearerAuth":{}}},
     *     @OA\Response(
     *         response=200,
     *         description="Returns a list of roles",
     *         @OA\JsonContent(
     *             @OA\Property(property="status", type="string", example="success"),
     *             @OA\Property(
     *                 property="roles",
     *                 type="array",
     *                 @OA\Items(
     *                     @OA\Property(property="id", type="integer"),
     *                     @OA\Property(property="name", type="string"),
     *                     @OA\Property(property="guard_name", type="string"),
     *                     @OA\Property(property="created_at", type="string", format="date-time"),
     *                     @OA\Property(property="updated_at", type="string", format="date-time")
     *                 )
     *             )
     *         )
     *     ),
     *     @OA\Response(
     *         response=403,
     *         description="Returns an error message if the user doesn't have permission to show roles",
     *         @OA\JsonContent(
     *             @OA\Property(property="status", type="string", example="error"),
     *             @OA\Property(property="message", type="string", example="You dont have permission to show roles")
     *         )
     *     ),
     *     @OA\Response(
     *         response=401,
     *         description="Returns an error message if the user is not authenticated",
     *         @OA\JsonContent(
     *             @OA\Property(property="message", type="string", example="Unauthenticated.")
     *         )
     *     )
     * )
     */
    public function getAllRoles()
    {
        $user=auth()->user();
        if(!$user->hasPermissionTo('role-list')){
            return response()->json([
                'status' => 'error',
                'message' => 'You dont have permission to show roles'
            ], 200);
        }
        $roles = Role::all();
        return response()->json([
           'status' => 'success',
           'roles' => $roles
        ]);
    }
    /**
     *
     * @OA\Post(
     *     path="/api/add-role",
     *     summary="Add a new role",
     *     description="Adds a new role with the given name and permissions",
     *     tags={"Role"},
     *     security={{"bearerAuth":{}}},
     *     @OA\RequestBody(
     *         required=true,
     *         description="The role information",
     *         @OA\JsonContent(
     *             required={"name", "permission"},
     *             @OA\Property(property="name", type="string", maxLength=255),
     *             @OA\Property(property="permission", type="array", @OA\Items(type="string", example="permission-name"))
     *         )
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Role created successfully",
     *         @OA\JsonContent(
     *             @OA\Property(property="status", type="string", example="success"),
     *             @OA\Property(property="message", type="string", example="Role created successfully")
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
     *             @OA\Property(property="message", type="string", example="You dont have permission to add role")
     *         )
     *     ),
     *     @OA\Response(
     *         response=409,
     *         description="Role already exists",
     *         @OA\JsonContent(
     *             @OA\Property(property="status", type="string", example="error"),
     *             @OA\Property(property="message", type="string", example="This role already exists. Please enter another one.")
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
    public function addRole(Request $request)
    {
        $user=auth()->user();
        if(!$user->can('role-create')){
            return response()->json([
                'status' => 'error',
                'message' => 'You dont have permission to add role'
            ], 200);
        }
        $request->validate([
            'name' => 'required|unique:roles,name',
            'permission' => 'required',
        ]);
    
        $role = Role::create(['name' => $request->name]);
        $role->syncPermissions($request->permission);
    
        return response()->json([
            'status' => 'success',
            'message' => 'role created successfuly'
        ]);
    }
    /**
     *
     * @OA\Get(
     *     path="/api/show-role",
     *     summary="Show a role",
     *     description="Shows the details of the role with the given ID",
     *     tags={"Role"},
     *     security={{"bearerAuth":{}}},
     *     @OA\Parameter(
     *         name="id",
     *         in="query",
     *         description="The ID of the role to show",
     *         required=true,
     *         @OA\Schema(
     *             type="integer",
     *             format="int64"
     *         )
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Role details returned successfully",
     *         @OA\JsonContent(
     *             @OA\Property(property="status", type="string", example="success"),
     *             @OA\Property(property="roles", type="object"),
     *             @OA\Property(property="rolePermissions", type="string")
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
     *             @OA\Property(property="message", type="string", example="You dont have permission to show roles")
     *         )
     *     ),
     *     @OA\Response(
     *         response=404,
     *         description="Role not found",
     *         @OA\JsonContent(
     *             @OA\Property(property="status", type="string", example="error"),
     *             @OA\Property(property="message", type="string", example="The role with the given ID was not found")
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
    public function showRole(Request $request)
    {
        $user=auth()->user();
        if(!$user->hasPermissionTo('role-list')){
            return response()->json([
                'status' => 'error',
                'message' => 'You dont have permission to show roles'
            ], 200);
        }
        $request->validate([
            'id' => 'required'
        ]);
        $role = Role::find($request->id);
        $rolePermissions = Permission::join("role_has_permissions","role_has_permissions.permission_id","=","permissions.id")
            ->where("role_has_permissions.role_id",$request->id)
            ->get();
    
        return response()->json([
            'status' => 'success',
            'roles' => $role,
            'rolePermissions' => $rolePermissions
        ]);
         
    }
    /**
     *
     * @OA\Post(
     *     path="/api/update-role",
     *     summary="Update a role",
     *     description="Updates the role with the given ID, name and permissions",
     *     tags={"Role"},
     *     security={{"bearerAuth":{}}},
     *     @OA\RequestBody(
     *         required=true,
     *         description="The role information",
     *         @OA\JsonContent(
     *             required={"id", "name", "permission"},
     *             @OA\Property(property="id", type="integer", example=1),
     *             @OA\Property(property="name", type="string", maxLength=255),
     *             @OA\Property(property="permission", type="array", @OA\Items(type="string", example="permission-name"))
     *         )
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Role updated successfully",
     *         @OA\JsonContent(
     *             @OA\Property(property="status", type="string", example="success"),
     *             @OA\Property(property="message", type="string", example="Role updated successfully")
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
     *             @OA\Property(property="message", type="string", example="You dont have permission to update role")
     *         )
     *     ),
     *     @OA\Response(
     *         response=404,
     *         description="Role not found",
     *         @OA\JsonContent(
     *             @OA\Property(property="status", type="string", example="error"),
     *             @OA\Property(property="message", type="string", example="Role not found.")
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
    public function updateRole(Request $request)
    {
        $user=auth()->user();
        if(!$user->hasPermissionTo('role-edit')){
            return response()->json([
                'status' => 'error',
                'message' => 'You dont have permission to update role'
            ], 200);
        }
        $request->validate( [
            'id'   => 'required',
            'name' => 'required',
            'permission' => 'required',
        ]);
    
        $role = Role::find($request->id);
        $role->name = $request->name;
        $role->save();
    
        $role->syncPermissions($request->permission);
    
        return
        response()->json([
           'status' => 'success',
           'message' => 'Role updated successfully'
        ]);
    }
    /**
     *
     * @OA\Delete(
     *     path="/api/delete-role",
     *     summary="Delete a role",
     *     description="Deletes the role with the given ID",
     *     tags={"Role"},
     *     security={{"bearerAuth":{}}},
     *     @OA\Parameter(
     *         name="id",
     *         in="query",
     *         description="The ID of the role to delete",
     *         required=true,
     *         @OA\Schema(
     *             type="integer",
     *             format="int64"
     *         )
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Role deleted successfully",
     *         @OA\JsonContent(
     *             @OA\Property(property="status", type="string", example="success"),
     *             @OA\Property(property="message", type="string", example="Role deleted successfully")
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
     *             @OA\Property(property="message", type="string", example="You dont have permission to delete role")
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
    public function deleteRole(Request $request)
    {
        $user=auth()->user();
        if(!$user->hasPermissionTo('role-delete')){
            return response()->json([
                'status' => 'error',
                'message' => 'You dont have permission to delete role'
            ], 200);
        }
        $request->validate([
            'id' => 'required'
        ]);
        Role::where('id',$request->id)->delete();
        return response()->json([
            'status' => 'succcess',
            'message' => 'Role deleted successfully'
        ]); 
    }
}