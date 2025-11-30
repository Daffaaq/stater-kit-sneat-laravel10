<!-- Edit User Modal -->
<div class="modal fade" id="editUserModal" tabindex="-1" aria-labelledby="editUserModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <form id="editUserForm">
            @csrf
            @method('PUT')
            <input type="hidden" name="user_id" id="editUserId">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="editUserModalLabel">Edit User</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>

                <div class="modal-body">
                    <div class="mb-3">
                        <label for="editName" class="form-label">Name</label>
                        <input type="text" name="name" id="editName" class="form-control" required>
                        <div class="invalid-feedback" id="editNameError"></div>
                    </div>

                    <div class="mb-3">
                        <label for="editEmail" class="form-label">Email</label>
                        <input type="email" name="email" id="editEmail" class="form-control" required>
                        <div class="invalid-feedback" id="editEmailError"></div>
                    </div>

                    <div class="mb-3">
                        <label for="editPassword" class="form-label">Password <small>(kosongkan jika tidak ingin
                                diubah)</small></label>
                        <input type="password" name="password" id="editPassword" class="form-control">
                        <div class="invalid-feedback" id="editPasswordError"></div>
                    </div>
                </div>

                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                    <button type="submit" class="btn btn-primary">Update User</button>
                </div>
            </div>
        </form>
    </div>
</div>

@push('scripts')
    <script>
        $(document).ready(function() {
            $(document).on('click', '.edit-user-btn', function(e) {
                e.preventDefault();

                $('#actionMenuGroupModal').modal('hide');
                const editUrl = selectedEditUrl;
                const updateUrl = selectedUpdateUrl;

                $.ajax({
                    url: editUrl,
                    type: 'GET',
                    success: function(response) {
                        if (response.status === 'success') {
                            let user = response.data;
                            $('#editUserId').val(user.id);
                            $('#editName').val(user.name);
                            $('#editEmail').val(user.email);
                            $('#editPassword').val('');
                            $('#editUserForm').data('update-url',
                                updateUrl); // simpan URL update
                            $('#editUserModal').modal('show');
                        }
                    },
                    error: function(xhr) {
                        let msg = xhr.responseJSON?.message || 'Something went wrong';
                        Swal.fire('Error', msg, 'error');
                    }
                });
            });
            $('#editUserForm').submit(function(e) {
                e.preventDefault();
                let updateUrl = $(this).data('update-url');

                $.ajax({
                    url: updateUrl,
                    type: 'PUT',
                    data: $(this).serialize(),
                    success: function(response) {
                        if (response.status === 'success') {
                            $('#editUserModal').modal('hide');
                            $('#users-table').DataTable().ajax.reload();
                            Swal.fire('Success', response.message, 'success');
                        }
                    },
                    error: function(xhr) {
                        if (xhr.status === 422) {
                            let errors = xhr.responseJSON.errors;
                            $('#editNameError').text(errors.name ? errors.name[0] : '').toggle(!
                                !errors.name);
                            $('#editEmailError').text(errors.email ? errors.email[0] : '')
                                .toggle(!!errors.email);
                            $('#editPasswordError').text(errors.password ? errors.password[0] :
                                '').toggle(!!errors.password);

                            $('#editName').toggleClass('is-invalid', !!errors.name);
                            $('#editEmail').toggleClass('is-invalid', !!errors.email);
                            $('#editPassword').toggleClass('is-invalid', !!errors.password);
                        } else {
                            Swal.fire('Error', 'Something went wrong!', 'error');
                        }
                    }
                });
            });


        });
    </script>
@endpush
