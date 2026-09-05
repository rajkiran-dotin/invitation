<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\InvitationEnquiry;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class EnquiryController extends Controller
{
    public function index(): View
    {
        return view('admin.enquiries.index', [
            'enquiries' => InvitationEnquiry::query()->latest()->paginate(15),
        ]);
    }

    public function update(Request $request, InvitationEnquiry $enquiry): RedirectResponse
    {
        $enquiry->update($request->validate([
            'status' => ['required', 'in:new,contacted,converted,closed'],
        ]));

        return back()->with('status', 'Enquiry status updated.');
    }

    public function destroy(InvitationEnquiry $enquiry): RedirectResponse
    {
        $enquiry->delete();

        return back()->with('status', 'Enquiry deleted.');
    }
}
