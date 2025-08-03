<?php

namespace App\Services;

use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;

class RolesService
{
    public function getPaginatedRoles($perPage = 10)
    {
        return Role::with('permissions')->paginate($perPage);
    }

    public function getAllPermissions()
    {
        return Permission::all();
    }

    public function updateRole(Role $role, array $data)
    {
        if ($role->id === 1 ) {
            throw new \Exception('Cannot edit CEO role.');
            return;
        }

        try {
            $role->update(['name' => $data['name']]);
            
            if (isset($data['permissions'])) {
                $permissions = Permission::whereIn('id', $data['permissions'])->get();
                $role->syncPermissions($permissions);
            }
            
            return $role;
        } catch (\Exception $e) {
            throw new \Exception('Error updating role: ' . $e->getMessage());
        }
    }

    public function getRole($id)
    {
        return Role::findOrFail($id);
    }

    public function deleteRole($id)
    {
        if ($id === 1) {
            throw new \Exception('Cannot delete the CEO role.');
        }
        $role = $this->getRole($id);
        return $role->delete();
    }

    public function createRole(array $data)
    {
        try {
            \DB::beginTransaction();
            
            $role = Role::create([
                'name' => $data['name'],
                'guard_name' => 'web'
            ]);

            if (!empty($data['permissions'])) {
                $permissions = Permission::whereIn('id', $data['permissions'])->get();
                $role->syncPermissions($permissions);
            }

            \DB::commit();
            return $role;
            
        } catch (\Exception $e) {
            \DB::rollBack();
            throw $e;
        }
    }
}
