<div class="admin-table-wrap">
    <table class="admin-table">
        <thead>
            <tr>
                <th>Name</th>
                <th>Contact</th>
                <th>Event</th>
                <th>Plan</th>
                <th>Status</th>
                <th>Received</th>
                @empty($compact)<th>Action</th>@endempty
            </tr>
        </thead>
        <tbody>
            @forelse ($enquiries as $enquiry)
                <tr>
                    <td><strong>{{ $enquiry->name }}</strong><small>{{ $enquiry->message }}</small></td>
                    <td>{{ $enquiry->email }}<small>{{ $enquiry->phone }}</small></td>
                    <td>{{ $enquiry->event_type }}<small>{{ optional($enquiry->event_date)->format('d M Y') }}</small></td>
                    <td>{{ $enquiry->plan ?: '-' }}</td>
                    <td><span class="status-pill">{{ ucfirst($enquiry->status) }}</span></td>
                    <td>{{ $enquiry->created_at->diffForHumans() }}</td>
                    @empty($compact)
                        <td>
                            <form method="POST" action="{{ route('admin.enquiries.update', $enquiry) }}" class="inline-form">
                                @csrf
                                @method('PATCH')
                                <select name="status" onchange="this.form.submit()">
                                    @foreach (['new', 'contacted', 'converted', 'closed'] as $status)
                                        <option value="{{ $status }}" @selected($enquiry->status === $status)>{{ ucfirst($status) }}</option>
                                    @endforeach
                                </select>
                            </form>
                            <form method="POST" action="{{ route('admin.enquiries.destroy', $enquiry) }}" class="inline-form">
                                @csrf
                                @method('DELETE')
                                <button type="submit">Delete</button>
                            </form>
                        </td>
                    @endempty
                </tr>
            @empty
                <tr><td colspan="7" class="empty-state">No enquiries yet.</td></tr>
            @endforelse
        </tbody>
    </table>
</div>
