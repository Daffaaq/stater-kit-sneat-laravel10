<!-- Create Menu Group Modal -->
<div class="modal fade" id="createMenuGroupModal" tabindex="-1" aria-labelledby="createMenuGroupModalLabel"
    aria-hidden="true">
    <div class="modal-dialog">
        <form id="createMenuGroupForm">
            @csrf
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="createMenuGroupModalLabel">Create Menu Group</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>

                <div class="modal-body">
                    <div class="mb-3">
                        <label for="name" class="form-label">Menu Group Name</label>
                        <input type="text" name="name" id="name" class="form-control" required>
                        <div class="invalid-feedback" id="nameError"></div>
                    </div>

                    <div class="mb-3">
                        <label for="permission_name" class="form-label">Permission Name</label>
                        <input type="text" name="permission_name" id="permission_name" class="form-control" required>
                        <div class="invalid-feedback" id="permissionNameError"></div>
                    </div>

                    <div class="mb-3">
                        <label for="icon" class="form-label">Icon</label>
                        <input type="text" name="icon" id="icon" class="form-control" required>
                        <div class="invalid-feedback" id="iconError"></div>
                    </div>

                    <div class="mb-3">
                        <label for="route" class="form-label">Route (optional)</label>
                        <input type="text" name="route" id="route" class="form-control">
                        <div class="invalid-feedback" id="routeError"></div>
                    </div>

                    <div class="mb-3">
                        <label for="order" class="form-label">Order</label>
                        <input type="number" name="order" id="order" class="form-control" min="0"
                            required>
                        <div class="invalid-feedback" id="orderError"></div>
                    </div>
                </div>

                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                    <button type="submit" class="btn btn-primary">Create Menu Group</button>
                </div>
            </div>
        </form>
    </div>
</div>

@push('scripts')
    <script>
        $(document).ready(function() {
            $('#createMenuGroupForm').submit(function(e) {
                e.preventDefault();

                // reset error messages
                $('#nameError, #permissionNameError, #iconError, #routeError, #orderError').text('').hide();
                $('input').removeClass('is-invalid');

                const formData = $(this).serializeArray();
                const orderInput = parseInt($('#order').val());

                // Fetch all menu groups
                $.ajax({
                    url: '{{ route('menu-group.list') }}', // use list route to get all menu groups
                    type: 'POST',
                    data: {
                        _token: '{{ csrf_token() }}'
                    },
                    success: function(response) {
                        let existingOrders = response.data.map(g => g.order);
                        let maxOrder = Math.max(...existingOrders);

                        if (orderInput > maxOrder + 1) {
                            // confirm for auto-correction
                            $('#createMenuGroupModal').modal('hide');
                            Swal.fire({
                                title: 'Order Too High',
                                text: `The order you entered (${orderInput}) exceeds the current number of menu groups (${maxOrder}). It will be changed to ${maxOrder + 1}. Continue?`,
                                icon: 'info',
                                showCancelButton: true,
                                confirmButtonText: 'Proceed',
                                cancelButtonText: 'Cancel'
                            }).then((result) => {
                                if (result.isConfirmed) {
                                    // update order value in form before submit
                                    $('#order').val(maxOrder + 1);
                                    submitMenuGroup(formData);
                                }
                            });
                        } else if (existingOrders.includes(orderInput)) {
                            // confirm for shifting orders
                            $('#createMenuGroupModal').modal('hide');
                            Swal.fire({
                                title: 'Order Conflict',
                                text: 'This order already exists. Do you want to shift other menu groups?',
                                icon: 'warning',
                                showCancelButton: true,
                                confirmButtonText: 'Yes, shift!',
                                cancelButtonText: 'Cancel'
                            }).then((result) => {
                                if (result.isConfirmed) {
                                    submitMenuGroup(formData); // submit form
                                }
                            });
                        } else {
                            submitMenuGroup(formData); // submit directly if order is unique
                        }
                    }
                });
            });

            function submitMenuGroup(data) {
                $.ajax({
                    url: '{{ route('menu-groups.store') }}',
                    method: 'POST',
                    data: $.param(data),
                    success: function(response) {
                        if (response.status === 'success') {
                            $('#createMenuGroupModal').modal('hide');
                            $('#menu-groups-table').DataTable().ajax.reload();
                            Swal.fire('Success', response.message, 'success');
                            $('#createMenuGroupForm')[0].reset();

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
                        if (xhr.status === 422) { // validation error
                            let errors = xhr.responseJSON.errors;
                            if (errors.name) {
                                $('#nameError').text(errors.name[0]).show();
                                $('#name').addClass('is-invalid');
                            }
                            if (errors.permission_name) {
                                $('#permissionNameError').text(errors.permission_name[0]).show();
                                $('#permission_name').addClass('is-invalid');
                            }
                            if (errors.icon) {
                                $('#iconError').text(errors.icon[0]).show();
                                $('#icon').addClass('is-invalid');
                            }
                            if (errors.route) {
                                $('#routeError').text(errors.route[0]).show();
                                $('#route').addClass('is-invalid');
                            }
                            if (errors.order) {
                                $('#orderError').text(errors.order[0]).show();
                                $('#order').addClass('is-invalid');
                            }
                        } else {
                            $('#createMenuGroupModal').modal('hide');
                            Swal.fire('Error', 'Something went wrong!', 'error');
                        }
                    }
                });
            }
        });
    </script>
@endpush
