@extends('layouts.main')

@section('content')
    <div class="container-xxl flex-grow-1 container-p-y">
        <!-- Breadcrumb in box with title -->
        <div class="mb-4">
            <div class="p-3 border rounded d-flex justify-content-between align-items-center"
                style="background-color: #f8f9fa;">
                <!-- Title di kiri -->
                <h6 class="m-0 fw-bold text-primary">Activity Logs</h6>

                <!-- Breadcrumb di kanan -->
                <nav aria-label="breadcrumb">
                    <ol class="breadcrumb breadcrumb-style2 mb-0">
                        <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">Dashboard</a></li>
                        <li class="breadcrumb-item active" aria-current="page">Activity Logs</li>
                    </ol>
                </nav>
            </div>
        </div>

        <div class="row">
            <div class="col-12">
                <div class="card">
                    <div class="card-body">
                        <!-- Table -->
                        <div class="table-responsive">
                            <table id="activity-logs-table" class="table" width="100%" cellspacing="0">
                                <thead>
                                    <tr>
                                        <th>No</th>
                                        <th>User</th>
                                        <th>Aksi</th>
                                        <th>Created At</th>
                                        <th>Actions</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <!-- Table rows will be populated via DataTables -->
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    @include('activity-logs.show')
@endsection

@push('scripts')
    <script>
        const showRouteBase = "{{ route('log-activity.show', ['log_activity' => 0]) }}";

        function getShowUrl(id) {
            return showRouteBase.replace(/0$/, id);
        }


        $(document).ready(function() {
            $('#activity-logs-table').DataTable({
                processing: true,
                serverSide: true,
                ajax: {
                    url: '{{ route('log-activity.list') }}',
                    type: 'POST',
                    data: {
                        _token: '{{ csrf_token() }}'
                    }
                },
                columns: [{
                        data: 'DT_RowIndex',
                        name: 'DT_RowIndex',
                        orderable: false,
                        searchable: false
                    },
                    {
                        data: 'user.name',
                        name: 'user.name',
                        render: data => data ? data : '-'
                    },
                    {
                        data: 'activity',
                        name: 'activity'
                    },
                    {
                        data: 'created_at',
                        name: 'created_at'
                    },
                    {
                        data: 'id',
                        name: 'id',
                        orderable: false,
                        searchable: false,
                        render: function(data) {
                            let showUrl = getShowUrl(data);
                            return `
                                        <button class="btn btn-sm btn-primary show-activity-log-btn" 
                                                data-show-route="${showUrl}" 
                                                title="showData">
                                            <i class="bx bx-eye"></i>
                                        </button>
                                    `;
                        }
                    }
                ],
                autoWidth: false,
                order: [
                    [3, 'desc']
                ],
                drawCallback: function(settings) {
                    $('a').tooltip();
                }
            });
        });
    </script>
@endpush
