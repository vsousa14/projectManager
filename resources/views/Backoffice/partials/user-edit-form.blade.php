<div class="modal-header bg-indigo-600">
    <h5 class="modal-title" id="editUserModalLabel">Edit User: {{ $user->name }}</h5>
    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
</div>
<div class="modal-body">
    <form id="editUserForm" action="{{ route('Backoffice.users.update', $user) }}" method="POST">
        @csrf
        @method('PUT')

        <div class="mb-3">
            <label for="name" class="form-label">Name</label>
            <input type="text" class="form-control" id="name" name="name" value="{{ old('name', $user->name) }}" required>
            <div class="text-danger mt-1" id="name-error"></div>
        </div>

        <div class="mb-3">
            <label for="email" class="form-label">Email address</label>
            <input type="email" class="form-control" id="email" name="email" value="{{ old('email', $user->email) }}" required>
            <div class="text-danger mt-1" id="email-error"></div>
        </div>

        <div class="mb-3">
            <label for="password" class="form-label">New Password</label>
            <input type="password" class="form-control" id="password" name="password">
            <small class="form-text text-muted">Leave blank if you don't want to change the password.</small>
            <div class="text-danger mt-1" id="password-error"></div>
        </div>

        <div class="mb-3">
            <label for="password_confirmation" class="form-label">Confirm New Password</label>
            <input type="password" class="form-control" id="password_confirmation" name="password_confirmation">
        </div>

        <div class="mb-3">
            <label for="roles" class="form-label">Roles</label>
            @foreach($roles as $role)
                <div class="form-check">
                    <input class="form-check-input" type="checkbox" name="roles[]" value="{{ $role->name }}" id="role-{{ $role->id }}"
                        {{ $user->hasRole($role->name) ? 'checked' : '' }}>
                    <label class="form-check-label" for="role-{{ $role->id }}">
                        {{ $role->name }}
                    </label>
                </div>
            @endforeach
            <div class="text-danger mt-1" id="roles-error"></div>
        </div>

        <div class="modal-footer">
            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
            <button type="submit" class="btn btn-primary">Save changes</button>
        </div>
    </form>
</div>

<script>
    document.getElementById('editUserForm').addEventListener('submit', async function(e) {
        e.preventDefault();

        const form = e.target;
        const formData = new FormData(form);
        const actionUrl = form.action;

        document.querySelectorAll('.text-danger').forEach(el => el.textContent = '');

        try {
            const response = await fetch(actionUrl, {
                method: 'POST', 
                headers: {
                    'X-Requested-With': 'XMLHttpRequest',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                },
                body: formData
            });

            if (response.ok) {
                const result = await response.json();
                alert(result.message);
                const editUserModal = bootstrap.Modal.getInstance(document.getElementById('editUserModal'));
                if (editUserModal) {
                    editUserModal.hide(); 
                }
                const usersNavLink = document.querySelector('.nav-link[data-section="users"]');
                if (usersNavLink) {
                    usersNavLink.click();
                }
            } else {
                const errorData = await response.json();
                if (response.status === 422 && errorData.errors) {
                    for (const field in errorData.errors) {
                        const errorDiv = document.getElementById(field + '-error');
                        if (errorDiv) {
                            errorDiv.textContent = errorData.errors[field][0];
                        }
                    }
                } else {
                    alert('Error: ' + (errorData.message || 'Something went wrong.'));
                }
            }
        } catch (error) {
            console.error('Network or other error:', error);
            alert('An unexpected error occurred. Please try again.');
        }
    });
</script>