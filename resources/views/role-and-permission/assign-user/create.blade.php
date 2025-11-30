<div class="modal fade" id="createAssignUserModal" tabindex="-1">
    <div class="modal-dialog">
        <form id="createAssignUserForm">
            @csrf
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Assign Roles to User</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <div class="mb-3">
                        <label>User</label>
                        <select name="user_id" class="form-select" required>
                            <option value="">Select User</option>
                            @foreach ($users as $user)
                                <option value="{{ $user->id }}">{{ $user->name }} ({{ $user->email }})</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="mb-3">
                        <label>Roles</label>
                        @foreach ($roles as $role)
                            <div class="form-check">
                                <input class="form-check-input" type="checkbox" name="roles[]"
                                    value="{{ $role->name }}">
                                <label class="form-check-label">{{ $role->name }}</label>
                            </div>
                        @endforeach
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                    <button type="submit" class="btn btn-primary">Assign Roles</button>
                </div>
            </div>
        </form>
    </div>
</div>
