<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\InvitationTemplate;
use App\Models\PricingPlan;
use App\Models\Purchase;
use App\Models\User;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\Request;
use Illuminate\View\View;

class UserController extends Controller
{
    public function index(Request $request): View
    {
        $users = User::query()
            ->with(['latestPurchase.template', 'latestPurchase.plan'])
            ->withCount('purchases')
            ->withSum([
                'purchases as total_spent' => fn (Builder $query) => $query->where('payment_status', Purchase::PAID_STATUS),
            ], 'amount')
            ->when($request->filled('search'), function (Builder $query) use ($request): void {
                $search = $request->string('search')->toString();

                $query->where(function (Builder $query) use ($search): void {
                    $query->where('name', 'like', "%{$search}%")
                        ->orWhere('email', 'like', "%{$search}%");
                });
            })
            ->when($request->registration === 'today', fn (Builder $query) => $query->whereDate('created_at', today()))
            ->when($request->registration === '7', fn (Builder $query) => $query->where('created_at', '>=', now()->subDays(7)))
            ->when($request->registration === '30', fn (Builder $query) => $query->where('created_at', '>=', now()->subDays(30)))
            ->when($request->filled('payment_status'), function (Builder $query) use ($request): void {
                $query->whereHas('purchases', fn (Builder $query) => $query->where('payment_status', $request->string('payment_status')));
            })
            ->when($request->filled('plan_id'), function (Builder $query) use ($request): void {
                $query->whereHas('purchases', fn (Builder $query) => $query->where('pricing_plan_id', $request->integer('plan_id')));
            })
            ->when($request->filled('template_id'), function (Builder $query) use ($request): void {
                $query->whereHas('purchases', fn (Builder $query) => $query->where('invitation_template_id', $request->integer('template_id')));
            })
            ->latest()
            ->paginate(20)
            ->withQueryString();

        return view('admin.users.index', [
            'users' => $users,
            'plans' => PricingPlan::query()->orderBy('sort_order')->orderBy('name')->get(),
            'templates' => InvitationTemplate::query()->orderBy('sort_order')->orderBy('name')->get(),
            'paymentStatuses' => ['paid', 'pending', 'failed', 'refunded', 'cancelled'],
        ]);
    }

    public function show(User $user): View
    {
        $purchases = Purchase::query()
            ->whereBelongsTo($user)
            ->with(['template', 'plan'])
            ->latest('purchased_at')
            ->latest()
            ->paginate(20);

        return view('admin.users.show', [
            'user' => $user,
            'purchases' => $purchases,
            'totalPurchases' => $user->purchases()->where('payment_status', Purchase::PAID_STATUS)->count(),
            'totalSpent' => (float) $user->purchases()->where('payment_status', Purchase::PAID_STATUS)->sum('amount'),
        ]);
    }
}
