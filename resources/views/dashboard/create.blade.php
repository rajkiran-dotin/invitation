@extends('layouts.dashboard')

@section('title', 'Create Invitation')

@section('content')
    <form class="create-flow" method="POST" action="{{ route('dashboard.store') }}" id="createInvitationForm" data-razorpay-key="{{ $razorpayKey }}">
        @csrf
        <input type="hidden" name="template_id" id="selectedTemplate" value="{{ old('template_id') }}">
        <input type="hidden" name="plan" id="selectedPlan" value="{{ old('plan', 'silver') }}">
        <input type="hidden" name="razorpay_payment_id" id="razorpayPaymentId">
        <input type="hidden" name="razorpay_order_id" id="razorpayOrderId">
        <input type="hidden" name="razorpay_signature" id="razorpaySignature">

        <div class="stepper" aria-label="Create invitation steps">
            <button type="button" class="active" data-step-button="1">1 Template</button>
            <button type="button" data-step-button="2">2 Details</button>
            <button type="button" data-step-button="3">3 Plan & Pay</button>
        </div>

        @if ($errors->any())
            <div class="error-box">
                @foreach ($errors->all() as $error)
                    <p>{{ $error }}</p>
                @endforeach
            </div>
        @endif

        <section class="flow-step active" data-step="1">
            <div class="section-heading">
                <h2>Choose Template</h2>
            </div>
            <div class="template-gallery">
                @forelse ($templates as $template)
                    <button type="button" class="template-option {{ old('template_id') == $template->id ? 'selected' : '' }}" data-template-id="{{ $template->id }}">
                        <span class="template-image">
                            @if ($template->thumbnail_url)
                                <img src="{{ $template->thumbnail_url }}" alt="{{ $template->name }} thumbnail">
                            @else
                                <span>{{ $template->name }}</span>
                            @endif
                        </span>
                        <strong>{{ $template->name }}</strong>
                        @if ($template->premium)
                            <em>Premium</em>
                        @endif
                    </button>
                @empty
                    <div class="empty-state">No active templates found.</div>
                @endforelse
            </div>
            <div class="flow-actions">
                <button type="button" class="primary-action" data-next-step="2">Continue</button>
            </div>
        </section>

        <section class="flow-step" data-step="2">
            <div class="section-heading"><h2>Wedding Details</h2></div>
            <div class="form-grid">
                <label>Groom Name<input required name="groom_name" value="{{ old('groom_name') }}"></label>
                <label>Bride Name<input required name="bride_name" value="{{ old('bride_name') }}"></label>
                <label>Wedding Date<input required type="date" name="wedding_date" value="{{ old('wedding_date') }}"></label>
                <label>Wedding Time<input type="time" name="wedding_time" value="{{ old('wedding_time') }}"></label>
                <label>Venue Name<input required name="venue_name" value="{{ old('venue_name') }}"></label>
                <label>City<input required name="city" value="{{ old('city') }}"></label>
                <label class="full">Venue Address<textarea name="venue_address" rows="3">{{ old('venue_address') }}</textarea></label>
                <label class="full">Family Names<textarea name="family_names" rows="3">{{ old('family_names') }}</textarea></label>
                <label>RSVP Phone Number<input name="rsvp_phone" value="{{ old('rsvp_phone') }}"></label>
            </div>
            <div class="flow-actions split">
                <button type="button" class="secondary-action" data-prev-step="1">Back</button>
                <button type="button" class="primary-action" data-next-step="3">Continue</button>
            </div>
        </section>

        <section class="flow-step" data-step="3">
            <div class="section-heading"><h2>Choose Plan & Pay</h2></div>
            <div class="plan-grid">
                @foreach ($plans as $key => $plan)
                    <button type="button" class="plan-card {{ old('plan', 'silver') === $key ? 'selected' : '' }}" data-plan="{{ $key }}" data-amount="{{ $plan['price'] }}">
                        <span>{{ $plan['name'] }}</span>
                        <strong>₹{{ $plan['price'] }}</strong>
                        @foreach ($plan['features'] as $feature)
                            <small>{{ $feature }}</small>
                        @endforeach
                    </button>
                @endforeach
            </div>
            <div class="flow-actions split">
                <button type="button" class="secondary-action" data-prev-step="2">Back</button>
                <button type="button" class="primary-action" id="payButton">Pay & Preview</button>
            </div>
        </section>
    </form>
@endsection

@push('scripts')
<script src="https://checkout.razorpay.com/v1/checkout.js"></script>
<script>
    const form = document.getElementById('createInvitationForm');
    const selectedTemplate = document.getElementById('selectedTemplate');
    const selectedPlan = document.getElementById('selectedPlan');
    const plans = @json($plans);

    function showStep(step) {
        document.querySelectorAll('[data-step]').forEach(function (panel) {
            panel.classList.toggle('active', panel.dataset.step === String(step));
        });
        document.querySelectorAll('[data-step-button]').forEach(function (button) {
            button.classList.toggle('active', button.dataset.stepButton === String(step));
        });
    }

    document.querySelectorAll('[data-template-id]').forEach(function (button) {
        button.addEventListener('click', function () {
            document.querySelectorAll('[data-template-id]').forEach(item => item.classList.remove('selected'));
            button.classList.add('selected');
            selectedTemplate.value = button.dataset.templateId;
        });
    });

    document.querySelectorAll('[data-plan]').forEach(function (button) {
        button.addEventListener('click', function () {
            document.querySelectorAll('[data-plan]').forEach(item => item.classList.remove('selected'));
            button.classList.add('selected');
            selectedPlan.value = button.dataset.plan;
        });
    });

    document.querySelectorAll('[data-next-step]').forEach(function (button) {
        button.addEventListener('click', function () {
            if (button.dataset.nextStep === '2' && !selectedTemplate.value) {
                alert('Please select a template.');
                return;
            }
            showStep(button.dataset.nextStep);
        });
    });

    document.querySelectorAll('[data-prev-step]').forEach(function (button) {
        button.addEventListener('click', function () { showStep(button.dataset.prevStep); });
    });

    document.getElementById('payButton').addEventListener('click', function () {
        if (!form.reportValidity()) {
            return;
        }

        const key = form.dataset.razorpayKey;
        const plan = plans[selectedPlan.value];

        if (!key || typeof Razorpay === 'undefined') {
            form.submit();
            return;
        }

        const checkout = new Razorpay({
            key: key,
            amount: plan.price * 100,
            currency: 'INR',
            name: 'InviteCraft',
            description: plan.name + ' Wedding Invitation Plan',
            prefill: {
                name: '{{ auth()->user()->name }}',
                email: '{{ auth()->user()->email }}',
                contact: '{{ auth()->user()->phone }}'
            },
            handler: function (response) {
                document.getElementById('razorpayPaymentId').value = response.razorpay_payment_id || '';
                document.getElementById('razorpayOrderId').value = response.razorpay_order_id || '';
                document.getElementById('razorpaySignature').value = response.razorpay_signature || '';
                form.submit();
            }
        });
        checkout.open();
    });
</script>
@endpush
