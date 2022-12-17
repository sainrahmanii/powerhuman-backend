<?php

namespace App\Http\Controllers\API;

use Exception;
use App\Helpers\ResponseFormatter;
use App\Http\Controllers\Controller;
use App\Http\Requests\CreateResponsibilityRequest;
use App\Models\Responsibility;
use Illuminate\Http\Request;

class ResponsibilityController extends Controller
{
    public function fetch(Request $request)
    {
        $id = $request->input('id');
        $name = $request->input('name');
        $limit= $request->input('limit', 10);

        $responsibilityQuery = Responsibility::query();

        if ($id) {
            $responsibility = $responsibilityQuery->find($id);

            if ($responsibility) {
                return ResponseFormatter::success($responsibility, 'Responsibility Berhasil Ditemukan');
            }

            return ResponseFormatter::error('Responsibility Not Found', 404);
        }

        $responsibilities = $responsibilityQuery->where('role_id', $request->role_id);

        if ($name) {
            $responsibilities->where('name', 'like', '%' . $name . '%');
        }

        return ResponseFormatter::success(
            $responsibilities->paginate($limit),
            'Responsibility Found'
        );
    }

    public function create(CreateResponsibilityRequest $request)
    {
        try {
            // Create responsibility
            $responsibility = Responsibility::create([
                'name' => $request->name,
                'role_id' => $request->role_id,
            ]);

            if (!$responsibility) {
                throw new Exception('Responsibility not created');
            }

            return ResponseFormatter::success($responsibility, 'Responsibility created');
        } catch (Exception $e) {
            return ResponseFormatter::error($e->getMessage(), 500);
        }
    }

    public function destroy($id)
    {
        try {
            $responsibility = Responsibility::find($id);

            if (!$responsibility) {
                throw new Exception("Responsibility Not Found");
            }

            $responsibility->delete();

            return ResponseFormatter::success('Responsibility Berhasil Di Hapus');

        } catch (Exception $error) {
            return ResponseFormatter::error($error->getMessage(), 500);
        }
    }
}
