@extends('layouts.main')

@section('content')
<div class="container-xxl flex-grow-1 container-p-y">

    <!-- Breadcrumb in box with title -->
    <div class="mb-4">
        <div class="p-3 border rounded d-flex justify-content-between align-items-center"
             style="background-color: #f8f9fa;">

            <!-- Title kiri -->
            <h6 class="m-0 fw-bold text-primary">Role Management</h6>

            <!-- Breadcrumb kanan -->
            <nav aria-label="breadcrumb">
                <ol class="breadcrumb breadcrumb-style2 mb-0">
                    <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">Dashboard</a></li>
                    <li class="breadcrumb-item active" aria-current="page">Roles</li>
                </ol>
            </nav>
        </div>
    </div>


    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-body">

                    <!-- Button Create Role -->
                    <div class="d-flex justify-content-end mb-4">
                        @can('roles.create')
                            <button type="button" class="btn btn-primary mb-4" 
                                    data-bs-toggle="modal"
                                    data-bs-target="#createRoleModal">
                                Create Role
                            </button>
                        @endcan
                    </div>

                    <!-- Table -->
                    <div class="table-responsive">
                        <table id="roles-table" class="table" width="100%" cellspacing="0">
                            <thead>
                                <tr>
                                    <th>No</th>
                                    <th>Role Name</th>
                                    <th>Guard</th>
                                    <th>Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                <!-- Filled automatically by Datatables -->
                            </tbody>
                        </table>
                    </div>

                </div>
            </div>
        </div>
    </div>

</div>

{{-- Include Modals --}}
@include('role-and-permission.roles.create')
@include('role-and-permission.roles.edit')
@include('role-and-permission.roles.action-menu')

@endsection

@push('scripts')
<script>
    // Base route mapping
    const editRouteBase = "{{ route('roles.edit', 0) }}"; 
    const updateRouteBase = "{{ route('roles.update', 0) }}"; 
    const deleteRouteBase = "{{ route('roles.destroy', 0) }}";

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
$(document).ready(function () {

    $('#roles-table').DataTable({
        processing: true,
        serverSide: true,
        ajax: {
            url: '{{ route('role.list') }}',
            type: 'POST',
            data: { _token: '{{ csrf_token() }}' }
        },
        columns: [
            {
                data: 'DT_RowIndex',
                name: 'DT_RowIndex',
                orderable: false,
                searchable: false
            },
            { data: 'name', name: 'name' },
            { data: 'guard_name', name: 'guard_name' },
            {
                data: 'id',
                orderable: false,
                searchable: false,
                render: function (id) {
                    let editUrl = getEditUrl(id);
                    let updateUrl = getUpdateUrl(id);
                    let deleteUrl = getDeleteUrl(id);

                    return `
                        <button class="btn btn-sm btn-primary action-menu-group-btn"
                                data-edit-route="${editUrl}" 
                                data-update-route="${updateUrl}" 
                                data-delete-route="${deleteUrl}">
                            <i class="bx bx-dots-vertical-rounded"></i>
                        </button>`;
                }
            },
        ],
        autoWidth: false,
        drawCallback: function(settings) {
            $('a').tooltip();
        }
    });
});

$(document).on('click', '.delete-role-btn', function(e) {
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
                data: { _token: '{{ csrf_token() }}' },
                success: function(response) {
                    if (response.status === 'success') {
                        $('#roles-table').DataTable().ajax.reload();
                        Swal.fire('Deleted!', response.message, 'success');
                    }
                }
            });

        }
    });
});
</script>
@endpush
