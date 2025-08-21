<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Add Contact</title>
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        body {
            min-height: 100vh;
            background: linear-gradient(135deg, #e0f7fa, #f1f8e9);
            display: flex;
            justify-content: center;
            align-items: center;
        }
        .form-container {
            max-width: 400px;
            width: 100%;
            background: #fff;
            padding: 2rem;
            border-radius: 1rem;
            box-shadow: 0 5px 20px rgba(0,0,0,0.1);
        }
    </style>
</head>
<body>

<div class="form-container">
    <h3 class="mb-4 text-center">Add Contact</h3>

    @if (session('success'))
        <div class="alert alert-success">{{ session('success') }}</div>
    @elseif (session('error'))
        <div class="alert alert-danger">{{ session('error') }}</div>
    @endif

    <form method="POST" action="{{ route('mailchimp.store') }}">
        @csrf
        <div class="mb-3">
            <label class="form-label">Email</label>
            <input type="email" name="email" class="form-control" required>
        </div>

        <div class="mb-3">
            <label class="form-label">First Name</label>
            <input type="text" name="first_name" class="form-control">
        </div>

        <div class="mb-3">
            <label class="form-label">Last Name</label>
            <input type="text" name="last_name" class="form-control">
        </div>

        <div class="mb-3">
            <label class="form-label">Tags (comma separated)</label>
            <input type="text" name="tags" class="form-control">
        </div>

        <button type="submit" class="btn btn-primary w-100">Add Contact</button>
    </form>

    <div class="text-center mt-3">
        <a href="{{ url('/mailchimp/list') }}" class="text-decoration-none">View Contact List</a>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
