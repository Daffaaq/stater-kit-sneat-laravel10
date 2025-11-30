<div class="modal fade" id="editPermissionModal" tabindex="-1">
    <div class="modal-dialog">
        <form id="editPermissionForm">
            @csrf
            @method('PUT')

            <input type="hidden" id="editPermissionId">

            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Edit Permission</h5>
                    <button class="btn-close" data-bs-dismiss="modal"></button>
                </div>

                <div class="modal-body">

                    <div class="mb-3">
                        <label class="form-label">Permission Name</label>
                        <input type="text" id="editPermissionName" name="name" class="form-control">
                        <div class="invalid-feedback" id="editPermissionNameError"></div>
                    </div>

                    <div class="mb-3">
                        <label class="form-label">Guard</label>
                        <input type="text" id="editPermissionGuard" name="guard_name" class="form-control">
                        <div class="invalid-feedback" id="editPermissionGuardError"></div>
                    </div>

                </div>

                <div class="modal-footer">
                    <button class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                    <button class="btn btn-primary">Update Permission</button>
                </div>

            </div>
        </form>
    </div>
</div>

@push('scripts')
    <script>
        $(document).on('click', '.edit-permission-btn', function() {
            $('#actionMenuPermissionModal').modal('hide');

            $.ajax({
                url: selectedEditUrl,
                type: 'GET',
                success: function(res) {
                    let p = res.data;

                    $('#editPermissionId').val(p.id);
                    $('#editPermissionName').val(p.name);
                    $('#editPermissionGuard').val(p.guard_name);

                    $('#editPermissionForm').data('update-url', selectedUpdateUrl);

                    $('#editPermissionModal').modal('show');
                },
                error: function(xhr) {
                    let msg = xhr.responseJSON?.message || 'Something went wrong';
                    Swal.fire('Error', msg, 'error');
                }
            });
        });

        $('#editPermissionForm').submit(function(e) {
            e.preventDefault();

            let url = $(this).data('update-url');

            $.ajax({
                url: url,
                type: "POST",
                data: $(this).serialize(),
                success: function(res) {
                    $('#editPermissionModal').modal('hide');
                    $('#permissions-table').DataTable().ajax.reload();
                    Swal.fire('Success', res.message, 'success');
                },
                error: function(xhr) {
                    if (xhr.status === 422) {
                        let e = xhr.responseJSON.errors;

                        $('#editPermissionNameError').text(e.name ? e.name[0] : '');
                        $('#editPermissionGuardError').text(e.guard_name ? e.guard_name[0] : '');

                        $('#editPermissionName').toggleClass('is-invalid', !!e.name);
                        $('#editPermissionGuard').toggleClass('is-invalid', !!e.guard_name);
                    }
                }
            });
        });
    </script>
@endpush
