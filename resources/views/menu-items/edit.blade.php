<!-- Edit Menu Item Modal -->
<div class="modal fade" id="editMenuItemModal" tabindex="-1" aria-labelledby="editMenuItemModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <form id="editMenuItemForm">
            @csrf
            <input type="hidden" id="editMenuItemId" name="id">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="editMenuItemModalLabel">Edit Menu Item</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>

                <div class="modal-body">
                    <div class="mb-3">
                        <label for="editMenuItemName" class="form-label">Menu Item Name</label>
                        <input type="text" name="name" id="editMenuItemName" class="form-control" required>
                        <div class="invalid-feedback" id="editMenuItemNameError"></div>
                    </div>

                    <div class="mb-3">
                        <label for="editMenuItemPermission" class="form-label">Permission Name</label>
                        <input type="text" name="permission_name" id="editMenuItemPermission" class="form-control">
                        <div class="invalid-feedback" id="editMenuItemPermissionError"></div>
                    </div>

                    <div class="mb-3">
                        <label for="editMenuItemRoute" class="form-label">Route (optional)</label>
                        <input type="text" name="route" id="editMenuItemRoute" class="form-control">
                        <div class="invalid-feedback" id="editMenuItemRouteError"></div>
                    </div>

                    <div class="mb-3">
                        <label for="editMenuItemOrder" class="form-label">Order</label>
                        <input type="number" name="order" id="editMenuItemOrder" class="form-control" min="0"
                            required>
                        <div class="invalid-feedback" id="editMenuItemOrderError"></div>
                    </div>
                </div>

                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                    <button type="submit" class="btn btn-primary">Update Menu Item</button>
                </div>
            </div>
        </form>
    </div>
</div>
@push('scripts')
    <script>
        $(document).on('click', '#modal-edit-btn', function(e) {
            e.preventDefault();
            $('#actionMenuItemModal').modal('hide');

            const editUrl = selectedEditUrl;
            const updateUrl = selectedUpdateUrl;

            $.get(editUrl, function(response) {
                if (response.status === 'success') {
                    const data = response.data;
                    $('#editMenuItemId').val(data.id);
                    $('#editMenuItemName').val(data.name);
                    $('#editMenuItemPermission').val(data.permission_name);
                    $('#editMenuItemRoute').val(data.route);
                    $('#editMenuItemOrder').val(data.order);
                    $('#editMenuItemForm').attr('action', updateUrl);
                    $('#editMenuItemModal').modal('show');
                }
            });
        });

        $('#editMenuItemForm').submit(function(e) {
            e.preventDefault();

            // reset error
            $('#editMenuItemNameError, #editMenuItemPermissionError, #editMenuItemRouteError, #editMenuItemOrderError')
                .text('').hide();
            $('input').removeClass('is-invalid');

            const form = $(this);
            const updateUrl = form.attr('action');
            const formData = form.serializeArray();

            $.ajax({
                url: updateUrl,
                method: 'PUT',
                data: $.param(formData),
                success: function(response) {
                    if (response.status === 'success') {
                        $('#editMenuItemModal').modal('hide');
                        $('#menu-items-table').DataTable().ajax.reload();
                        Swal.fire('Success', response.message, 'success');

                        // Reload sidebar
                        $.get('{{ route('sidebar') }}', function(html) {
                            $('#sidebar-container').html(html);
                            if (typeof window.Helpers !== 'undefined' && typeof window.Helpers
                                .initSidebarToggle === 'function') {
                                window.Helpers.initSidebarToggle();
                            }
                            $.getScript('/assets/vendor/js/menu.js', function() {
                                setTimeout(function() {
                                    $('.menu-toggle').off('click').on('click',
                                        function(e) {
                                            e.preventDefault();
                                            const $item = $(this).closest(
                                                '.menu-item');
                                            const isOpen = $item.hasClass(
                                                'open');
                                            $item.siblings('.open')
                                                .removeClass('open');
                                            $item.toggleClass('open', !
                                                isOpen);
                                        });

                                    const currentUrl = window.location.href;
                                    $('.menu-item').removeClass('active open');
                                    $('a.menu-link').each(function() {
                                        let linkUrl = $(this).attr(
                                            'href');
                                        if (linkUrl && linkUrl !==
                                            'javascript:void(0);' &&
                                            currentUrl.includes(linkUrl)
                                            ) {
                                            const item = $(this)
                                                .closest('.menu-item');
                                            item.addClass('active');
                                            item.parents('.menu-item')
                                                .addClass(
                                                'open active');
                                        }
                                    });
                                }, 80);
                            });
                        });
                    }
                },
                error: function(xhr) {
                    if (xhr.status === 422) {
                        const errors = xhr.responseJSON.errors;
                        if (errors.name) {
                            $('#editMenuItemNameError').text(errors.name[0]).show();
                            $('#editMenuItemName').addClass('is-invalid');
                        }
                        if (errors.permission_name) {
                            $('#editMenuItemPermissionError').text(errors.permission_name[0]).show();
                            $('#editMenuItemPermission').addClass('is-invalid');
                        }
                        if (errors.route) {
                            $('#editMenuItemRouteError').text(errors.route[0]).show();
                            $('#editMenuItemRoute').addClass('is-invalid');
                        }
                        if (errors.order) {
                            $('#editMenuItemOrderError').text(errors.order[0]).show();
                            $('#editMenuItemOrder').addClass('is-invalid');
                        }
                    } else {
                        $('#editMenuItemModal').modal('hide');
                        Swal.fire('Error', 'Something went wrong!', 'error');
                    }
                }
            });
        });
    </script>
@endpush
