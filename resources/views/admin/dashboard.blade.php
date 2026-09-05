@extends('admin.layout')

@section('title', 'Dashboard')

@section('content')
    <section class="admin-stats">
        <article><span>New Enquiries</span><strong>{{ $newEnquiries }}</strong></article>
        <article><span>Total Enquiries</span><strong>{{ $totalEnquiries }}</strong></article>
        <article><span>Templates</span><strong>{{ $templates }}</strong></article>
        <article><span>Plans</span><strong>{{ $plans }}</strong></article>
    </section>

    <section class="admin-panel">
        <div class="admin-panel-head">
            <h2>Recent Enquiries</h2>
            <a class="btn btn-outline" href="{{ route('admin.enquiries.index') }}">View All</a>
        </div>
        @include('admin.partials.enquiry-table', ['enquiries' => $recentEnquiries, 'compact' => true])
    </section>
@endsection
