@extends('layouts.main')

@section('content')
    <div class="container-xxl flex-grow-1 container-p-y">

        <!-- Header -->
        <div class="mb-4">
            <div class="p-3 border rounded d-flex justify-content-between" style="background-color:#f8f9fa;">
                <h6 class="m-0 fw-bold text-primary">Assign Role to User</h6>
                <nav aria-label="breadcrumb">
                    <ol class="breadcrumb breadcrumb-style2 mb-0">
                        <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">Dashboard</a></li>
                        <li class="breadcrumb-item active">Assign User</li>
                    </ol>
                </nav>
            </div>
        </div>

        <!-- Card -->
        <div class="card">
            <div class="card-body">

                <div class="d-flex justify-content-end mb-4">
                    @can('assign.create')
                        <button class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#createAssignUserModal">
                            Assign User
                        </button>
                    @endcan
                </div>

                <table id="assign-user-table" class="table table-striped table-bordered">
                    <thead>
                        <tr>
                            <th>No</th>
                            <th>User</th>
                            <th>Email</th>
                            <th>Roles</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                </table>

            </div>
        </div>

    </div>

    @include('role-and-permission.assign-user.create')
    @include('role-and-permission.assign-user.edit')
    @include('role-and-permission.assign-user.action-menu')
@endsection

@push('scripts')
    <script>
        $(document).ready(function() {
            const editBase = "{{ route('assign.user.edit', 0) }}";
            const updateBase = "{{ route('assign.user.update', 0) }}";

            function getEditUrl(id) {
                return editBase.replace('/0/edit', `/${id}/edit`);
            }

            function getUpdateUrl(id) {
                return updateBase.replace('/0', `/${id}`);
            }

            // DataTable
            const table = $('#assign-user-table').DataTable({
                processing: true,
                serverSide: true,
                ajax: {
                    url: "{{ route('assign.user.list') }}",
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
                        data: 'email'
                    },
                    {
                        data: 'roles'
                    },
                    {
                        data: 'id',
                        render: function(id) {
                            let edit = getEditUrl(id);
                            let update = getUpdateUrl(id);
                            return `<button class="btn btn-sm btn-primary assign-action-btn"
                                data-edit="${edit}" data-update="${update}">
                                <i class="bx bx-dots-vertical-rounded"></i>
                            </button>`;
                        }
                    }
                ]
            });

            // Action Modal
            let selectedEditUrl = '';
            let selectedUpdateUrl = '';
            $(document).on('click', '.assign-action-btn', function() {
                selectedEditUrl = $(this).data('edit');
                selectedUpdateUrl = $(this).data('update');
                $('#assignUserActionModal').modal('show');
            });

            // Edit button in Action Menu
            $(document).on('click', '.edit-assign-btn', function() {
                $('#assignUserActionModal').modal('hide');

                $.get(selectedEditUrl, function(res) {
                    if (res.status === 'success') {
                        let user = res.data.user;
                        let userRoles = res.data.user_roles;
                        let roles = res.data.roles;

                        $('#assignUserId').val(user.id);
                        $('#assignUserName').val(user.name);

                        let html = '';
                        roles.forEach(role => {
                            let checked = userRoles.includes(role.name) ? 'checked' : '';
                            html += `
<div class="form-check">
    <input class="form-check-input edit-role-checkbox" type="checkbox" name="roles[]" value="${role.name}" ${checked}>
    <label class="form-check-label">${role.name}</label>
</div>`;
                        });
                        $('#assignUserRolesList').html(html);
                        $('#editAssignUserForm').data('update-url', selectedUpdateUrl);
                        $('#editAssignUserModal').modal('show');
                    },
                    error: function(xhr) {
                        let msg = xhr.responseJSON?.message || 'Something went wrong';
                        Swal.fire('Error', msg, 'error');
                    }
                });
            });

            // Submit Edit Form
            $('#editAssignUserForm').submit(function(e) {
                e.preventDefault();
                let updateUrl = $(this).data('update-url');
                $.ajax({
                    url: updateUrl,
                    type: 'POST',
                    data: $(this).serialize(),
                    success: function(res) {
                        $('#editAssignUserModal').modal('hide');
                        table.ajax.reload(null, false);
                        Swal.fire('Success', res.message, 'success');
                    },
                    error: function(xhr) {
                        let msg = xhr.responseJSON?.message || 'Something went wrong';
                        Swal.fire('Error', msg, 'error');
                    }
                });
            });

            // Submit Create Form
            $('#createAssignUserForm').submit(function(e) {
                e.preventDefault();
                $.ajax({
                    url: "{{ route('assign.user.store') }}",
                    type: 'POST',
                    data: $(this).serialize(),
                    success: function(res) {
                        if (res.status === 'success') {
                            $('#createAssignUserModal').modal('hide');
                            table.ajax.reload(null, false);
                            Swal.fire('Success', res.message, 'success');
                            $('#createAssignUserForm')[0].reset();
                        }
                    },
                    error: function(xhr) {
                        let msg = xhr.responseJSON?.message || 'Something went wrong';
                        Swal.fire('Error', msg, 'error');
                    }
                });
            });

            // Reset Create Form on Modal Close
            $('#createAssignUserModal').on('hidden.bs.modal', function() {
                $('#createAssignUserForm')[0].reset();
            });

            // Reset Edit Form on Modal Close
            $('#editAssignUserModal').on('hidden.bs.modal', function() {
                $('#editAssignUserForm')[0].reset();
            });
        });
    </script>
@endpush
