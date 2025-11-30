<!-- Edit Menu Group Modal -->
<div class="modal fade" id="editMenuGroupModal" tabindex="-1" aria-labelledby="editMenuGroupModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <form id="editMenuGroupForm">
            @csrf
            <input type="hidden" id="editMenuGroupId" name="id">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="editMenuGroupModalLabel">Edit Menu Group</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>

                <div class="modal-body">
                    <div class="mb-3">
                        <label for="editName" class="form-label">Menu Group Name</label>
                        <input type="text" name="name" id="editName" class="form-control" required>
                        <div class="invalid-feedback" id="editNameError"></div>
                    </div>

                    <div class="mb-3">
                        <label for="editPermissionName" class="form-label">Permission Name</label>
                        <input type="text" name="permission_name" id="editPermissionName" class="form-control"
                            required>
                        <div class="invalid-feedback" id="editPermissionNameError"></div>
                    </div>

                    <div class="mb-3">
                        <label for="editIcon" class="form-label">Icon</label>
                        <input type="text" name="icon" id="editIcon" class="form-control" required>
                        <div class="invalid-feedback" id="editIconError"></div>
                    </div>

                    <div class="mb-3">
                        <label for="editRoute" class="form-label">Route (optional)</label>
                        <input type="text" name="route" id="editRoute" class="form-control">
                        <div class="invalid-feedback" id="editRouteError"></div>
                    </div>

                    <div class="mb-3">
                        <label for="editOrder" class="form-label">Order</label>
                        <input type="number" name="order" id="editOrder" class="form-control" min="0"
                            required>
                        <div class="invalid-feedback" id="editOrderError"></div>
                    </div>
                </div>

                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                    <button type="submit" class="btn btn-primary">Update Menu Group</button>
                </div>
            </div>
        </form>
    </div>
</div>

