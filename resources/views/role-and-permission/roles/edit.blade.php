<!-- Edit Role Modal -->
<div class="modal fade" id="editRoleModal" tabindex="-1" aria-labelledby="editRoleModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <form id="editRoleForm">
            @csrf
            @method('PUT')

            <input type="hidden" name="role_id" id="editRoleId">

            <div class="modal-content">

                <div class="modal-header">
                    <h5 class="modal-title" id="editRoleModalLabel">Edit Role</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>

                <div class="modal-body">

                    <div class="mb-3">
                        <label for="editRoleName" class="form-label">Role Name</label>
                        <input type="text" name="name" id="editRoleName" class="form-control" required>
                        <div class="invalid-feedback" id="editRoleNameError"></div>
                    </div>

                    <div class="mb-3">
                        <label for="editGuardName" class="form-label">Guard Name</label>
                        <input type="text" name="guard_name" id="editGuardName" class="form-control">
                        <div class="invalid-feedback" id="editGuardNameError"></div>
                    </div>

                </div>

                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                    <button type="submit" class="btn btn-primary">Update Role</button>
                </div>

            </div>

        </form>
    </div>
</div>

@push('scripts')
    <script>
        $(document).ready(function() {

            // open edit modal
            $(document).on('click', '.edit-role-btn', function() {

                $('#actionMenuGroupModal').modal('hide');

                $.ajax({
                    url: selectedEditUrl,
                    method: 'GET',
                    success: function(response) {

                        if (response.status === 'success') {

                            let role = response.data;

                            $('#editRoleId').val(role.id);
                            $('#editRoleName').val(role.name);
                            $('#editGuardName').val(role.guard_name);

                            $('#editRoleForm').data('update-url', selectedUpdateUrl);
                            $('#editRoleModal').modal('show');
                        }
                    },
                    error: function(xhr) {
                        let msg = xhr.responseJSON?.message || 'Something went wrong';
                        Swal.fire('Error', msg, 'error');
                    }
                });
            });

            // submit edit
            $('#editRoleForm').submit(function(e) {
                e.preventDefault();

                let updateUrl = $(this).data('update-url');

                $.ajax({
                    url: updateUrl,
                    type: 'PUT',
                    data: $(this).serialize(),
                    success: function(response) {

                        if (response.status === 'success') {
                            $('#editRoleModal').modal('hide');
                            $('#roles-table').DataTable().ajax.reload();

                            Swal.fire('Success', response.message, 'success');
                        }
                    },
                    error: function(xhr) {

                        if (xhr.status === 422) {
                            let errors = xhr.responseJSON.errors;

                            $('#editRoleNameError').text(errors.name ? errors.name[0] : '')
                                .toggle(!!errors.name);
                            $('#editGuardNameError').text(errors.guard_name ? errors.guard_name[
                                0] : '').toggle(!!errors.guard_name);

                            $('#editRoleName').toggleClass('is-invalid', !!errors.name);
                            $('#editGuardName').toggleClass('is-invalid', !!errors.guard_name);

                        } else {
                            Swal.fire('Error', 'Something went wrong!', 'error');
                        }
                    }
                });

            });

        });
    </script>
@endpush
