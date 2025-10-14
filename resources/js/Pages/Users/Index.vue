<script setup lang="ts">
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import { Head, Link, router } from '@inertiajs/vue3';

interface User {
    id: number;
    name: string;
    email: string;
    position: string;
    user_type: string;
    is_active: boolean;
    facility?: { name: string };
    municipality?: { name: string };
    roles: Array<{ name: string }>;
    created_at: string;
}

interface Props {
    users: {
        data: User[];
        links: any[];
        current_page: number;
        last_page: number;
    };
}

defineProps<Props>();

const getStatusColor = (isActive: boolean) => {
    return isActive
        ? 'bg-green-100 text-green-800'
        : 'bg-red-100 text-red-800';
};

const getUserTypeColor = (userType: string) => {
    const colors: Record<string, string> = {
        'encoder': 'bg-blue-100 text-blue-800',
        'validator': 'bg-green-100 text-green-800',
        'pesu_admin': 'bg-purple-100 text-purple-800',
    };
    return colors[userType] || 'bg-gray-100 text-gray-800';
};

const toggleStatus = (userId: number) => {
    if (confirm('Are you sure you want to toggle this user\'s status?')) {
        router.post(route('users.toggle-status', userId), {}, {
            preserveScroll: true,
        });
    }
};
</script><template>
    <Head title="User Management" />

    <AuthenticatedLayout>
        <template #header>
            <div class="flex justify-between items-center">
                <div>
                    <h2 class="text-xl font-semibold text-gray-900">
                        User Management
                    </h2>
                    <p class="text-sm text-gray-600 mt-1">Manage system users and their permissions</p>
                </div>
                <Link
                    :href="route('users.create')"
                    class="inline-flex items-center px-4 py-2 bg-blue-600 border border-transparent rounded-lg text-sm font-medium text-white hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:ring-offset-2 transition-colors duration-200"
                >
                    <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" />
                    </svg>
                    Add User
                </Link>
            </div>
        </template>

        <div class="py-6">
            <div class="mx-auto max-w-7xl px-4 sm:px-6 lg:px-8">
                <div class="bg-white rounded-lg shadow-sm border border-gray-200">
                    <div class="p-6">
                        <!-- Users Table -->
                        <div class="overflow-x-auto">
                            <table class="min-w-full divide-y divide-gray-200">
                                <thead>
                                    <tr class="bg-gray-50">
                                        <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">User</th>
                                        <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Role & Type</th>
                                        <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Location</th>
                                        <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Status</th>
                                        <th class="px-4 py-3 text-center text-xs font-medium text-gray-500 uppercase tracking-wider">Actions</th>
                                    </tr>
                                </thead>
                                <tbody class="bg-white divide-y divide-gray-100">
                                    <tr v-for="user in users.data" :key="user.id" class="hover:bg-gray-50 transition-colors duration-150">
                                        <td class="px-4 py-4">
                                            <div class="flex items-center">
                                                <div class="flex-shrink-0 h-10 w-10">
                                                    <div class="h-10 w-10 rounded-full bg-blue-100 flex items-center justify-center">
                                                        <span class="text-sm font-medium text-blue-600">
                                                            {{ user.name.charAt(0).toUpperCase() }}
                                                        </span>
                                                    </div>
                                                </div>
                                                <div class="ml-3">
                                                    <div class="text-sm font-medium text-gray-900">{{ user.name }}</div>
                                                    <div class="text-sm text-gray-500">{{ user.email }}</div>
                                                    <div v-if="user.position" class="text-xs text-gray-400">{{ user.position }}</div>
                                                </div>
                                            </div>
                                        </td>
                                        <td class="px-4 py-4">
                                            <div class="space-y-1">
                                                <span v-for="role in user.roles" :key="role.name"
                                                    class="inline-flex items-center px-2 py-1 text-xs font-medium rounded-md bg-indigo-50 text-indigo-700 mr-1">
                                                    {{ role.name }}
                                                </span>
                                                <div>
                                                    <span :class="getUserTypeColor(user.user_type)"
                                                        class="inline-flex items-center px-2 py-1 text-xs font-medium rounded-md">
                                                        {{ user.user_type }}
                                                    </span>
                                                </div>
                                            </div>
                                        </td>
                                        <td class="px-4 py-4">
                                            <div class="text-sm text-gray-900">
                                                {{ user.facility?.name || user.municipality?.name || 'Not assigned' }}
                                            </div>
                                        </td>
                                        <td class="px-4 py-4">
                                            <span :class="getStatusColor(user.is_active)"
                                                class="inline-flex items-center px-2 py-1 text-xs font-medium rounded-full">
                                                <div class="w-2 h-2 rounded-full mr-1.5"
                                                     :class="user.is_active ? 'bg-green-400' : 'bg-red-400'"></div>
                                                {{ user.is_active ? 'Active' : 'Inactive' }}
                                            </span>
                                        </td>
                                        <td class="px-4 py-4 text-center">
                                            <div class="flex items-center justify-center space-x-2">
                                                <Link :href="route('users.edit', user.id)"
                                                    class="inline-flex items-center px-3 py-1.5 text-xs font-medium text-blue-600 bg-blue-50 rounded-md hover:bg-blue-100 transition-colors duration-150">
                                                    <svg class="w-3 h-3 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z" />
                                                    </svg>
                                                    Edit
                                                </Link>
                                                <button
                                                    class="inline-flex items-center px-3 py-1.5 text-xs font-medium rounded-md transition-colors duration-150"
                                                    :class="user.is_active
                                                        ? 'text-orange-600 bg-orange-50 hover:bg-orange-100'
                                                        : 'text-green-600 bg-green-50 hover:bg-green-100'"
                                                    @click="toggleStatus(user.id)">
                                                    {{ user.is_active ? 'Deactivate' : 'Activate' }}
                                                </button>
                                            </div>
                                        </td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>

                        <!-- Pagination -->
                        <div v-if="users.links.length > 3" class="mt-6 flex justify-center border-t border-gray-200 pt-4">
                            <nav class="flex space-x-1">
                                <Link
                                    v-for="(link, index) in users.links"
                                    :key="index"
                                    :href="link.url || '#'"
                                    :class="[
                                        'px-3 py-2 text-sm font-medium rounded-md transition-colors duration-150',
                                        link.active
                                            ? 'bg-blue-600 text-white'
                                            : 'text-gray-700 bg-white border border-gray-300 hover:bg-gray-50',
                                        !link.url ? 'opacity-50 cursor-not-allowed' : ''
                                    ]"
                                    :disabled="!link.url"
                                    v-html="link.label"
                                />
                            </nav>
                        </div>

                        <!-- Empty State -->
                        <div v-if="users.data.length === 0" class="text-center py-12">
                            <div class="mx-auto h-16 w-16 rounded-full bg-gray-100 flex items-center justify-center mb-4">
                                <svg class="h-8 w-8 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z" />
                                </svg>
                            </div>
                            <h3 class="text-lg font-medium text-gray-900 mb-2">No users found</h3>
                            <p class="text-gray-500 mb-4">Get started by creating your first user account.</p>
                            <Link
                                :href="route('users.create')"
                                class="inline-flex items-center px-4 py-2 bg-blue-600 text-white text-sm font-medium rounded-lg hover:bg-blue-700 transition-colors duration-200"
                            >
                                <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" />
                                </svg>
                                Add User
                            </Link>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </AuthenticatedLayout>
</template>
