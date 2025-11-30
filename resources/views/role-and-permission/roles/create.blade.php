<!-- Create Role Modal -->
<div class="modal fade" id="createRoleModal" tabindex="-1" aria-labelledby="createRoleModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <form id="createRoleForm">
            @csrf
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="createRoleModalLabel">Create Role</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>

                <div class="modal-body">
                    <div class="mb-3">
                        <label for="roleName" class="form-label">Role Name</label>
                        <input type="text" name="name" id="roleName" class="form-control" required>
                        <div class="invalid-feedback" id="roleNameError"></div>
                    </div>

                    <div class="mb-3">
                        <label for="guardName" class="form-label">Guard Name</label>
                        <input type="text" name="guard_name" id="guardName" class="form-control" value="web">
                        <div class="invalid-feedback" id="guardNameError"></div>
                    </div>
                </div>

                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                    <button type="submit" class="btn btn-primary">Create Role</button>
                </div>
            </div>
        </form>
    </div>
</div>

@push('scripts')
    <script>
        $(document).ready(function() {

            $('#createRoleForm').submit(function(e) {
                e.preventDefault();

                // reset error
                $('#roleNameError, #guardNameError').text('').hide();
                $('#roleName, #guardName').removeClass('is-invalid');

                $.ajax({
                    url: "{{ route('roles.store') }}",
                    method: "POST",
                    data: $(this).serialize(),
                    success: function(response) {
                        if (response.status === 'success') {
                            $('#createRoleModal').modal('hide');
                            $('#roles-table').DataTable().ajax.reload();

                            Swal.fire({
                                icon: 'success',
                                title: 'Success',
                                text: response.message
                            });

                            $('#createRoleForm')[0].reset();
                        }
                    },
                    error: function(xhr) {

                        if (xhr.status === 422) {
                            let errors = xhr.responseJSON.errors;

                            if (errors.name) {
                                $('#roleNameError').text(errors.name[0]).show();
                                $('#roleName').addClass('is-invalid');
                            }
                            if (errors.guard_name) {
                                $('#guardNameError').text(errors.guard_name[0]).show();
                                $('#guardName').addClass('is-invalid');
                            }
                        } else {
                            Swal.fire('Error', 'Something went wrong!', 'error');
                        }
                    }
                });

            });

        });
    </script>
@endpush
