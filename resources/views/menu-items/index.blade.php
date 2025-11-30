@extends('layouts.main')

@section('content')
    <div class="container-xxl flex-grow-1 container-p-y">
        <!-- Breadcrumb -->
        <div class="mb-4">
            <div class="p-3 border rounded d-flex justify-content-between align-items-center"
                style="background-color: #f8f9fa;">
                <h6 class="m-0 fw-bold text-primary">Menu Items Management</h6>
                <nav aria-label="breadcrumb">
                    <ol class="breadcrumb breadcrumb-style2 mb-0">
                        <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">Dashboard</a></li>
                        <li class="breadcrumb-item"><a href="{{ route('menu-groups.index') }}">Menu Groups</a></li>
                        <li class="breadcrumb-item active" aria-current="page">{{ $menuGroup->name }}</li>
                    </ol>
                </nav>
            </div>
        </div>

        <!-- Menu Group Info -->
        <!-- Menu Group Info -->
        <div class="mb-3 p-3 border rounded" style="background-color: #f8f9fa;">
            <h5 class="fw-bold">Menu Group: {{ $menuGroup->name }}</h5>
            <p class="mb-1"><strong>Permission:</strong> {{ $menuGroup->permission_name }}</p>
        </div>

        <!-- Table Card -->
        <div class="row">
            <div class="col-12">
                <div class="card">
                    <div class="card-body">
                        <!-- Button Create -->
                        <div class="d-flex justify-content-end mb-4">
                            @can('menu-items.create')
                                <button type="button" class="btn btn-primary mb-4" data-bs-toggle="modal"
                                    data-bs-target="#createMenuItemModal">
                                    Create Menu Item
                                </button>
                            @endcan
                        </div>

                        <!-- Table -->
                        <div class="table-responsive">
                            <table id="menu-items-table" class="table" width="100%" cellspacing="0">
                                <thead>
                                    <tr>
                                        <th>No</th>
                                        <th>Name</th>
                                        <th>Permission</th>
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
        <div class="d-flex justify-content-end mt-3 mb-5">
            <a href="{{ route('menu-groups.index') }}" class="btn btn-secondary">
                <i class="bx bx-arrow-back"></i> Back to Menu Groups
            </a>
        </div>
    </div>

    @include('menu-items.create')
    @include('menu-items.edit')
    @include('menu-items.action-menu') {{-- modal pilihan actions --}}
@endsection

@push('scripts')
    <script>
        const editRouteBase = "{{ route('menu-items.edit', [$menuGroup->id, 0]) }}";
        const updateRouteBase = "{{ route('menu-items.update', [$menuGroup->id, 0]) }}";
        const deleteRouteBase = "{{ route('menu-items.destroy', [$menuGroup->id, 0]) }}";

        function getEditUrl(id) {
            return editRouteBase.replace('/0', `/${id}`);
        }

        function getUpdateUrl(id) {
            return updateRouteBase.replace('/0', `/${id}`);
        }

        function getDeleteUrl(id) {
            return deleteRouteBase.replace('/0', `/${id}`);
        }

        $(document).ready(function() {
            $('#menu-items-table').DataTable({
                processing: true,
                serverSide: true,
                ajax: {
                    url: '{{ route('menu-items.list', $menuGroup->id) }}',
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
                        data: 'name',
                        name: 'name'
                    },
                    {
                        data: 'permission_name',
                        name: 'permission_name',
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
                        render: function(data) {
                            let editUrl = getEditUrl(data);
                            let updateUrl = getUpdateUrl(data);
                            let deleteUrl = getDeleteUrl(data);

                            return `
                  <button class="btn btn-sm btn-primary action-menu-item-btn" 
                          data-edit-route="${editUrl}" 
                          data-update-route="${updateUrl}" 
                          data-delete-route="${deleteUrl}" 
                          title="Actions">
                      <i class="bx bx-dots-vertical-rounded"></i>
                  </button>`;
                        }
                    }
                ],
                autoWidth: false,
                drawCallback: function(settings) {
                    $('a').tooltip();
                }
            });
        });

        $(document).on('click', '.delete-menu-item-btn', function(e) {
            e.preventDefault();
            $('#actionMenuItemModal').modal('hide');
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
                                $('#menu-items-table').DataTable().ajax.reload();
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
