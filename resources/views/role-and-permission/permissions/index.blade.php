@extends('layouts.main')

@section('content')
    <div class="container-xxl flex-grow-1 container-p-y">

        <!-- Breadcrumb & Title -->
        <div class="mb-4">
            <div class="p-3 border rounded d-flex justify-content-between align-items-center"
                style="background-color: #f8f9fa;">

                <h6 class="m-0 fw-bold text-primary">Permission Management</h6>

                <nav aria-label="breadcrumb">
                    <ol class="breadcrumb breadcrumb-style2 mb-0">
                        <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">Dashboard</a></li>
                        <li class="breadcrumb-item active">Permissions</li>
                    </ol>
                </nav>
            </div>
        </div>


        <div class="row">
            <div class="col-12">
                <div class="card">
                    <div class="card-body">

                        <!-- Create Button -->
                        <div class="d-flex justify-content-end mb-4">
                            @can('permissions.create')
                                <button type="button" class="btn btn-primary mb-4" data-bs-toggle="modal"
                                    data-bs-target="#createPermissionModal">
                                    Create Permission
                                </button>
                            @endcan
                        </div>

                        <div class="table-responsive">
                            <table id="permissions-table" class="table" width="100%">
                                <thead>
                                    <tr>
                                        <th>No</th>
                                        <th>Permission Name</th>
                                        <th>Guard</th>
                                        <th>Actions</th>
                                    </tr>
                                </thead>
                                <tbody></tbody>
                            </table>
                        </div>

                    </div>
                </div>
            </div>
        </div>

    </div>

    @include('role-and-permission.permissions.create')
    @include('role-and-permission.permissions.edit')
    @include('role-and-permission.permissions.action-menu')
@endsection

@push('scripts')
    <script>
        const editRouteBase = "{{ route('permissions.edit', 0) }}";
        const updateRouteBase = "{{ route('permissions.update', 0) }}";
        const deleteRouteBase = "{{ route('permissions.destroy', 0) }}";

        function getEditUrl(id) {
            return editRouteBase.replace('/0/edit', `/${id}/edit`);
        }

        function getUpdateUrl(id) {
            return updateRouteBase.replace('/0', `/${id}`);
        }

        function getDeleteUrl(id) {
            return deleteRouteBase.replace('/0', `/${id}`);
        }
    </script>

    <script>
        $(document).ready(function() {
            $('#permissions-table').DataTable({
                processing: true,
                serverSide: true,
                ajax: {
                    url: "{{ route('permission.list') }}",
                    type: "POST",
                    data: {
                        _token: "{{ csrf_token() }}"
                    }
                },
                columns: [{
                        data: 'DT_RowIndex',
                        orderable: false,
                        searchable: false
                    },
                    {
                        data: 'name'
                    },
                    {
                        data: 'guard_name'
                    },
                    {
                        data: 'id',
                        orderable: false,
                        searchable: false,
                        render: function(id) {
                            let editUrl = getEditUrl(id);
                            let updateUrl = getUpdateUrl(id);
                            let deleteUrl = getDeleteUrl(id);

                            return `
                        <button class="btn btn-sm btn-primary action-menu-btn"
                                data-edit="${editUrl}"
                                data-update="${updateUrl}"
                                data-delete="${deleteUrl}">
                            <i class="bx bx-dots-vertical-rounded"></i>
                        </button>
                    `;
                        }
                    }
                ]
            });
        });

        // open action modal
        let selectedEditUrl = '',
            selectedUpdateUrl = '',
            selectedDeleteUrl = '';

        $(document).on('click', '.action-menu-btn', function() {
            selectedEditUrl = $(this).data('edit');
            selectedUpdateUrl = $(this).data('update');
            selectedDeleteUrl = $(this).data('delete');

            $('#actionMenuPermissionModal').modal('show');
        });
    </script>
@endpush
