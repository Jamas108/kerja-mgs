@extends('layouts.admin')

@section('title', 'Review Management')

@section('content')
<div class="container-fluid">
    <div class="d-sm-flex align-items-center justify-content-between mb-4">
        <h1 class="h3 mb-0 text-gray-800">Review Management</h1>
    </div>

    <!-- Filter Section -->
    <div class="card shadow mb-4">
        <div class="card-header py-3">
            <h6 class="m-0 font-weight-bold text-primary">Filter & Search</h6>
        </div>
        <div class="card-body">
            <form method="GET" action="{{ route('admin.reviews.index') }}">
                <div class="row">
                    <div class="col-md-4 mb-3">
                        <label for="division_id" class="form-label">Division</label>
                        <select name="division_id" id="division_id" class="form-control">
                            <option value="">All Divisions</option>
                            @foreach($divisions as $division)
                                <option value="{{ $division->id }}" {{ request('division_id') == $division->id ? 'selected' : '' }}>
                                    {{ $division->name }}
                                </option>
                            @endforeach
                        </select>
                    </div>
                    <div class="col-md-4 mb-3">
                        <label for="search" class="form-label">Search Employee/Task</label>
                        <input type="text" name="search" id="search" class="form-control"
                               placeholder="Search by employee name or task title..."
                               value="{{ request('search') }}">
                    </div>
                    <div class="col-md-4 mb-3 d-flex align-items-end">
                        <button type="submit" class="btn btn-primary me-2">
                            <i class="fas fa-search"></i> Filter
                        </button>
                        <a href="{{ route('admin.reviews.index') }}" class="btn btn-secondary">
                            <i class="fas fa-times"></i> Clear
                        </a>
                    </div>
                </div>
            </form>
        </div>
    </div>

    <!-- Card Ulasan Menunggu -->
    <div class="card shadow mb-4">
        <div class="card-header py-3 d-flex justify-content-between align-items-center">
            <h6 class="m-0 font-weight-bold text-primary">Pending Director Reviews ({{ $pendingReviews->count() }})</h6>
            @if($pendingReviews->count() > 0)
                <button type="button" class="btn btn-success btn-sm" data-toggle="modal" data-target="#bulkApproveModal">
                    <i class="fas fa-check-double"></i> Bulk Approve
                </button>
            @endif
        </div>
        <div class="card-body">
            @if($pendingReviews->count() > 0)
                <!-- Tampilan Desktop -->
                <div class="table-responsive d-none d-md-block">
                    <table class="table table-bordered" id="pendingReviewsTable" width="100%" cellspacing="0">
                        <thead>
                            <tr>
                                <th width="30">
                                    <input type="checkbox" id="selectAll">
                                </th>
                                <th>Task Title</th>
                                <th>Employee</th>
                                <th>Division</th>
                                <th>Head Rating</th>
                                <th>Completed At</th>
                                <th>Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($pendingReviews as $assignment)
                            <tr>
                                <td>
                                    <input type="checkbox" class="assignment-checkbox" value="{{ $assignment->id }}">
                                </td>
                                <td>{{ $assignment->jobDesk->title }}</td>
                                <td>{{ $assignment->employee->name }}</td>
                                <td>{{ $assignment->jobDesk->division->name }}</td>
                                <td>
                                    <span class="badge badge-primary">{{ $assignment->kadiv_rating }} / 4</span>
                                </td>
                                <td>{{ $assignment->completed_at->format('d M Y H:i') }}</td>
                                <td>
                                    <a href="{{ route('admin.reviews.show', $assignment) }}" class="btn btn-primary btn-sm">
                                        <i class="fas fa-eye"></i> Review
                                    </a>
                                </td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>

                <!-- Tampilan Mobile -->
                <div class="d-md-none">
                    @foreach($pendingReviews as $assignment)
                    <div class="card mb-3 border-left-primary">
                        <div class="card-body">
                            <div class="d-flex justify-content-between align-items-start mb-2">
                                <h6 class="font-weight-bold text-primary mb-0">{{ $assignment->jobDesk->title }}</h6>
                                <input type="checkbox" class="assignment-checkbox" value="{{ $assignment->id }}">
                            </div>
                            <div class="mb-2">
                                <small class="text-muted">Employee:</small>
                                <p class="mb-1">{{ $assignment->employee->name }}</p>
                            </div>
                            <div class="mb-2">
                                <small class="text-muted">Division:</small>
                                <p class="mb-1">{{ $assignment->jobDesk->division->name }}</p>
                            </div>
                            <div class="mb-2">
                                <small class="text-muted">Head Rating:</small>
                                <div><span class="badge badge-primary">{{ $assignment->kadiv_rating }} / 4</span></div>
                            </div>
                            <div class="mb-3">
                                <small class="text-muted">Completed At:</small>
                                <p class="mb-0">{{ $assignment->completed_at->format('d M Y H:i') }}</p>
                            </div>
                            <a href="{{ route('admin.reviews.show', $assignment) }}" class="btn btn-primary btn-sm btn-block">
                                <i class="fas fa-eye"></i> Review
                            </a>
                        </div>
                    </div>
                    @endforeach
                </div>
            @else
                <div class="alert alert-info mb-0">
                    <i class="fas fa-info-circle"></i> No pending director reviews at this time.
                </div>
            @endif
        </div>
    </div>

    <!-- Card Ditolak oleh Direktur -->
    <div class="card shadow mb-4">
        <div class="card-header py-3">
            <h6 class="m-0 font-weight-bold text-danger">Rejected by Director ({{ $rejectedByDirector->count() }})</h6>
        </div>
        <div class="card-body">
            @if($rejectedByDirector->count() > 0)
                <!-- Tampilan Desktop -->
                <div class="table-responsive d-none d-md-block">
                    <table class="table table-bordered" id="rejectedTable" width="100%" cellspacing="0">
                        <thead>
                            <tr>
                                <th>Task Title</th>
                                <th>Employee</th>
                                <th>Division</th>
                                <th>Head Rating</th>
                                <th>Director Rating</th>
                                <th>Director Notes</th>
                                <th>Status</th>
                                <th>Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($rejectedByDirector as $assignment)
                            <tr>
                                <td>{{ $assignment->jobDesk->title }}</td>
                                <td>{{ $assignment->employee->name }}</td>
                                <td>{{ $assignment->jobDesk->division->name }}</td>
                                <td><span class="badge badge-primary">{{ $assignment->kadiv_rating }} / 4</span></td>
                                <td><span class="badge badge-danger">{{ $assignment->director_rating }} / 4</span></td>
                                <td>{{ Str::limit($assignment->director_notes, 50) }}</td>
                                <td>{!! $assignment->status_badge !!}</td>
                                <td>
                                    <a href="{{ route('admin.reviews.show', $assignment) }}" class="btn btn-info btn-sm">
                                        <i class="fas fa-eye"></i> View
                                    </a>
                                </td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>

                <!-- Tampilan Mobile -->
                <div class="d-md-none">
                    @foreach($rejectedByDirector as $assignment)
                    <div class="card mb-3 border-left-danger">
                        <div class="card-body">
                            <h6 class="font-weight-bold text-danger mb-2">{{ $assignment->jobDesk->title }}</h6>
                            <div class="mb-2">
                                <small class="text-muted">Employee:</small>
                                <p class="mb-1">{{ $assignment->employee->name }}</p>
                            </div>
                            <div class="mb-2">
                                <small class="text-muted">Division:</small>
                                <p class="mb-1">{{ $assignment->jobDesk->division->name }}</p>
                            </div>
                            <div class="mb-2">
                                <small class="text-muted">Head Rating:</small>
                                <div><span class="badge badge-primary">{{ $assignment->kadiv_rating }} / 4</span></div>
                            </div>
                            <div class="mb-2">
                                <small class="text-muted">Director Rating:</small>
                                <div><span class="badge badge-danger">{{ $assignment->director_rating }} / 4</span></div>
                            </div>
                            <div class="mb-2">
                                <small class="text-muted">Director Notes:</small>
                                <p class="mb-1">{{ $assignment->director_notes }}</p>
                            </div>
                            <div class="mb-3">
                                <small class="text-muted">Status:</small>
                                <div class="mt-1">{!! $assignment->status_badge !!}</div>
                            </div>
                            <a href="{{ route('admin.reviews.show', $assignment) }}" class="btn btn-info btn-sm btn-block">
                                <i class="fas fa-eye"></i> View Details
                            </a>
                        </div>
                    </div>
                    @endforeach
                </div>
            @else
                <div class="alert alert-info mb-0">
                    <i class="fas fa-info-circle"></i> No assignments rejected by director.
                </div>
            @endif
        </div>
    </div>
