<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Customer;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class CustomerController extends Controller
{
    public function index(Request $request): View
    {
        $query = Customer::orderBy('created_at', 'desc')->orderBy('id', 'desc');

        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('first_name',  'like', "%{$search}%")
                  ->orWhere('last_name',  'like', "%{$search}%")
                  ->orWhere('address',    'like', "%{$search}%")
                  ->orWhere('contact_num','like', "%{$search}%");
            });
        }

        $customers = $query->paginate(10)->withQueryString();

        return view('admin.customers.index', compact('customers'));
    }

    public function store(Request $request): RedirectResponse
    {
        $request->validate([
            'first_name'  => ['required', 'string', 'max:50'],
            'last_name'   => ['required', 'string', 'max:50'],
            'address'     => ['required', 'string', 'max:100'],
            'contact_num' => ['required', 'string', 'max:13'],
        ]);

        Customer::create($request->only('first_name', 'last_name', 'address', 'contact_num'));

        return redirect()->route('admin.customers.index')
            ->with('success', 'Customer added successfully.');
    }

    public function update(Request $request, Customer $customer): RedirectResponse
    {
        $request->validate([
            'first_name'  => ['required', 'string', 'max:50'],
            'last_name'   => ['required', 'string', 'max:50'],
            'address'     => ['required', 'string', 'max:100'],
            'contact_num' => ['required', 'string', 'max:13'],
        ]);

        $customer->update($request->only('first_name', 'last_name', 'address', 'contact_num'));

        return redirect()->route('admin.customers.index')
            ->with('success', 'Customer updated successfully.');
    }

    public function destroy(Customer $customer): RedirectResponse
    {
        // Prevent deletion if customer has orders
        if ($customer->orders()->exists()) {
            return redirect()->route('admin.customers.index')
                ->with('error', 'Cannot delete a customer that has existing orders.');
        }

        $customer->delete();

        return redirect()->route('admin.customers.index')
            ->with('success', 'Customer deleted.');
    }
}