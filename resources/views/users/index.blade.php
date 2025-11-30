@extends('layouts.main')

@section('content')
    <div class="container-xxl flex-grow-1 container-p-y">
        <!-- Breadcrumb in box with title -->
        <div class="mb-4">
            <div class="p-3 border rounded d-flex justify-content-between align-items-center"
                style="background-color: #f8f9fa;">
                <!-- Title di kiri -->
                <h6 class="m-0 fw-bold text-primary">User Management</h6>

                <!-- Breadcrumb di kanan -->
                <nav aria-label="breadcrumb">
                    <ol class="breadcrumb breadcrumb-style2 mb-0">
                        <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">Dashboard</a></li>
                        <li class="breadcrumb-item active" aria-current="page">Users</li>
                    </ol>
                </nav>
            </div>
        </div>


        <div class="row">
            <div class="col-12">
                <div class="card">
                    <div class="card-body">
                        <!-- Add a button for creating a user -->
                        <div class="d-flex justify-content-end mb-4">
                            @can('users.create')
                                <button type="button" class="btn btn-primary mb-4" data-bs-toggle="modal"
                                    data-bs-target="#createUserModal">
                                    Create User
                                </button>
                            @endcan
                        </div>
                        <!-- Table -->
                        <div class="table-responsive">
                            <table id="users-table" class="table" width="100%" cellspacing="0">
                                <thead>
                                    <tr>
                                        <th>No</th> <!-- Row number column -->
                                        <th>Name</th>
                                        <th>Email</th>
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
    @include('users.create')
    @include('users.edit')
    @include('users.action-menu')
@endsection
@push('styles')
@endpush
@push('scripts')
    <script>
        // ambil base route Laravel
        const editRouteBase = "{{ route('users.edit', 0) }}"; // /users/0/edit
        const updateRouteBase = "{{ route('users.update', 0) }}"; // /users/0
        const deleteRouteBase = "{{ route('users.destroy', 0) }}";

        function getEditUrl(id) {
            return editRouteBase.replace('/0/', `/${id}/`);
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
            // Initialize the DataTable
            $('#users-table').DataTable({
                processing: true,
                serverSide: true,
                ajax: {
                    url: '{{ route('users.list') }}',
                    type: 'POST',
                    dataType: 'json',
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
                        data: 'name',
                        name: 'name'
                    },
                    {
                        data: 'email',
                        name: 'email'
                    },
                    {
                        data: 'id',
                        name: 'id',
                        render: function(data) {
                            let editUrl = getEditUrl(data); // /users/{id}/edit
                            let updateUrl = getUpdateUrl(data); // /users/{id}
                            let deleteUrl = getDeleteUrl(data);

                            return `
                            <button class="btn btn-sm btn-primary action-menu-group-btn" 
                data-edit-route="${editUrl}" 
                data-update-route="${updateUrl}" 
                data-delete-route="${deleteUrl}" 
                title="Actions">
            <i class="bx bx-dots-vertical-rounded"></i>
        </button>
                                        
                                    `;
                        },
                        orderable: false,
                        searchable: false
                    },
                ],
                autoWidth: false,
                drawCallback: function(settings) {
                    $('a').tooltip();
                }
            });
        });

        $(document).on('click', '.delete-user-btn', function(e) {
            e.preventDefault();
            $('#actionMenuGroupModal').modal('hide');
            let deleteUrl = selectedDeleteUrl;

            Swal.fire({
                title: 'Are you sure?',
                text: "This action cannot be undone!",
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#3085d6',
                cancelButtonColor: '#d33',
                confirmButtonText: 'Yes, delete it!',
                cancelButtonText: 'Cancel'
            }).then((result) => {
                if (result.isConfirmed) {
                    $.ajax({
                        url: deleteUrl,
                        type: 'DELETE',
                        data: {
                            _token: '{{ csrf_token() }}'
                        },
                        success: function(response) {
                            if (response.status === 'success') {
                                $('#users-table').DataTable().ajax.reload();
                                Swal.fire(
                                    'Deleted!',
                                    response.message,
                                    'success'
                                );
                            }
                        },
                        error: function(xhr) {
                            let msg = xhr.responseJSON && xhr.responseJSON.message ? xhr
                                .responseJSON.message : 'Failed to delete user';
                            Swal.fire('Error', msg, 'error');
                        }
                    });
                }
            });
        });
    </script>
@endpush
