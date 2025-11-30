<div class="modal fade" id="editAssignPermissionModal" tabindex="-1">
    <div class="modal-dialog modal-lg"><!-- Modal lebih lebar -->
        <form id="editAssignPermissionForm">
            @csrf
            @method('PUT')
            <input type="hidden" id="assignRoleId" name="role_id">

            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Edit Assigned Permissions</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>

                <div class="modal-body">
                    <div class="mb-3">
                        <label>Role</label>
                        <input type="text" id="assignRoleName" class="form-control" readonly>
                    </div>

                    <div class="mb-3">
                        <input type="text" id="permissionSearchInput" class="form-control permission-search-input"
                            placeholder="Search permission...">
                    </div>


                    <div id="assignRolePermissionsList"></div>
                </div>

                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                    <button type="submit" class="btn btn-primary">Update Permissions</button>
                </div>
            </div>
        </form>
    </div>
</div>
