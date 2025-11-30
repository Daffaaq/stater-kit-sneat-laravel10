<!-- Create User Modal -->
<div class="modal fade" id="createUserModal" tabindex="-1" aria-labelledby="createUserModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <form id="createUserForm">
            @csrf
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="createUserModalLabel">Create User</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>

                <div class="modal-body">
                    <div class="mb-3">
                        <label for="name" class="form-label">Name</label>
                        <input type="text" name="name" id="name" class="form-control" required>
                        <div class="invalid-feedback" id="nameError"></div>
                    </div>

                    <div class="mb-3">
                        <label for="email" class="form-label">Email</label>
                        <input type="email" name="email" id="email" class="form-control" required>
                        <div class="invalid-feedback" id="emailError"></div>
                    </div>

                    <div class="mb-3">
                        <label for="password" class="form-label">Password</label>
                        <input type="password" name="password" id="password" class="form-control" required>
                        <div class="invalid-feedback" id="passwordError"></div>
                    </div>
                </div>

                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                    <button type="submit" class="btn btn-primary">Create User</button>
                </div>
            </div>
        </form>
    </div>
</div>

@push('scripts')
    <script>
        $(document).ready(function() {
            $('#createUserForm').submit(function(e) {
                e.preventDefault();

                // reset error
                $('#nameError, #emailError, #passwordError').text('').hide();
                $('input').removeClass('is-invalid');

                $.ajax({
                    url: '{{ route('users.store') }}',
                    method: 'POST',
                    data: $(this).serialize(),
                    success: function(response) {
                        if (response.status === 'success') {
                            $('#createUserModal').modal('hide');
                            $('#users-table').DataTable().ajax.reload(); // reload datatable
                            Swal.fire({
                                icon: 'success',
                                title: 'Success',
                                text: response.message
                            });
                            $('#createUserForm')[0].reset();
                        }
                    },
                    error: function(xhr) {
                        if (xhr.status === 422) { // validation error
                            let errors = xhr.responseJSON.errors;
                            if (errors.name) {
                                $('#nameError').text(errors.name[0]).show();
                                $('#name').addClass('is-invalid');
                            }
                            if (errors.email) {
                                $('#emailError').text(errors.email[0]).show();
                                $('#email').addClass('is-invalid');
                            }
                            if (errors.password) {
                                $('#passwordError').text(errors.password[0]).show();
                                $('#password').addClass('is-invalid');
                            }
                        } else {
                            $('#createUserModal').modal('hide');
                            Swal.fire({
                                icon: 'error',
                                title: 'Error',
                                text: 'Something went wrong!'
                            });
                        }
                    }
                });
            });
        });
    </script>
@endpush
