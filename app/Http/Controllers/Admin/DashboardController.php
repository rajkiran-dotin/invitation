<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\InvitationEnquiry;
use App\Models\InvitationTemplate;
use App\Models\Purchase;
use App\Models\PricingPlan;
use App\Models\SiteVisit;
use App\Models\User;
use Carbon\CarbonPeriod;
use Illuminate\View\View;

class DashboardController extends Controller
{
    public function __invoke(): View
    {
        $today = today();
        $paidStatus = Purchase::PAID_STATUS;

        $visitsToday = SiteVisit::query()->whereDate('visited_at', $today);
        $paidPurchases = Purchase::query()->where('payment_status', $paidStatus);

        $chartDays = collect(CarbonPeriod::create(now()->subDays(6)->startOfDay(), now()->startOfDay()));
        $chartLabels = $chartDays->map(fn ($day) => $day->format('d M'))->all();

        return view('admin.dashboard', [
            'todayUniqueVisitors' => $this->uniqueVisitorsForDate($today),
            'todayPageViews' => (clone $visitsToday)->count(),
            'newUsersToday' => User::query()->whereDate('created_at', $today)->count(),
            'purchasesToday' => (clone $paidPurchases)->whereDate('purchased_at', $today)->count(),
            'todayRevenue' => (float) (clone $paidPurchases)->whereDate('purchased_at', $today)->sum('amount'),
            'totalUsers' => User::count(),
            'totalPurchases' => (clone $paidPurchases)->count(),
            'totalRevenue' => (float) (clone $paidPurchases)->sum('amount'),
            'newEnquiries' => InvitationEnquiry::query()->where('status', 'new')->count(),
            'totalEnquiries' => InvitationEnquiry::count(),
            'templates' => InvitationTemplate::count(),
            'plans' => PricingPlan::count(),
            'recentPurchases' => Purchase::query()
                ->with(['user', 'template', 'plan'])
                ->latest('purchased_at')
                ->latest()
                ->take(10)
                ->get(),
            'recentEnquiries' => InvitationEnquiry::latest()->take(6)->get(),
            'chartData' => [
                'labels' => $chartLabels,
                'visitors' => $chartDays->map(fn ($day) => $this->uniqueVisitorsForDate($day))->all(),
                'purchases' => $chartDays->map(fn ($day) => Purchase::query()
                    ->where('payment_status', $paidStatus)
                    ->whereDate('purchased_at', $day)
                    ->count())->all(),
                'revenue' => $chartDays->map(fn ($day) => (float) Purchase::query()
                    ->where('payment_status', $paidStatus)
                    ->whereDate('purchased_at', $day)
                    ->sum('amount'))->all(),
            ],
        ]);
    }

    private function uniqueVisitorsForDate(mixed $date): int
    {
        $baseQuery = SiteVisit::query()->whereDate('visited_at', $date);

        return (clone $baseQuery)->whereNotNull('user_id')->distinct('user_id')->count('user_id')
            + (clone $baseQuery)
                ->whereNull('user_id')
                ->whereNotNull('session_id')
                ->distinct('session_id')
                ->count('session_id')
            + (clone $baseQuery)
                ->whereNull('user_id')
                ->whereNull('session_id')
                ->whereNotNull('ip_hash')
                ->distinct('ip_hash')
                ->count('ip_hash');
    }
}
