<div class="modal fade" id="editAssignUserModal" tabindex="-1">
    <div class="modal-dialog">
        <form id="editAssignUserForm">
            @csrf
            @method('PUT')
            <input type="hidden" id="assignUserId" name="user_id">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Edit Assigned Roles</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <div class="mb-3">
                        <label>User</label>
                        <input type="text" id="assignUserName" class="form-control" readonly>
                    </div>
                    <label>Roles</label>
                    <div id="assignUserRolesList"></div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                    <button type="submit" class="btn btn-primary">Update Roles</button>
                </div>
            </div>
        </form>
    </div>
</div>
