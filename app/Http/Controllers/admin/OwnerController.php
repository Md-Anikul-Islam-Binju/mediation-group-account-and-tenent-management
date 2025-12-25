<?php

namespace App\Http\Controllers\admin;

use App\Http\Controllers\Controller;
use App\Models\Owner;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Gate;
use Yoeunes\Toastr\Facades\Toastr;

class OwnerController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');

        $this->middleware(function ($request, $next) {
            if (!Gate::allows('owner-list')) {
                return redirect()->route('unauthorized.action');
            }
            return $next($request);
        })->only('index');
    }

    public function index()
    {
        $owners = Owner::latest()->get();
        return view('admin.pages.owner.index', compact('owners'));
    }

    public function store(Request $request)
    {
        try {
            $request->validate([
                'name'  => 'required|string|max:255',
                'email' => 'nullable|email|unique:owners,email',
                'phone' => 'nullable|string|unique:owners,phone',
                'agreement_paper' => 'nullable|array',
                'agreement_paper.*' => 'file|mimes:pdf,jpg,png',

            ]);

            $files = [];

            if ($request->hasFile('agreement_paper')) {
                foreach ($request->file('agreement_paper') as $file) {
                    $filename = time() . '_' . uniqid() . '.' . $file->extension();
                    $file->move(public_path('images/owner_agreements'), $filename);
                    $files[] = $filename;
                }
            }

            Owner::create([
                'name'            => $request->name,
                'email'           => $request->email,
                'phone'           => $request->phone,
                'agreement_paper' => $files,
                'status'          => 'active',
            ]);

            Toastr::success('Owner Added Successfully', 'Success');
            return redirect()->back();

        } catch (\Exception $e) {
            return redirect()->back()->with('error', $e->getMessage());
        }
    }

    public function update(Request $request, $id)
    {
        try {
            $owner = Owner::findOrFail($id);

            $request->validate([
                'name'  => 'required|string|max:255',
                'email' => 'nullable|email|unique:owners,email,' . $owner->id,
                'phone' => 'nullable|string|unique:owners,phone,' . $owner->id,
                'agreement_paper' => 'nullable|array',
                'agreement_paper.*' => 'file|mimes:pdf,jpg,png',
                'status' => 'required|in:active,inactive',
            ]);

            $files = $owner->agreement_paper ?? [];

            if ($request->hasFile('agreement_paper')) {
                foreach ($request->file('agreement_paper') as $file) {
                    $filename = time() . '_' . uniqid() . '.' . $file->extension();
                    $file->move(public_path('images/owner_agreements'), $filename);
                    $files[] = $filename;
                }
            }

            $owner->update([
                'name'            => $request->name,
                'email'           => $request->email,
                'phone'           => $request->phone,
                'agreement_paper' => $files,
                'status'          => $request->status,
            ]);

            Toastr::success('Owner Updated Successfully', 'Success');
            return redirect()->back();

        } catch (\Exception $e) {
            return redirect()->back()->with('error', $e->getMessage());
        }
    }

    public function destroy($id)
    {
        try {
            $owner = Owner::findOrFail($id);

            if (!empty($owner->agreement_paper)) {
                foreach ($owner->agreement_paper as $file) {
                    $path = public_path('images/owner_agreements/' . $file);
                    if (file_exists($path)) {
                        unlink($path);
                    }
                }
            }

            $owner->delete();

            Toastr::success('Owner Deleted Successfully', 'Success');
            return redirect()->back();

        } catch (\Exception $e) {
            return redirect()->back()->with('error', $e->getMessage());
        }
    }
}
