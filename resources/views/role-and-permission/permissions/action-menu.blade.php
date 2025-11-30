<div class="modal fade" id="actionMenuPermissionModal" tabindex="-1">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Choose Action</h5>
                <button class="btn-close" data-bs-dismiss="modal"></button>
            </div>

            <div class="modal-body d-flex justify-content-around">
                <button type="button" class="btn btn-warning edit-permission-btn">
                    <i class="bx bx-edit"></i> Edit
                </button>
                <button type="button" class="btn btn-danger delete-permission-btn">
                    <i class="bx bx-trash"></i> Delete
                </button>
            </div>
        </div>
    </div>
</div>

@push('scripts')
    <script>
        $(document).on('click', '.delete-permission-btn', function() {
            $('#actionMenuPermissionModal').modal('hide');

            Swal.fire({
                title: 'Delete?',
                text: 'This action cannot be undone!',
                icon: 'warning',
                showCancelButton: true
            }).then((result) => {
                if (result.isConfirmed) {
                    $.ajax({
                        url: selectedDeleteUrl,
                        type: 'DELETE',
                        data: {
                            _token: "{{ csrf_token() }}"
                        },
                        success: function(res) {
                            Swal.fire('Success', res.message, 'success');
                            $('#permissions-table').DataTable().ajax.reload();
                        }
                    });
                }
            });
        });
    </script>
@endpush
