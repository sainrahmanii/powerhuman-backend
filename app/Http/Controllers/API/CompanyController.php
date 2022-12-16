<?php

namespace App\Http\Controllers\API;

use App\Models\Company;
use Illuminate\Http\Request;
use App\Helpers\ResponseFormatter;
use App\Http\Controllers\Controller;
use App\Http\Requests\CreateCompanyRequest;
use App\Http\Requests\UpdateCompanyRequest;
use App\Models\User;
use Exception;
use Illuminate\Support\Facades\Auth;

class CompanyController extends Controller
{
    public function fetch(Request $request)
    {
        $id = $request->input('id');
        $name = $request->input('name');
        $limit= $request->input('limit', 10);

        $companyQuery = Company::with(['users'])->whereHas('users', function ($query) {
            $query->where('user_id', Auth::id());
        });

        if ($id) {
            $company = $companyQuery->find($id);

            if ($company) {
                return ResponseFormatter::success($company);
            }

            return ResponseFormatter::error('Company Not Found', 404);
        }

        $companies = $companyQuery;

        $companies = Company::with(['users']);

        if ($name) {
            $companies->where('name', 'like', '%' . $name . '%');
        }

        return ResponseFormatter::success(
            $companies->paginate($limit),
            'Companies Found'
        );
    }

    public function create(CreateCompanyRequest  $request)
    {
        try {

            // upload logo
            if ($request->hasFile('logo')) {
                $path = $request->file('logo')->store('public/logos');
            }

            // create company
            $company = Company::create([
                'name'      => $request->name,
                'logo'      => $path,
            ]);

            if (!$company) {
                throw new Exception('Company not created');
            }

            // Attach company to user
            $user = User::find(Auth::id());
            $user->companies()->attach($company->id);

            // Load users at company
            $company->load('users');

            return ResponseFormatter::success($company, 'Company Berhasil ditambahkan');

        } catch (Exception $error) {
            //throw $th;
        }
    }

    public function update(UpdateCompanyRequest $request, $id)
    {
        try {
            $company = Company::find($id);

            if (!$company) {
                throw new Exception("Company Not Found");
            }

            if ($request->hasFile('logo')) {
                $path = $request->file('logo')->store('public/logos');
            }

            $company->update([
                'name'      => $request->name,
                'logo'      => $path
            ]);

            return ResponseFormatter::success($company, 'Company Berhasil di update');

        } catch (Exception $error) {
            return ResponseFormatter::error($error->getMessage(), 500);
        }
    }
}
