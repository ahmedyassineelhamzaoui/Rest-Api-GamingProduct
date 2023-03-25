<?php
    
namespace App\Http\Controllers;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;

    
class RoleController extends Controller
{
    
    function __construct()
    {
         $this->middleware('permission:role-list|role-create|role-edit|role-delete', ['only' => ['index','store']]);
         $this->middleware('permission:role-create', ['only' => ['store']]);
         $this->middleware('permission:role-edit', ['only' => ['update']]);
         $this->middleware('permission:role-delete', ['only' => ['destroy']]);
    }
    
    
    public function index()
    {
        $roles = Role::orderBy('id','DESC');
        return response()->json([
           'status' => 'success',
           'roles' => $roles
        ]);
    }
    
 
    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|unique:roles,name',
            'permission' => 'required',
        ]);
    
        $role = Role::create(['name' => $request->name]);
        $role->syncPermissions($request->input('permission'));
    
        return response()->json([
            'status' => 'success',
            'message' => 'role created successfuly'
        ]);
    }
    
    public function show(Request $request)
    {
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
    
    
   
    public function update(Request $request)
    {
        $this->validate($request, [
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
   
    public function destroy(Request $request)
    {

        Role::where('id',$request->id)->delete();
        return response()->json([
            'status' => 'succcess',
            'message' => 'Role deleted successfully'
        ]); 
    }
}