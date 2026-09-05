<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\InvitationEnquiry;
use App\Models\InvitationTemplate;
use App\Models\PricingPlan;
use Illuminate\View\View;

class DashboardController extends Controller
{
    public function __invoke(): View
    {
        return view('admin.dashboard', [
            'newEnquiries' => InvitationEnquiry::query()->where('status', 'new')->count(),
            'totalEnquiries' => InvitationEnquiry::count(),
            'templates' => InvitationTemplate::count(),
            'plans' => PricingPlan::count(),
            'recentEnquiries' => InvitationEnquiry::latest()->take(6)->get(),
        ]);
    }
}
