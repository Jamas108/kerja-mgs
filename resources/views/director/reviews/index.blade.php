@extends('layouts.director')

@section('title', 'Task Reviews')

@section('content')
<div class="container-fluid">
    <div class="d-sm-flex align-items-center justify-content-between mb-4">
        <h1 class="h3 mb-0 text-gray-800">Task Reviews</h1>
    </div>

    <div class="card shadow mb-4">
        <div class="card-header py-3">
            <h6 class="m-0 font-weight-bold text-primary">Pending Reviews</h6>
        </div>
        <div class="card-body">
            @if($pendingReviews->count() > 0)
                <div class="table-responsive">
                    <table class="table table-bordered" id="pendingReviewsTable" width="100%" cellspacing="0">
                        <thead>
                            <tr>
                                <th>Task Title</th>
                                <th>Employee</th>
                                <th>Division</th>
                                <th>Kadiv Rating</th>
                                <th>Completed At</th>
                                <th>Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($pendingReviews as $assignment)
                            <tr>
                                <td>{{ $assignment->jobDesk->title }}</td>
                                <td>{{ $assignment->employee->name }}</td>
                                <td>{{ $assignment->jobDesk->division->name }}</td>
                                <td>{{ $assignment->kadiv_rating }} / 4</td>
                                <td>{{ $assignment->completed_at->format('d M Y H:i') }}</td>
                                <td>
                                    <a href="{{ route('director.reviews.show', $assignment) }}" class="btn btn-primary btn-sm">
                                        <i class="fas fa-eye"></i> Review
                                    </a>
                                </td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            @else
                <div class="alert alert-info">No pending reviews at this time.</div>
            @endif
        </div>
    </div>
</div>
@endsection
