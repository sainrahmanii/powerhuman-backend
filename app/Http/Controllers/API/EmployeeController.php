<?php

namespace App\Http\Controllers\API;

use Exception;
use App\Helpers\ResponseFormatter;
use App\Http\Controllers\Controller;
use App\Http\Requests\CreateEmployeeRequest;
use App\Http\Requests\UpdateEmployeeRequest;
use App\Models\Employee;
use Illuminate\Http\Request;

class EmployeeController extends Controller
{
    public function fetch(Request $request)
    {
        $id = $request->input('id');
        $name = $request->input('name');
        $email = $request->input('email');
        $age = $request->input('age');
        $phone = $request->input('phone');
        $team_id = $request->input('team_id');
        $role_id = $request->input('role_id');
        $company_id = $request->input('company_id');
        $limit= $request->input('limit', 10);

        $employeeQuery = Employee::with('team', 'role');

        if ($id) {
            $employee = $employeeQuery->with(['team', 'role'])->find($id);

            if ($employee) {
                return ResponseFormatter::success($employee, 'Employee Berhasil Ditemukan');
            }

            return ResponseFormatter::error('Employee Not Found', 404);
        }

        $employees = $employeeQuery;

        if ($name) {
            $employees->where('name', 'like', '%' . $name . '%');
        }

        if ($email) {
            $employees->where('email', $email);
        }

        if ($age) {
            $employees->where('age', $age);
        }

        if ($phone) {
            $employees->where('phone', 'like', '%' . $phone . '%');
        }

        if ($team_id) {
            $employees->where('team_id', $team_id);
        }

        if ($role_id) {
            $employees->where('role_id', $role_id);
        }

        if ($company_id) {
            $employees->whereHas('team', function($query) use ($company_id) {
                $query->where('company_id', $company_id);
            });
        }

        return ResponseFormatter::success(
            $employees->paginate($limit),
            'Employee Found'
        );
    }

    public function create(CreateEmployeeRequest $request)
    {
        try {
            // Upload photo
            if ($request->hasFile('photo')) {
                $path = $request->file('photo')->store('public/photos');
            }

            // Create employee
            $employee = Employee::create([
                'name' => $request->name,
                'email' => $request->email,
                'gender' => $request->gender,
                'age' => $request->age,
                'phone' => $request->phone,
                'photo' => isset($path) ? $path : '',
                'team_id' => $request->team_id,
                'role_id' => $request->role_id
            ]);

            if (!$employee) {
                throw new Exception('Employee not created');
            }

            return ResponseFormatter::success($employee, 'Employee created');
        } catch (Exception $e) {
            return ResponseFormatter::error($e->getMessage(), 500);
        }
    }

    public function update(UpdateEmployeeRequest $request, $id)
    {
        try {
            $employee = Employee::find($id);

            if (!$employee) {
                throw new Exception("Employee Not Found");
            }

            if ($request->hasFile('photo')) {
                $path = $request->file('photo')->store('public/photos');
            }

            $employee->update([
                'name'      => $request->name,
                'name' => $request->name,
                'email' => $request->email,
                'gender' => $request->gender,
                'age' => $request->age,
                'phone' => $request->phone,
                'photo'      => isset($path) ? $path : $employee->photo, // pengkondisian jika ada tidak ada yang diperbarui maka pakai icon sebelumnya
                'team_id' => $request->team_id,
                'role_id' => $request->role_id
            ]);

            return ResponseFormatter::success($employee, 'Employee Berhasil di update');

        } catch (Exception $error) {
            return ResponseFormatter::error($error->getMessage(), 500);
        }
    }

    public function destroy($id)
    {
        try {
            $employee = Employee::find($id);

            if (!$employee) {
                throw new Exception("Employee Not Found");
            }

            $employee->delete();

            return ResponseFormatter::success('Employee Berhasil Di Hapus');

        } catch (Exception $error) {
            return ResponseFormatter::error($error->getMessage());
        }
    }
}
