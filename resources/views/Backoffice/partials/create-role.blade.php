<div class="modal-header">
    <h5 class="modal-title">Create New Role</h5>
    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
</div>
<div class="modal-body">
    <form id="createRoleForm" action="{{ route('Backoffice.roles.store') }}" method="POST">
        @csrf
        <div class="mb-3">
            <label for="name" class="form-label">Role Name</label>
            <input type="text" class="form-control" id="name" name="name" required>
            <div id="name-error" class="text-danger"></div>
        </div>

        <div class="mb-3">
            <label class="form-label d-block">Permissions</label>
            <div class="row g-3">
                @foreach($permissions->chunk(2) as $chunk)
                    @foreach($chunk as $permission)
                        <div class="col-md-6">
                            <div class="form-check">
                                <input class="form-check-input" type="checkbox" 
                                       name="permissions[]" 
                                       value="{{ $permission->id }}" 
                                       id="permission_{{ $permission->id }}">
                                <label class="form-check-label" for="permission_{{ $permission->id }}">
                                    {{ $permission->name }}
                                </label>
                            </div>
                        </div>
                    @endforeach
                @endforeach
            </div>
            <div id="permissions-error" class="text-danger mt-2"></div>
        </div>

        <div class="modal-footer px-0 pb-0">
            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
            <button type="submit" class="btn btn-primary">Create Role</button>
        </div>
    </form>
</div>
