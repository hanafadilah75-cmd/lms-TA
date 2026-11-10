<?php

namespace App\Http\Controllers;

use App\Models\Company;
use App\Models\User;
use App\Models\ActivityLog;
use App\Http\Requests\StoreCompanyRequest;
use App\Http\Requests\UpdateCompanyRequest;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\DB;

class CompanyController extends Controller
{
    /**
     * Display a listing of companies.
     */
    public function index()
    {
        $companies = Company::withCount('users')->paginate(10);
        return view('companies.index', compact('companies'));
    }

    /**
     * Show the form for creating a new company.
     */
    public function create()
    {
        return view('companies.create');
    }

    /**
     * Store a newly created company in storage.
     */
    public function store(StoreCompanyRequest $request)
    {
        DB::transaction(function () use ($request) {
            $company = Company::create($request->validated());
            ActivityLog::record('company_created', "Perusahaan '{$company->name}' berhasil ditambahkan.");
        });

        return redirect()->route('companies.index')->with('success', 'Perusahaan berhasil ditambahkan.');
    }

    /**
     * Show the form for editing the specified company.
     */
    public function edit(Company $company)
    {
        return view('companies.edit', compact('company'));
    }

    /**
     * Update the specified company in storage.
     */
    public function update(UpdateCompanyRequest $request, Company $company)
    {
        DB::transaction(function () use ($request, $company) {
            $company->update($request->validated());
        });

        return redirect()->route('companies.index')->with('success', 'Perusahaan berhasil diperbarui.');
    }

    /**
     * Remove the specified company from storage.
     */
    public function destroy(Company $company)
    {
        DB::transaction(function () use ($company) {
            $company->delete();
        });

        return redirect()->route('companies.index')->with('success', 'Perusahaan berhasil dihapus.');
    }

    // User (Karyawan & Tutor) management methods

    /**
     * Display a listing of employees.
     */
    public function employees(Request $request)
    {
        $query = User::with('company');

        if ($request->filled('role')) {
            $query->where('role', $request->role);
        } else {
            $query->whereIn('role', ['karyawan', 'tutor']);
        }

        if ($request->filled('company_id')) {
            $query->where('company_id', $request->company_id);
        }

        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', '%' . $search . '%')
                  ->orWhere('email', 'like', '%' . $search . '%');
            });
        }

        $employees = $query->paginate(10)->withQueryString();
        $companies = Company::all();

        return view('companies.employees', compact('employees', 'companies'));
    }

    /**
     * Show form to create a new employee/tutor.
     */
    public function createEmployee()
    {
        $companies = Company::all();
        $startupCompanies = Company::where('type', 'startup')->get();
        $clientCompanies = Company::where('type', 'client')->get();
        return view('companies.create_employee', compact('companies', 'startupCompanies', 'clientCompanies'));
    }

    /**
     * Store new employee/tutor.
     */
    public function storeEmployee(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users',
            'password' => 'required|string|min:8',
            'role' => 'required|in:tutor,karyawan',
            'company_id' => 'nullable|exists:companies,id',
            'avatar' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
        ]);

        // Rule:
        // - Role Tutor -> automatically associate with platform startup entity
        // - Role Karyawan -> automatically associate with client company entity
        if ($validated['role'] === 'tutor') {
            $startup = null;
            if (!empty($validated['company_id'])) {
                $startup = Company::where('id', $validated['company_id'])->where('type', 'startup')->first();
            }
            if (!$startup) {
                $startup = Company::where('type', 'startup')->first();
            }
            if (!$startup) {
                $startup = Company::create([
                    'name' => 'EduTech Platform (Startup)',
                    'type' => 'startup',
                    'email' => 'startup@lms.com',
                ]);
            }
            $targetCompanyId = $startup->id;
        } else {
            $client = null;
            if (!empty($validated['company_id'])) {
                $client = Company::where('id', $validated['company_id'])->where('type', 'client')->first();
            }
            if (!$client) {
                $client = Company::where('type', 'client')->first();
            }
            if (!$client) {
                return back()->withInput()->withErrors(['company_id' => 'Belum ada perusahaan klien yang terdaftar.']);
            }
            $targetCompanyId = $client->id;
        }

        DB::transaction(function () use ($validated, $request, $targetCompanyId) {
            $avatarPath = null;
            if ($request->hasFile('avatar')) {
                $avatarPath = $request->file('avatar')->store('avatars', 'public');
            }

            $newUser = User::create([
                'name' => $validated['name'],
                'email' => $validated['email'],
                'password' => Hash::make($validated['password']),
                'role' => $validated['role'],
                'company_id' => $targetCompanyId,
                'avatar' => $avatarPath,
            ]);

            ActivityLog::record('user_created', "Pengguna baru '{$newUser->name}' sebagai {$newUser->role} berhasil ditambahkan.");
        });

        return redirect()->route('companies.employees')->with('success', 'User berhasil ditambahkan.');
    }

    /**
     * Show form to edit employee/tutor.
     */
    public function editEmployee(User $employee)
    {
        $companies = Company::all();
        $startupCompanies = Company::where('type', 'startup')->get();
        $clientCompanies = Company::where('type', 'client')->get();
        return view('companies.edit_employee', compact('employee', 'companies', 'startupCompanies', 'clientCompanies'));
    }

    /**
     * Update employee/tutor details.
     */
    public function updateEmployee(Request $request, User $employee)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users,email,' . $employee->id,
            'password' => 'nullable|string|min:8',
            'role' => 'required|in:tutor,karyawan',
            'company_id' => 'nullable|exists:companies,id',
            'avatar' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
        ]);

        if ($validated['role'] === 'tutor') {
            $startup = null;
            if (!empty($validated['company_id'])) {
                $startup = Company::where('id', $validated['company_id'])->where('type', 'startup')->first();
            }
            if (!$startup) {
                $startup = Company::where('type', 'startup')->first();
            }
            if (!$startup) {
                $startup = Company::create([
                    'name' => 'EduTech Platform (Startup)',
                    'type' => 'startup',
                    'email' => 'startup@lms.com',
                ]);
            }
            $targetCompanyId = $startup->id;
        } else {
            $client = null;
            if (!empty($validated['company_id'])) {
                $client = Company::where('id', $validated['company_id'])->where('type', 'client')->first();
            }
            if (!$client) {
                $client = Company::where('type', 'client')->first();
            }
            if (!$client) {
                return back()->withInput()->withErrors(['company_id' => 'Belum ada perusahaan klien yang terdaftar.']);
            }
            $targetCompanyId = $client->id;
        }

        DB::transaction(function () use ($validated, $request, $employee, $targetCompanyId) {
            $employee->name = $validated['name'];
            $employee->email = $validated['email'];
            $employee->role = $validated['role'];
            $employee->company_id = $targetCompanyId;

            if (!empty($validated['password'])) {
                $employee->password = Hash::make($validated['password']);
            }

            if ($request->hasFile('avatar')) {
                if ($employee->avatar && Storage::disk('public')->exists($employee->avatar)) {
                    Storage::disk('public')->delete($employee->avatar);
                }
                $employee->avatar = $request->file('avatar')->store('avatars', 'public');
            }

            $employee->save();
        });

        return redirect()->route('companies.employees')->with('success', 'User berhasil diperbarui.');
    }

    /**
     * Soft delete user.
     */
    public function destroyEmployee(User $employee)
    {
        DB::transaction(function () use ($employee) {
            $employee->delete();
        });

        return redirect()->route('companies.employees')->with('success', 'User berhasil dihapus.');
    }
}
