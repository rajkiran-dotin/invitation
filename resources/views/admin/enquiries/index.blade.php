@extends('admin.layout')

@section('title', 'Enquiries')

@section('content')
    <section class="admin-panel">
        <div class="admin-panel-head">
            <h2>Invitation Requests</h2>
        </div>
        @include('admin.partials.enquiry-table', ['enquiries' => $enquiries])
        {{ $enquiries->links() }}
    </section>
@endsection
