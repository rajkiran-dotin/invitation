<?php

namespace App\Http\Controllers;

use App\Models\Faq;
use App\Models\InvitationEnquiry;
use App\Models\InvitationTemplate;
use App\Models\PricingPlan;
use App\Models\Testimonial;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Schema;
use Illuminate\View\View;

class LandingController extends Controller
{
    public function index(): View
    {
        return view('welcome', [
            'dbTemplates' => $this->homepageTemplates(),
            'dbPlans' => $this->activeRows(PricingPlan::class, 'pricing_plans'),
            'dbTestimonials' => $this->activeRows(Testimonial::class, 'testimonials', 3),
            'dbFaqs' => $this->activeRows(Faq::class, 'faqs', 5),
        ]);
    }

    public function enquiry(Request $request): RedirectResponse
    {
        InvitationEnquiry::create($request->validate([
            'name' => ['required', 'string', 'max:120'],
            'email' => ['required', 'email', 'max:160'],
            'phone' => ['nullable', 'string', 'max:30'],
            'event_type' => ['required', 'string', 'max:80'],
            'event_date' => ['nullable', 'date'],
            'plan' => ['nullable', 'string', 'max:80'],
            'message' => ['nullable', 'string', 'max:1000'],
        ]));

        return back()->with('status', 'Thanks! We received your invitation request.');
    }

    private function homepageTemplates(): Collection
    {
        if (! Schema::hasTable('invitation_templates')) {
            return collect();
        }

        $query = InvitationTemplate::query()
            ->with('templateCategory')
            ->where('is_active', true)
            ->orderBy('sort_order');

        if (Schema::hasTable('template_categories') && Schema::hasColumn('invitation_templates', 'category_id')) {
            $query->whereHas('templateCategory', fn ($query) => $query->where('is_active', true));
        }

        return $query->take(6)->get();
    }

    private function activeRows(string $model, string $table, ?int $limit = null): Collection
    {
        if (! Schema::hasTable($table)) {
            return collect();
        }

        $query = $model::query()->where('is_active', true)->orderBy('sort_order');

        return $limit ? $query->take($limit)->get() : $query->get();
    }
}
