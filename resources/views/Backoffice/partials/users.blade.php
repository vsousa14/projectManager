<h2 class="display-6 fw-bold text-gray-800 mb-4">User Management</h2>
<p class="lead text-gray-600">Manage all your users here.</p>

<div class="table-responsive mt-4">
    <table class="table table-hover table-bordered rounded-lg overflow-hidden">
        <thead class="bg-indigo-100">
            <tr>
                <th scope="col" class="py-3 px-4 text-left text-xs font-semibold text-gray-700 uppercase tracking-wider">ID</th>
                <th scope="col" class="py-3 px-4 text-left text-xs font-semibold text-gray-700 uppercase tracking-wider">Name</th>
                <th scope="col" class="py-3 px-4 text-left text-xs font-semibold text-gray-700 uppercase tracking-wider">Email</th>
                <th scope="col" class="py-3 px-4 text-left text-xs font-semibold text-gray-700 uppercase tracking-wider">Role</th>
                <th scope="col" class="py-3 px-4 text-left text-xs font-semibold text-gray-700 uppercase tracking-wider">Actions</th>
            </tr>
        </thead>
        <tbody class="bg-white divide-y divide-gray-200">
            @forelse($users as $user)
            <tr>
                <td class="py-3 px-4 whitespace-nowrap">{{ $user->id }}</td>
                <td class="py-3 px-4 whitespace-nowrap">{{ $user->name }}</td>
                <td class="py-3 px-4 whitespace-nowrap">{{ $user->email }}</td>
                <td class="py-3 px-4 whitespace-nowrap">
                    {{ $user->getRoleNames()->isNotEmpty() ? $user->getRoleNames()->join(', ') : 'N/A' }}
                </td>
                <td class="py-3 px-4 whitespace-nowrap">
                    @can('update', $user)
                        <button type="button" 
                                class="btn btn-sm btn-info text-white rounded-md edit-user-btn"
                                data-edit-url="{{ route('Backoffice.users.edit', $user) }}"
                                data-user-id="{{ $user->id }}">
                            Edit
                        </button>
                    @endcan
                </td>
            </tr>
            @empty
            <tr>
                <td colspan="5" class="py-3 px-4 text-center text-gray-500">No users to show.</td>
            </tr>
            @endforelse
        </tbody>
    </table>
</div>

<div class="mt-4">
    {{ $users->links() }}
</div>