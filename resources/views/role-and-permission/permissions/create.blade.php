<div class="modal fade" id="createPermissionModal" tabindex="-1">
    <div class="modal-dialog">
        <form id="createPermissionForm">
            @csrf
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Create Permission</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>

                <div class="modal-body">

                    <div class="mb-3">
                        <label class="form-label">Permission Name</label>
                        <input type="text" name="name" class="form-control">
                        <div class="invalid-feedback" id="createPermissionNameError"></div>
                    </div>

                    <div class="mb-3">
                        <label class="form-label">Guard Name</label>
                        <input type="text" name="guard_name" class="form-control" value="web">
                        <div class="invalid-feedback" id="createPermissionGuardError"></div>
                    </div>

                </div>

                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                    <button type="submit" class="btn btn-primary">Create Permission</button>
                </div>
            </div>
        </form>
    </div>
</div>

@push('scripts')
    <script>
        $('#createPermissionForm').submit(function(e) {
            e.preventDefault();

            $('input').removeClass('is-invalid');

            $.ajax({
                url: "{{ route('permissions.store') }}",
                method: 'POST',
                data: $(this).serialize(),
                success: function(res) {
                    $('#createPermissionModal').modal('hide');
                    $('#permissions-table').DataTable().ajax.reload();
                    Swal.fire('Success', res.message, 'success');
                    $('#createPermissionForm')[0].reset();
                },
                error: function(xhr) {
                    if (xhr.status === 422) {
                        let e = xhr.responseJSON.errors;
                        if (e.name) {
                            $('#createPermissionNameError').text(e.name[0]).show();
                            $('input[name="name"]').addClass('is-invalid');
                        }
                        if (e.guard_name) {
                            $('#createPermissionGuardError').text(e.guard_name[0]).show();
                            $('input[name="guard_name"]').addClass('is-invalid');
                        }
                    }
                }
            });
        });
    </script>
@endpush