</div>

<!-- Bulk Approve Modal -->
<div class="modal fade" id="bulkApproveModal" tabindex="-1" aria-labelledby="bulkApproveModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <form action="{{ route('admin.reviews.bulk-approve') }}" method="POST" id="bulkApproveForm">
                @csrf
                <div class="modal-header">
                    <h5 class="modal-title" id="bulkApproveModalLabel">Bulk Approve Reviews</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <div id="selectedAssignments"></div>

                    <div class="form-group">
                        <label for="bulk_rating">Director Rating</label>
                        <select class="form-control" id="bulk_rating" name="bulk_rating" required>
                            <option value="">Select Rating</option>
                            <option value="1">1 - Poor</option>
                            <option value="2">2 - Fair</option>
                            <option value="3">3 - Good</option>
                            <option value="4">4 - Excellent</option>
                        </select>
                    </div>

                    <div class="form-group">
                        <label for="bulk_notes">Director Notes (Optional)</label>
                        <textarea class="form-control" id="bulk_notes" name="bulk_notes" rows="3"
                                  placeholder="Enter notes for all selected assignments..."></textarea>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-success">
                        <i class="fas fa-check"></i> Approve Selected
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

@push('styles')
<style>
    /* Styling untuk card mobile */
    @media (max-width: 767.98px) {
        .card-body h6 {
            font-size: 0.95rem;
        }

        .card-body small {
            font-size: 0.75rem;
            font-weight: 600;
        }

        .card-body p {
            font-size: 0.875rem;
        }

        .border-left-primary {
            border-left: 0.25rem solid #4e73df !important;
        }

        .border-left-danger {
            border-left: 0.25rem solid #e74a3b !important;
        }
    }

    .assignment-checkbox {
        transform: scale(1.2);
    }
