@extends('layouts.main')

@section('content')
    <div class="container-xxl flex-grow-1 container-p-y">
        <!-- Breadcrumb -->
        <div class="mb-4">
            <div class="p-3 border rounded d-flex justify-content-between align-items-center"
                style="background-color: #f8f9fa;">
                <h6 class="m-0 fw-bold text-primary">Menu Groups Management</h6>
                <nav aria-label="breadcrumb">
                    <ol class="breadcrumb breadcrumb-style2 mb-0">
                        <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">Dashboard</a></li>
                        <li class="breadcrumb-item active" aria-current="page">Menu Groups</li>
                    </ol>
                </nav>
            </div>
        </div>

        <!-- Table Card -->
        <div class="row">
            <div class="col-12">
                <div class="card">
                    <div class="card-body">
                        <!-- Button Create -->
                        <div class="d-flex justify-content-end mb-4">
                            @can('menu-groups.create')
                                <button type="button" class="btn btn-primary mb-4" data-bs-toggle="modal"
                                    data-bs-target="#createMenuGroupModal">
                                    Create Menu Group
                                </button>
                            @endcan
                        </div>

                        <!-- Table -->
                        <div class="table-responsive">
                            <table id="menu-groups-table" class="table" width="100%" cellspacing="0">
                                <thead>
                                    <tr>
                                        <th>No</th>
                                        <th>Name</th>
                                        <th>Permission</th>
                                        <th>Icon</th>
                                        <th>Route</th>
                                        <th>Order</th>
                                        <th>Actions</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <!-- DataTables -->
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    @include('menu-groups.create')
    @include('menu-groups.edit')
    @include('menu-groups.action-menu')
@endsection

@push('styles')
    <style>
    </style>
@endpush

@push('scripts')
    <script>
        const editRouteBase = "{{ route('menu-groups.edit', 0) }}";
        const updateRouteBase = "{{ route('menu-groups.update', 0) }}";
        const deleteRouteBase = "{{ route('menu-groups.destroy', 0) }}";
        const getMenuItemsRouteBase = "{{ route('menu-items.index', 0) }}";

        function getEditUrl(id) {
            return editRouteBase.replace('/0/', `/${id}/`);
        }

        function getUpdateUrl(id) {
            return updateRouteBase.replace('/0', `/${id}`);
        }

        function getDeleteUrl(id) {
            return deleteRouteBase.replace('/0', `/${id}`);
        }

        function getMenuItemsUrl(id) {
            return getMenuItemsRouteBase.replace('/0', `/${id}`);
        }

        $(document).ready(function() {
            @if (session('error'))
                Swal.fire({
                    icon: 'error',
                    title: 'Oops...',
                    text: "{{ session('error') }}",
                    confirmButtonColor: '#3085d6',
                    confirmButtonText: 'OK'
                });
            @endif
            $('#menu-groups-table').DataTable({
                processing: true,
                serverSide: true,
                ajax: {
                    url: '{{ route('menu-group.list') }}',
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
                        data: 'permission_name',
                        name: 'permission_name'
                    },
                    {
                        data: 'icon',
                        name: 'icon'
                    },
                    {
                        data: 'route',
                        name: 'route',
                        render: function(data) {
                            return data ? data : '-';
                        }
                    },
                    {
                        data: 'order',
                        name: 'order'
                    },
                    {
                        data: 'id',
                        name: 'id',
                        orderable: false,
                        searchable: false,
                        render: function(data, type, row) {
                            let editUrl = getEditUrl(data);
                            let updateUrl = getUpdateUrl(data);
                            let deleteUrl = getDeleteUrl(data);
                            let menuItemUrl = getMenuItemsUrl(data);
                            let routeData = row.route ? row.route : '-';

                            return `
                                        <button class="btn btn-sm btn-primary action-menu-group-btn" 
                                                data-edit-route="${editUrl}" 
                                                data-update-route="${updateUrl}" 
                                                data-delete-route="${deleteUrl}" 
                                                data-menu-item-url="${menuItemUrl}"
                                                data-route="${routeData}"
                                                title="Actions">
                                            <i class="bx bx-dots-vertical-rounded"></i>
                                        </button>
                                    `;
                        }
                    }
                ],
                autoWidth: false,
                drawCallback: function(settings) {
                    $('a').tooltip();
                }
            });
        });

        $(document).on('click', '.delete-menu-group-btn', function(e) {
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
                                $('#menu-groups-table').DataTable().ajax.reload();
                                Swal.fire('Deleted!', response.message, 'success');

                                $.get('{{ route('sidebar') }}', function(html) {
                                    $('#sidebar-container').html(html);

                                    // Init sidebar toggle (INI ADA DI VERSI KAMU)
                                    if (typeof window.Helpers !== 'undefined' &&
                                        typeof window
                                        .Helpers.initSidebarToggle === 'function') {
                                        window.Helpers.initSidebarToggle();
                                        console.log('initSidebarToggle');
                                    }

                                    // Reload menu.js
                                    $.getScript('/assets/vendor/js/menu.js',
                                        function() {
                                            console.log('menu.js reloaded');

                                            // Re-bind menu toggle manually (WAJIB AGAR SUBMENU BISA DIBUKA)
                                            setTimeout(function() {
                                                // REBIND MENU TOGGLE
                                                $('.menu-toggle').off(
                                                    'click').on(
                                                    'click',
                                                    function(e) {
                                                        e
                                                            .preventDefault();
                                                        const $item = $(
                                                                this)
                                                            .closest(
                                                                '.menu-item'
                                                            );
                                                        const isOpen =
                                                            $item
                                                            .hasClass(
                                                                'open');
                                                        $item.siblings(
                                                                '.open')
                                                            .removeClass(
                                                                'open');
                                                        $item
                                                            .toggleClass(
                                                                'open',
                                                                !
                                                                isOpen);
                                                    });

                                                // SET ACTIVE STATE
                                                const currentUrl = window
                                                    .location.href;

                                                $('.menu-item').removeClass(
                                                    'active open');

                                                $('a.menu-link').each(
                                                    function() {
                                                        let linkUrl = $(
                                                                this)
                                                            .attr(
                                                                'href');
                                                        if (linkUrl &&
                                                            linkUrl !==
                                                            'javascript:void(0);' &&
                                                            currentUrl
                                                            .includes(
                                                                linkUrl)
                                                        ) {
                                                            const item =
                                                                $(this)
                                                                .closest(
                                                                    '.menu-item'
                                                                );
                                                            item.addClass(
                                                                'active'
                                                            );
                                                            item.parents(
                                                                    '.menu-item'
                                                                )
                                                                .addClass(
                                                                    'open active'
                                                                );
                                                        }
                                                    });

                                                console.log(
                                                    'Menu toggle manually rebound + active restored'
                                                );

                                            }, 80);

                                        });
                                });
                            }
                        },
                        error: function(xhr) {
                            let msg = xhr.responseJSON?.message ||
                                'Failed to delete Menu Group';
                            Swal.fire('Error', msg, 'error');
                        }
                    });
                }
            });
        });
    </script>
@endpush