@push('scripts')
    <script>
        // Fill form when edit button clicked
        $(document).on('click', '.edit-menu-group-btn', function(e) {
            e.preventDefault(); // cegah scroll ke top / # muncul
            $('#actionMenuGroupModal').modal('hide');
            const editUrl = selectedEditUrl;
            const updateUrl = selectedUpdateUrl;

            $.get(editUrl, function(response) {
                if (response.status === 'success') {
                    const data = response.data;
                    $('#editMenuGroupId').val(data.id);
                    $('#editName').val(data.name);
                    $('#editPermissionName').val(data.permission_name);
                    $('#editIcon').val(data.icon);
                    $('#editRoute').val(data.route);
                    $('#editOrder').val(data.order);
                    $('#editMenuGroupForm').attr('action', updateUrl);
                    $('#editMenuGroupModal').modal('show');
                }
            });
        });
        $(document).ready(function() {

            $('#editMenuGroupForm').submit(function(e) {
                e.preventDefault();

                // Reset errors
                $('#editNameError, #editPermissionNameError, #editIconError, #editRouteError, #editOrderError')
                    .text('').hide();
                $('input').removeClass('is-invalid');

                const formData = $(this).serializeArray();
                const orderInput = parseInt($('#editOrder').val());
                const form = $(this);
                const updateUrl = form.attr('action'); // ambil URL dari atribut action form

                // Fetch all menu groups to check conflicts
                $.ajax({
                    url: '{{ route('menu-group.list') }}',
                    type: 'POST',
                    data: {
                        _token: '{{ csrf_token() }}'
                    },
                    success: function(response) {
                        const id = $('#editMenuGroupId').val();
                        let existingOrders = response.data
                            .filter(g => g.id != id) // exclude current menu group
                            .map(g => g.order);
                        let maxOrder = Math.max(...existingOrders);

                        if (orderInput > maxOrder + 1) {
                            $('#editMenuGroupModal').modal('hide');
                            Swal.fire({
                                title: 'Order Too High',
                                text: `The order you entered (${orderInput}) exceeds the current max order (${maxOrder}). It will be set to ${maxOrder + 1}. Continue?`,
                                icon: 'info',
                                showCancelButton: true,
                                confirmButtonText: 'Proceed',
                                cancelButtonText: 'Cancel'
                            }).then(result => {
                                if (result.isConfirmed) {
                                    $('#editOrder').val(maxOrder + 1);
                                    submitEditMenuGroup(updateUrl, formData);
                                }
                            });
                        } else if (existingOrders.includes(orderInput)) {
                            $('#editMenuGroupModal').modal('hide');
                            Swal.fire({
                                title: 'Order Conflict',
                                text: 'This order already exists. Do you want to shift other menu groups?',
                                icon: 'warning',
                                showCancelButton: true,
                                confirmButtonText: 'Yes, shift!',
                                cancelButtonText: 'Cancel'
                            }).then(result => {
                                if (result.isConfirmed) {
                                    submitEditMenuGroup(updateUrl, formData);
                                }
                            });
                        } else {
                            submitEditMenuGroup(updateUrl, formData);
                        }
                    }
                });
            });

            function submitEditMenuGroup(url, data) {
                $.ajax({
                    url: url,
                    method: 'PUT',
                    data: $.param(data),
                    success: function(response) {
                        if (response.status === 'success') {
                            $('#editMenuGroupModal').modal('hide');
                            $('#menu-groups-table').DataTable().ajax.reload();
                            Swal.fire('Success', response.message, 'success');

                            $.get('{{ route('sidebar') }}', function(html) {
                                $('#sidebar-container').html(html);

                                // Init sidebar toggle (INI ADA DI VERSI KAMU)
                                if (typeof window.Helpers !== 'undefined' && typeof window
                                    .Helpers.initSidebarToggle === 'function') {
                                    window.Helpers.initSidebarToggle();
                                    console.log('initSidebarToggle');
                                }

                                // Reload menu.js
                                $.getScript('/assets/vendor/js/menu.js', function() {
                                    console.log('menu.js reloaded');

                                    // Re-bind menu toggle manually (WAJIB AGAR SUBMENU BISA DIBUKA)
                                    setTimeout(function() {
                                        // REBIND MENU TOGGLE
                                        $('.menu-toggle').off('click').on(
                                            'click',
                                            function(e) {
                                                e.preventDefault();
                                                const $item = $(this)
                                                    .closest('.menu-item');
                                                const isOpen = $item
                                                    .hasClass('open');
                                                $item.siblings('.open')
                                                    .removeClass('open');
                                                $item.toggleClass('open', !
                                                    isOpen);
                                            });

                                        // SET ACTIVE STATE
                                        const currentUrl = window.location.href;

                                        $('.menu-item').removeClass(
                                            'active open');

                                        $('a.menu-link').each(function() {
                                            let linkUrl = $(this).attr(
                                                'href');
                                            if (linkUrl && linkUrl !==
                                                'javascript:void(0);' &&
                                                currentUrl.includes(
                                                    linkUrl)) {
                                                const item = $(this)
                                                    .closest(
                                                        '.menu-item');
                                                item.addClass('active');
                                                item.parents(
                                                        '.menu-item')
                                                    .addClass(
                                                        'open active');
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
                        if (xhr.status === 422) {
                            let errors = xhr.responseJSON.errors;
                            if (errors.name) {
                                $('#editNameError').text(errors.name[0]).show();
                                $('#editName').addClass('is-invalid');
                            }
                            if (errors.permission_name) {
                                $('#editPermissionNameError').text(errors.permission_name[0]).show();
                                $('#editPermissionName').addClass('is-invalid');
                            }
                            if (errors.icon) {
                                $('#editIconError').text(errors.icon[0]).show();
                                $('#editIcon').addClass('is-invalid');
                            }
                            if (errors.route) {
                                $('#editRouteError').text(errors.route[0]).show();
                                $('#editRoute').addClass('is-invalid');
                            }
                            if (errors.order) {
                                $('#editOrderError').text(errors.order[0]).show();
                                $('#editOrder').addClass('is-invalid');
                            }
                        } else {
                            $('#editMenuGroupModal').modal('hide');
                            Swal.fire('Error', 'Something went wrong!', 'error');
                        }
                    }
                });
            }


        });
    </script>
@endpush