</style>
@endpush

@push('scripts')
<script>
$(document).ready(function() {
    // Initialize DataTables
    $('#pendingReviewsTable, #rejectedTable').DataTable({
        "pageLength": 25,
        "responsive": true,
        "order": [[ 5, "asc" ]], // Order by completed_at
        "columnDefs": [
            { "orderable": false, "targets": [0, 6] } // Disable sorting for checkbox and actions
        ]
    });

    // Select All functionality
    $('#selectAll').change(function() {
        $('.assignment-checkbox').prop('checked', this.checked);
        updateBulkApproveButton();
    });

    // Individual checkbox change
    $('.assignment-checkbox').change(function() {
        updateBulkApproveButton();

        // Update select all checkbox
        var totalCheckboxes = $('.assignment-checkbox').length;
        var checkedCheckboxes = $('.assignment-checkbox:checked').length;

        if (checkedCheckboxes === 0) {
            $('#selectAll').prop('indeterminate', false).prop('checked', false);
        } else if (checkedCheckboxes === totalCheckboxes) {
            $('#selectAll').prop('indeterminate', false).prop('checked', true);
        } else {
            $('#selectAll').prop('indeterminate', true);
        }
    });

    // Update bulk approve button state
    function updateBulkApproveButton() {
        var selectedCount = $('.assignment-checkbox:checked').length;
        var $button = $('[data-target="#bulkApproveModal"]');

        if (selectedCount > 0) {
            $button.prop('disabled', false).text('Bulk Approve (' + selectedCount + ')');
        } else {
            $button.prop('disabled', true).text('Bulk Approve');
        }
    }

    // When bulk approve modal is shown
    $('#bulkApproveModal').on('show.bs.modal', function() {
        var selectedIds = [];
        var selectedTitles = [];

        $('.assignment-checkbox:checked').each(function() {
            var id = $(this).val();
            var title = $(this).closest('tr').find('td:eq(1)').text() ||
                       $(this).closest('.card-body').find('h6').text();

            selectedIds.push(id);
            selectedTitles.push(title.trim());
        });

        // Add hidden inputs for selected assignments
        var hiddenInputs = '';
        selectedIds.forEach(function(id) {
            hiddenInputs += '<input type="hidden" name="assignment_ids[]" value="' + id + '">';
        });
        $('#selectedAssignments').html(hiddenInputs);

        // Show selected assignments list
        var listHtml = '<div class="alert alert-info"><strong>Selected Assignments (' + selectedIds.length + '):</strong><ul class="mb-0 mt-2">';
        selectedTitles.forEach(function(title) {
            listHtml += '<li>' + title + '</li>';
        });
        listHtml += '</ul></div>';
        $('#selectedAssignments').append(listHtml);
    });

    // Clear modal when hidden
    $('#bulkApproveModal').on('hidden.bs.modal', function() {
        $('#selectedAssignments').empty();
        $('#bulkApproveForm')[0].reset();
    });

    // Initial button state
    updateBulkApproveButton();
});
</script>
@endpush
@endsection