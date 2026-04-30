<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Employee;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rules\Password;
use Illuminate\View\View;

class EmployeeController extends Controller
{
    public function index(Request $request): View
    {
        $query = Employee::latest();

        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('first_name',  'like', "%{$search}%")
                  ->orWhere('last_name',  'like', "%{$search}%")
                  ->orWhere('username',   'like', "%{$search}%")
                  ->orWhere('position',   'like', "%{$search}%")
                  ->orWhere('contact_num','like', "%{$search}%");
            });
        }

        $employees = $query->paginate(10)->withQueryString();

        return view('admin.employees.index', compact('employees'));
    }

    public function store(Request $request): RedirectResponse
    {
        $request->validate([
            'first_name'  => ['required', 'string', 'max:50'],
            'last_name'   => ['required', 'string', 'max:50'],
            'position'    => ['required', 'string', 'max:50'],
            'contact_num' => ['required', 'string', 'max:13'],
            'username'    => ['required', 'string', 'max:50', 'unique:employees,username'],
            'password'    => ['required', 'confirmed', Password::min(8)],
            'role'        => ['required', 'in:owner,staff'],
        ], [
            'username.unique'      => 'This username is already taken.',
            'password.confirmed'   => 'Password confirmation does not match.',
            'password.min'         => 'Password must be at least 8 characters.',
        ]);

        Employee::create([
            'first_name'  => $request->first_name,
            'last_name'   => $request->last_name,
            'position'    => $request->position,
            'contact_num' => $request->contact_num,
            'username'    => $request->username,
            'password'    => Hash::make($request->password),
            'role'        => $request->role,
        ]);

        return redirect()->route('admin.employees.index')
            ->with('success', 'Employee account created successfully.');
    }

    public function update(Request $request, Employee $employee): RedirectResponse
    {
        $request->validate([
            'first_name'  => ['required', 'string', 'max:50'],
            'last_name'   => ['required', 'string', 'max:50'],
            'position'    => ['required', 'string', 'max:50'],
            'contact_num' => ['required', 'string', 'max:13'],
            'username'    => ['required', 'string', 'max:50', 'unique:employees,username,' . $employee->id],
            'role'        => ['required', 'in:owner,staff'],
            // Password is optional on update — only validate if provided
            'password'    => ['nullable', 'confirmed', Password::min(8)],
        ], [
            'username.unique'    => 'This username is already taken.',
            'password.confirmed' => 'Password confirmation does not match.',
        ]);

        $data = [
            'first_name'  => $request->first_name,
            'last_name'   => $request->last_name,
            'position'    => $request->position,
            'contact_num' => $request->contact_num,
            'username'    => $request->username,
            'role'        => $request->role,
        ];

        // Only update password if a new one was entered
        if ($request->filled('password')) {
            $data['password'] = Hash::make($request->password);
        }

        $employee->update($data);

        return redirect()->route('admin.employees.index')
            ->with('success', 'Employee updated successfully.');
    }

    public function destroy(Employee $employee): RedirectResponse
    {
        // Prevent owner from deleting their own account
        if ($employee->id === Auth::guard('employee')->id()) {
            return redirect()->route('admin.employees.index')
                ->with('error', 'You cannot delete your own account.');
        }

        $employee->delete();

        return redirect()->route('admin.employees.index')
            ->with('success', 'Employee account deleted.');
    }
}