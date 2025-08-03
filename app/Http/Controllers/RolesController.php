<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Gate;
use App\Services\RolesService;
use Spatie\Permission\Models\Role;

class RolesController extends Controller
{
    protected $rolesService;

    public function __construct(RolesService $rolesService)
    {
        $this->rolesService = $rolesService;
    }

    public function index()
    {
        $this->authorize('viewAny', Role::class);

        $roles = $this->rolesService->getPaginatedRoles(10);
        return view('Backoffice.partials.roles', compact('roles'));
    }

    public function create()
    {
        $this->authorize('create', Role::class);
        
        $permissions = $this->rolesService->getAllPermissions();
        return view('Backoffice.partials.create-role', compact('permissions'));
    }

    public function store(Request $request)
    {
        try {
            $this->authorize('create', Role::class);

            $validated = $request->validate([
                'name' => 'required|string|max:255|unique:roles,name',
                'permissions' => 'array',
                'permissions.*' => 'exists:permissions,id'
            ]);

            $validated['permissions'] = $validated['permissions'] ?? [];

            \DB::beginTransaction();
            try {
                $role = $this->rolesService->createRole($validated);
                \DB::commit();
            } catch (\Exception $e) {
                \DB::rollBack();
                throw $e;
            }

            return response()->json([
                'success' => true,
                'message' => 'Role created successfully!'
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error creating role: ' . $e->getMessage()
            ], 500);
        }
    }

    public function edit(Role $role)
    {
        $this->authorize('update', $role);

        $permissions = $this->rolesService->getAllPermissions();
        return view('Backoffice.partials.edit-role', compact('role', 'permissions'));
    }

    public function update(Request $request, Role $role)
    {
        try {
            $this->authorize('update', $role);

            if ($role->id === 1) {
                return response()->json([
                    'success' => false,
                    'message' => 'The CEO role cannot be edited for security reasons.'
                ], 403);
            }

            $validated = $request->validate([
                'name' => 'required|string|max:255|unique:roles,name,' . $role->id,
                'permissions' => 'array',
                'permissions.*' => 'exists:permissions,id'
            ]);

            $validated['permissions'] = $validated['permissions'] ?? [];

            \DB::beginTransaction();
            try {
                $role = $this->rolesService->updateRole($role, $validated);
                \DB::commit();
            } catch (\Exception $e) {
                \DB::rollBack();
                throw $e;
            }

            return response()->json([
                'success' => true,
                'message' => 'Role updated successfully!'
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error updating role: ' . $e->getMessage()
            ], 500);
        }
    }
    public function destroy(Role $role)
    {
        $this->authorize('delete', $role);

        $this->rolesService->deleteRole($role->id);

        return response()->json([
            'success' => true,
            'message' => 'Role deleted successfully!'
        ]);
    }
}
