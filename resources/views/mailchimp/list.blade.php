<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Mailchimp Contacts</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body class="bg-light">

<div class="container my-5">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h1 class="h3">Mailchimp Contacts</h1>
        <a href="{{ url('/') }}" class="btn btn-primary">Add New Contact</a>
    </div>

    @if (session('success'))
        <div class="alert alert-success">{{ session('success') }}</div>
    @endif

    @if (session('error'))
        <div class="alert alert-danger">{{ session('error') }}</div>
    @endif

    @if ($contacts->isEmpty())
        <div class="alert alert-warning">No contacts found.</div>
    @else
        <div class="card shadow-sm">
            <div class="card-body p-0">
                <table class="table table-hover mb-0">
                    <thead class="table-dark">
                        <tr>
                            <th>Email</th>
                            <th>First Name</th>
                            <th>Last Name</th>
                            <th>Signup Date</th>
                            <th>Tags</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($contacts as $contact)
                            <tr>
                                <td>{{ $contact->email }}</td>
                                <td>{{ $contact->first_name }}</td>
                                <td>{{ $contact->last_name }}</td>
                                <td>
                                    {{ $contact->signup_date ? \Carbon\Carbon::parse($contact->signup_date)->format('Y-m-d H:i') : 'N/A' }}
                                </td>
                                <td>
                                    @php
                                        $tags = is_array($contact->tags) ? $contact->tags : explode(',', $contact->tags);
                                    @endphp
                                    @forelse ($tags as $tag)
                                        <span class="badge bg-secondary me-1">{{ trim($tag) }}</span>
                                    @empty
                                        <span class="text-muted">No tags</span>
                                    @endforelse
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    @endif
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>

