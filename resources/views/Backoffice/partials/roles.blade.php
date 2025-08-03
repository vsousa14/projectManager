<div class="d-flex justify-content-between align-items-center mb-4">
    <div>
        <h2 class="display-6 fw-bold text-gray-800 mb-2">Role Management</h2>
        <p class="lead text-gray-600">Manage all your roles and permissions here.</p>
    </div>
    @can('create', \Spatie\Permission\Models\Role::class)
    <button type="button" 
            class="btn btn-primary create-role-btn"
            data-create-url="{{ route('Backoffice.roles.create') }}">
        <i class="fas fa-plus me-2"></i>Create New Role
    </button>
    @endcan
</div>

<div class="table-responsive mt-4">
    <table class="table table-hover table-bordered rounded-lg overflow-hidden">
        <thead class="bg-indigo-100">
            <tr>
                <th scope="col" class="py-3 px-4 text-left text-xs font-semibold text-gray-700 uppercase tracking-wider">ID</th>
                <th scope="col" class="py-3 px-4 text-left text-xs font-semibold text-gray-700 uppercase tracking-wider">Name</th>
                <th scope="col" class="py-3 px-4 text-left text-xs font-semibold text-gray-700 uppercase tracking-wider">Permissions</th>
                <th scope="col" class="py-3 px-4 text-left text-xs font-semibold text-gray-700 uppercase tracking-wider">Actions</th>
            </tr>
        </thead>
        <tbody class="bg-white divide-y divide-gray-200">
            @forelse($roles as $role)
            <tr data-role-id="{{ $role->id }}">
                <td class="py-3 px-4 whitespace-nowrap">{{ $role->id }}</td>
                <td class="py-3 px-4 whitespace-nowrap">{{ $role->name }}</td>
                <td class="py-3 px-4">
                    <div class="flex flex-wrap gap-1">
                        @foreach($role->permissions as $permission)
                            <span class="inline-flex items-center px-2 py-1 bg-indigo-100 text-xs font-medium text-indigo-700 rounded">
                                {{ $permission->name }}
                            </span>
                        @endforeach
                    </div>
                </td>
                <td class="py-3 px-4 whitespace-nowrap">
                    @can('update', $role)
                        <button type="button" 
                                class="btn btn-sm btn-info text-white rounded-md edit-role-btn"
                                data-edit-url="{{ route('Backoffice.roles.edit', $role) }}"
                                data-role-id="{{ $role->id }}">
                            Edit
                        </button>
                        @can('delete', $role)
                        <button type="button" 
                                class="btn btn-sm btn-danger text-white rounded-md delete-role-btn"
                                data-delete-url="{{ route('Backoffice.roles.destroy', $role) }}"
                                data-role-id="{{ $role->id }}">
                            Delete
                        </button>
                    @endcan
                    @endcan
                </td>
            </tr>
            @empty
            <tr>
                <td colspan="5" class="py-3 px-4 text-center text-gray-500">No roles to show.</td>
            </tr>
            @endforelse
        </tbody>
    </table>
</div>

@if(isset($roles) && method_exists($roles, 'links'))
<div class="mt-4">
    {{ $roles->links() }}
</div>
@endif

