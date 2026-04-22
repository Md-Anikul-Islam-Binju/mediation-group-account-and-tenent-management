<?php

namespace App\Http\Controllers\admin;

use App\Http\Controllers\Controller;
use App\Models\Tenant;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Gate;
use Yoeunes\Toastr\Facades\Toastr;

class TenantController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');

        $this->middleware(function ($request, $next) {
            if (!Gate::allows('tenant-list')) {
                return redirect()->route('unauthorized.action');
            }
            return $next($request);
        })->only('index');
    }

    public function index()
    {
        $tenants = Tenant::latest()->get();
        return view('admin.pages.tenant.index', compact('tenants'));
    }

    public function store(Request $request)
    {
        try {
            $request->validate([
                'name'  => 'required|string|max:255',
                'email' => 'nullable|email|unique:tenants,email',
                'phone' => 'nullable|string|unique:tenants,phone',
                'agreement_paper' => 'nullable|array',
                'agreement_paper.*' => 'file|mimes:pdf,jpg,png',

            ]);

            $files = [];

            if ($request->hasFile('agreement_paper')) {
                foreach ($request->file('agreement_paper') as $file) {
                    $filename = time() . '_' . uniqid() . '.' . $file->extension();
                    $file->move(public_path('images/tenant_agreements'), $filename);
                    $files[] = $filename;
                }
            }

            Tenant::create([
                'name'            => $request->name,
                'email'           => $request->email,
                'phone'           => $request->phone,
                'organization'    => $request->organization,
                'agreement_paper' => $files,
                'account_mode'    => $request->account_mode,
                'status'          => 'active',
            ]);

            Toastr::success('Tenant Added Successfully', 'Success');
            return redirect()->back();

        } catch (\Exception $e) {
            return redirect()->back()->with('error', $e->getMessage());
        }
    }

    public function update(Request $request, $id)
    {
        try {
            $tenant = Tenant::findOrFail($id);

            $request->validate([
                'name'  => 'required|string|max:255',
                'email' => 'nullable|email|unique:tenants,email,' . $tenant->id,
                'phone' => 'nullable|string|unique:tenants,phone,' . $tenant->id,
                'agreement_paper' => 'nullable|array',
                'agreement_paper.*' => 'file|mimes:pdf,jpg,png',
                'status' => 'required|in:active,inactive',
            ]);

            $files = $tenant->agreement_paper ?? [];

            if ($request->hasFile('agreement_paper')) {
                foreach ($request->file('agreement_paper') as $file) {
                    $filename = time() . '_' . uniqid() . '.' . $file->extension();
                    $file->move(public_path('images/tenant_agreements'), $filename);
                    $files[] = $filename;
                }
            }

            $tenant->update([
                'name'            => $request->name,
                'email'           => $request->email,
                'phone'           => $request->phone,
                'account_mode'    => $request->account_mode,
                'organization'    => $request->organization,
                'agreement_paper' => $files,
                'status'          => $request->status,
            ]);

            Toastr::success('Tenant Updated Successfully', 'Success');
            return redirect()->back();

        } catch (\Exception $e) {
            return redirect()->back()->with('error', $e->getMessage());
        }
    }

    public function destroy($id)
    {
        try {
            $tenant = Tenant::findOrFail($id);

            if (!empty($tenant->agreement_paper)) {
                foreach ($tenant->agreement_paper as $file) {
                    $path = public_path('images/tenant_agreements/' . $file);
                    if (file_exists($path)) {
                        unlink($path);
                    }
                }
            }

            $tenant->delete();

            Toastr::success('Tenant Deleted Successfully', 'Success');
            return redirect()->back();

        } catch (\Exception $e) {
            return redirect()->back()->with('error', $e->getMessage());
        }
    }
}
