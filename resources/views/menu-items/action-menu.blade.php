<!-- Action Modal -->
<div class="modal fade" id="actionMenuItemModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Choose Action</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body d-flex justify-content-around">
                <button type="button" class="btn btn-warning" id="modal-edit-btn">
                    <i class="bx bx-edit"></i> Edit
                </button>
                <button type="button" class="btn btn-danger delete-menu-item-btn" id="modal-delete-btn">
                    <i class="bx bx-trash"></i> Delete
                </button>
            </div>
        </div>
    </div>
</div>
@push('scripts')
    <script>
        let selectedEditUrl = '';
        let selectedUpdateUrl = '';
        let selectedDeleteUrl = '';

        $(document).on('click', '.action-menu-item-btn', function() {
            selectedEditUrl = $(this).data('edit-route');
            selectedUpdateUrl = $(this).data('update-route');
            selectedDeleteUrl = $(this).data('delete-route');
            $('#actionMenuItemModal').modal('show');
        });
    </script>
@endpush
