<?php

namespace App\Http\Controllers\API;

use Exception;
use App\Helpers\ResponseFormatter;
use App\Http\Controllers\Controller;
use App\Http\Requests\CreateRoleRequest;
use App\Http\Requests\UpdateRoleRequest;
use App\Models\Role;
use Illuminate\Http\Request;

class RoleController extends Controller
{
    public function fetch(Request $request)
    {
        $id = $request->input('id');
        $name = $request->input('name');
        $limit= $request->input('limit', 10);

        $roleQuery = Role::query();

        if ($id) {
            $role = $roleQuery->find($id);

            if ($role) {
                return ResponseFormatter::success($role, 'Role Berhasil Ditemukan');
            }

            return ResponseFormatter::error('Role Not Found', 404);
        }

        $roles = $roleQuery->where('company_id', $request->company_id);

        if ($name) {
            $roles->where('name', 'like', '%' . $name . '%');
        }

        return ResponseFormatter::success(
            $roles->paginate($limit),
            'Role Found'
        );
    }

    public function create(CreateRoleRequest $request)
    {
        try {
            // Create role
            $role = Role::create([
                'name' => $request->name,
                'company_id' => $request->company_id,
            ]);

            if (!$role) {
                throw new Exception('Role not created');
            }

            return ResponseFormatter::success($role, 'Role created');
        } catch (Exception $e) {
            return ResponseFormatter::error($e->getMessage(), 500);
        }
    }

    public function update(UpdateRoleRequest $request, $id)
    {
        try {
            $role = Role::find($id);

            if (!$role) {
                throw new Exception("Role Not Found");
            }

            $role->update([
                'name'      => $request->name,
                'company_id'   => $request->company_id
            ]);

            return ResponseFormatter::success($role, 'Role Berhasil di update');

        } catch (Exception $error) {
            return ResponseFormatter::error($error->getMessage(), 500);
        }
    }

    public function destroy($id)
    {
        try {
            $role = Role::find($id);

            if (!$role) {
                throw new Exception("Role Not Found");
            }

            $role->delete();

            return ResponseFormatter::success('Role Berhasil Di Hapus');

        } catch (Exception $error) {
            return ResponseFormatter::error($error->getMessage());
        }
    }
}
