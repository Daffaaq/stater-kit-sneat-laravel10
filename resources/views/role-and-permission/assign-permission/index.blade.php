@extends('layouts.main')

@section('content')
    <div class="container-xxl flex-grow-1 container-p-y">

        <div class="mb-4">
            <div class="p-3 border rounded d-flex justify-content-between" style="background-color:#f8f9fa;">
                <h6 class="m-0 fw-bold text-primary">Assign Permissions to Role</h6>
                <nav aria-label="breadcrumb">
                    <ol class="breadcrumb breadcrumb-style2 mb-0">
                        <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">Dashboard</a></li>
                        <li class="breadcrumb-item active">Assign Permission</li>
                    </ol>
                </nav>
            </div>
        </div>

        <div class="card">
            <div class="card-body">

                <table id="assign-permission-table" class="table">
                    <thead>
                        <tr>
                            <th>No</th>
                            <th>Role</th>
                            <th>Permissions</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                </table>

            </div>
        </div>
    </div>

    @include('role-and-permission.assign-permission.action-menu')
    @include('role-and-permission.assign-permission.edit')
@endsection

@push('styles')
    <style>
        .permission-search-input {
            border-radius: 0.375rem;
            font-size: 0.9rem;
            padding: 0.5rem 0.75rem;
            border: 1px solid #ced4da;
            margin-bottom: 0.5rem;
        }

        .form-check-label small {
            font-size: 0.75rem;
            color: #6c757d;
        }

        .card-body {
            padding: 1rem;
        }

        .fw-semibold {
            font-weight: 600;
        }

        .permission-search-input {
            border-radius: 0.375rem;
            font-size: 0.9rem;
            padding: 0.5rem 0.75rem;
            border: 1px solid #ced4da;
            margin-bottom: 0.5rem;
        }

        .form-check-label small {
            font-size: 0.75rem;
            color: #6c757d;
        }

        .fw-semibold {
            font-weight: 600;
        }

        /* Scrollable permission list */
        #assignRolePermissionsList {
            max-height: 60vh;
            overflow-y: auto;
            padding-right: 0.5rem;
        }

        /* Sticky group header */
        #assignRolePermissionsList .border-bottom {
            position: sticky;
            top: 0;
            background-color: #fff;
            z-index: 10;
            padding-top: 0.5rem;
            padding-bottom: 0.5rem;
        }
    </style>
@endpush

@push('scripts')
    <script>
        $(function() {
            let selectedEditUrl = '';

            let table = $('#assign-permission-table').DataTable({
                processing: true,
                serverSide: true,
                ajax: {
                    url: "{{ route('assign.list') }}",
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
                        data: 'permissions'
                    },
                    {
                        data: 'id',
                        render: function(id) {
                            return `<button class="btn btn-sm btn-primary assign-action-btn" data-id="${id}">
                     <i class="bx bx-dots-vertical-rounded"></i>
                 </button>`;
                        }
                    },
                ]
            });

            $(document).on('click', '.assign-action-btn', function() {
                let roleId = $(this).data('id');

                // simpan roleId di tombol action-menu agar bisa dipakai saat klik Edit
                $('#assignPermissionActionModal').data('role-id', roleId).modal('show');
            });

            $(document).on('click', '.edit-assign-permission-btn', function() {
                let roleId = $('#assignPermissionActionModal').data('role-id');
                let editUrl = "{{ route('assign.edit', ':id') }}".replace(':id', roleId);

                $.get(editUrl, function(res) {
                    let role = res.data.role;
                    let rolePermissions = res.data.role_permissions;
                    let groupedMenus = res.data.grouped_menus;

                    $('#assignRoleId').val(role.id);
                    $('#assignRoleName').val(role.name);

                    let html = '';

                    groupedMenus.forEach(group => {
                        html += `
<div class="mb-3 border rounded shadow-sm p-3 bg-white">
    <div class="mb-2 d-flex align-items-center border-bottom pb-2">
        <i class="${group.group.icon} me-2 text-primary fs-5"></i>
        <strong>${group.group.name}</strong>
    </div>
    <div class="row">`;

                        // Group permission
                        let groupChecked = rolePermissions.includes(group.group
                            .permission_name) ? 'checked' : '';
                        html += `
    <div class="col-md-4 mb-2">
        <div class="form-check">
            <input class="form-check-input edit-permission-checkbox" type="checkbox" name="permissions[]" id="group_perm_${group.group.id}" value="${group.group.permission_name}" ${groupChecked}>
            <label class="form-check-label fw-semibold" for="group_perm_${group.group.id}">
                ${group.group.name} Permission
                <small class="text-muted d-block">(${group.group.permission_name})</small>
            </label>
        </div>
    </div>`;

                        // Item permissions
                        group.items.forEach(obj => {
                            let item = obj.item;

                            obj.permissions.forEach(p => {
                                let checked = rolePermissions.includes(p
                                    .name) ? 'checked' : '';
                                html += `
    <div class="col-md-4 mb-2">
        <div class="form-check">
            <input class="form-check-input edit-permission-checkbox" type="checkbox" name="permissions[]" id="perm_${p.id}" value="${p.name}" ${checked}>
            <label class="form-check-label" for="perm_${p.id}">
                ${item.name} - ${p.name}
                <small class="text-muted d-block">(${p.name})</small>
            </label>
        </div>
    </div>`;
                            });
                        });

                        html += `
    </div>
</div>`;
                    });

                    $('#assignRolePermissionsList').html(html);
                    $('#editAssignPermissionForm').data('update-url',
                        "{{ route('assign.update', ':id') }}".replace(':id', role.id));

                    $('#assignPermissionActionModal').modal('hide');
                    $('#editAssignPermissionModal').modal('show');

                    // Reset search input saat modal dibuka
                    $('#permissionSearchInput').val('');
                });
            });

            // Live search/filter
            $('#permissionSearchInput').on('keyup', function() {
                let search = $(this).val().toLowerCase().trim();

                $('#assignRolePermissionsList .mb-3').each(function() {
                    let group = $(this);
                    let groupName = group.find('strong').text().toLowerCase();

                    let anyVisible = false;

                    group.find('div.col-md-4').each(function() {
                        let labelText = $(this).find('label').text().toLowerCase();
                        if (labelText.includes(search) || groupName.includes(search)) {
                            $(this).show();
                            anyVisible = true;
                        } else {
                            $(this).hide();
                        }
                    });

                    if (anyVisible || groupName.includes(search)) {
                        group.show();
                    } else {
                        group.hide();
                    }

                    if (search === '') {
                        group.show();
                        group.find('div.col-md-4').show();
                    }
                });
            });

            // Submit form
            $('#editAssignPermissionForm').submit(function(e) {
                e.preventDefault();
                let updateUrl = $(this).data('update-url');

                $.ajax({
                    url: updateUrl,
                    type: 'PUT',
                    data: $(this).serialize(),
                    success: function(res) {
                        $('#editAssignPermissionModal').modal('hide');
                        $('#assign-permission-table').DataTable().ajax.reload(null, false);
                        Swal.fire('Success', res.message, 'success');
                    },
                    error: function(xhr) {
                        let msg = xhr.responseJSON?.message || 'Something went wrong';
                        Swal.fire('Error', msg, 'error');
                    }
                });
            });

            // Reset search saat modal ditutup
            $('#editAssignPermissionModal').on('hidden.bs.modal', function() {
                // Clear input
                $('#permissionSearchInput').val('');

                // Tampilkan semua permission & grup
                $('#assignRolePermissionsList .mb-3').show();
                $('#assignRolePermissionsList .col-md-4').show();
            });

        });
    </script>
@endpush
