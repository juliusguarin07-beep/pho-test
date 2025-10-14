<script setup lang="ts">
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import { Head, Link, useForm } from '@inertiajs/vue3';
import { ref, watch } from 'vue';

interface Role {
    id: number;
    name: string;
}

interface Facility {
    id: number;
    name: string;
}

interface Municipality {
    id: number;
    name: string;
}

interface User {
    id: number;
    name: string;
    email: string;
    position: string;
    contact_number?: string;
    user_type: string;
    facility_id?: number;
    municipality_id?: number;
    is_active: boolean;
    roles: Role[];
}

interface Props {
    user: User;
    roles: Role[];
    facilities: Facility[];
    municipalities: Municipality[];
}

const props = defineProps<Props>();

const form = useForm({
    name: props.user.name,
    email: props.user.email,
    position: props.user.position,
    contact_number: props.user.contact_number || '',
    user_type: props.user.user_type,
    role: props.user.roles?.[0]?.name || '',
    facility_id: props.user.facility_id,
    municipality_id: props.user.municipality_id,
    is_active: props.user.is_active,
});

const userTypes: Array<{value: string, label: string, description: string}> = [
    { value: 'encoder', label: 'Encoder', description: 'Creates and submits case reports' },
    { value: 'validator', label: 'Validator', description: 'Validates submitted case reports' },
    { value: 'pesu_admin', label: 'PESU Administrator', description: 'Approves validated reports and manages system' },
];

// Map user types to their corresponding roles
const userTypeToRole = {
    'encoder': 'encoder',
    'validator': 'validator',
    'pesu_admin': 'pesu_admin'
};

// Watch for user_type changes and automatically set the role
watch(() => form.user_type, (newUserType) => {
    if (newUserType && userTypeToRole[newUserType as keyof typeof userTypeToRole]) {
        form.role = userTypeToRole[newUserType as keyof typeof userTypeToRole];
    } else {
        form.role = '';
    }
});

const submit = () => {
    form.put(route('users.update', props.user.id), {
        preserveScroll: true,
    });
};
</script>

<template>
    <Head title="Edit User" />

    <AuthenticatedLayout>
        <template #header>
            <div class="flex items-center justify-between">
                <div>
                    <h2 class="text-xl font-semibold leading-tight text-gray-800">
                        Edit User
                    </h2>
                    <p class="text-sm text-gray-600 mt-1">Update user account information</p>
                </div>
                <Link
                    :href="route('users.index')"
                    class="inline-flex items-center px-4 py-2 text-sm font-medium text-gray-700 bg-white border border-gray-300 rounded-lg hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500 transition-colors duration-200"
                >
                    <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7" />
                    </svg>
                    Back to Users
                </Link>
            </div>
        </template>

        <div class="py-12">
            <div class="max-w-4xl mx-auto sm:px-6 lg:px-8">
                <div class="bg-white shadow-sm sm:rounded-lg overflow-hidden">
                    <form @submit.prevent="submit" class="p-6 space-y-6">

                        <!-- Personal Information Section -->
                        <div>
                            <h3 class="text-lg font-medium text-gray-900 mb-4">Personal Information</h3>
                            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">

                                <!-- Full Name -->
                                <div>
                                    <label for="name" class="block text-sm font-medium text-gray-700 mb-1">
                                        Full Name <span class="text-red-500">*</span>
                                    </label>
                                    <input
                                        id="name"
                                        v-model="form.name"
                                        type="text"
                                        required
                                        class="block w-full px-3 py-2 border border-gray-300 rounded-lg text-sm focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition-colors duration-200"
                                        placeholder="e.g. Juan Dela Cruz"
                                    />
                                    <div v-if="form.errors.name" class="mt-1 text-sm text-red-600">
                                        {{ form.errors.name }}
                                    </div>
                                </div>

                                <!-- Email Address -->
                                <div>
                                    <label for="email" class="block text-sm font-medium text-gray-700 mb-1">
                                        Email Address <span class="text-red-500">*</span>
                                    </label>
                                    <input
                                        id="email"
                                        v-model="form.email"
                                        type="email"
                                        required
                                        class="block w-full px-3 py-2 border border-gray-300 rounded-lg text-sm focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition-colors duration-200"
                                        placeholder="email@example.com"
                                    />
                                    <div v-if="form.errors.email" class="mt-1 text-sm text-red-600">
                                        {{ form.errors.email }}
                                    </div>
                                </div>

                                <!-- Position/Designation -->
                                <div>
                                    <label for="position" class="block text-sm font-medium text-gray-700 mb-1">
                                        Position/Designation <span class="text-red-500">*</span>
                                    </label>
                                    <input
                                        id="position"
                                        v-model="form.position"
                                        type="text"
                                        required
                                        class="block w-full px-3 py-2 border border-gray-300 rounded-lg text-sm focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition-colors duration-200"
                                        placeholder="e.g. Disease Surveillance Officer"
                                    />
                                    <div v-if="form.errors.position" class="mt-1 text-sm text-red-600">
                                        {{ form.errors.position }}
                                    </div>
                                </div>

                                <!-- Contact Number -->
                                <div>
                                    <label for="contact_number" class="block text-sm font-medium text-gray-700 mb-1">
                                        Contact Number
                                    </label>
                                    <input
                                        id="contact_number"
                                        v-model="form.contact_number"
                                        type="tel"
                                        class="block w-full px-3 py-2 border border-gray-300 rounded-lg text-sm focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition-colors duration-200"
                                        placeholder="e.g. 09171234567"
                                    />
                                    <div v-if="form.errors.contact_number" class="mt-1 text-sm text-red-600">
                                        {{ form.errors.contact_number }}
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- System Access Section -->
                        <div class="border-t border-gray-200 pt-6">
                            <h3 class="text-lg font-medium text-gray-900 mb-4">System Access</h3>
                            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">

                                <!-- User Type -->
                                <div>
                                    <label for="user_type" class="block text-sm font-medium text-gray-700 mb-1">
                                        User Type <span class="text-red-500">*</span>
                                    </label>
                                    <select
                                        id="user_type"
                                        v-model="form.user_type"
                                        required
                                        class="block w-full px-3 py-2 border border-gray-300 rounded-lg text-sm focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition-colors duration-200"
                                    >
                                        <option value="">Select User Type</option>
                                        <option v-for="type in userTypes" :key="type.value" :value="type.value">
                                            {{ type.label }} - {{ type.description }}
                                        </option>
                                    </select>
                                    <div v-if="form.errors.user_type" class="mt-1 text-sm text-red-600">
                                        {{ form.errors.user_type }}
                                    </div>
                                </div>

                                <!-- Role -->
                                <div>
                                    <label for="role" class="block text-sm font-medium text-gray-700 mb-1">
                                        Role <span class="text-red-500">*</span>
                                        <span class="text-xs text-gray-500">(Auto-filled based on User Type)</span>
                                    </label>
                                    <select
                                        id="role"
                                        v-model="form.role"
                                        required
                                        disabled
                                        class="block w-full px-3 py-2 border border-gray-300 rounded-lg text-sm bg-gray-50 text-gray-600 cursor-not-allowed transition-colors duration-200"
                                    >
                                        <option value="">Select User Type first</option>
                                        <option v-for="role in roles" :key="role.id" :value="role.name">
                                            {{ role.name }}
                                        </option>
                                    </select>
                                    <div v-if="form.errors.role" class="mt-1 text-sm text-red-600">
                                        {{ form.errors.role }}
                                    </div>
                                    <div v-if="form.user_type" class="mt-1 text-xs text-green-600 flex items-center">
                                        <svg class="w-3 h-3 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
                                        </svg>
                                        Role automatically set based on selected user type
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Location Assignment Section -->
                        <div class="border-t border-gray-200 pt-6">
                            <h3 class="text-lg font-medium text-gray-900 mb-4">Location Assignment</h3>
                            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">

                                <!-- Facility -->
                                <div>
                                    <label for="facility_id" class="block text-sm font-medium text-gray-700 mb-1">
                                        Assigned Facility
                                    </label>
                                    <select
                                        id="facility_id"
                                        v-model="form.facility_id"
                                        class="block w-full px-3 py-2 border border-gray-300 rounded-lg text-sm focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition-colors duration-200"
                                    >
                                        <option :value="null">No Facility Assignment</option>
                                        <option v-for="facility in facilities" :key="facility.id" :value="facility.id">
                                            {{ facility.name }}
                                        </option>
                                    </select>
                                    <div v-if="form.errors.facility_id" class="mt-1 text-sm text-red-600">
                                        {{ form.errors.facility_id }}
                                    </div>
                                </div>

                                <!-- Municipality -->
                                <div>
                                    <label for="municipality_id" class="block text-sm font-medium text-gray-700 mb-1">
                                        Assigned Municipality
                                    </label>
                                    <select
                                        id="municipality_id"
                                        v-model="form.municipality_id"
                                        class="block w-full px-3 py-2 border border-gray-300 rounded-lg text-sm focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition-colors duration-200"
                                    >
                                        <option :value="null">No Municipality Assignment</option>
                                        <option v-for="municipality in municipalities" :key="municipality.id" :value="municipality.id">
                                            {{ municipality.name }}
                                        </option>
                                    </select>
                                    <div v-if="form.errors.municipality_id" class="mt-1 text-sm text-red-600">
                                        {{ form.errors.municipality_id }}
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Account Status Section -->
                        <div class="border-t border-gray-200 pt-6">
                            <h3 class="text-lg font-medium text-gray-900 mb-4">Account Status</h3>
                            <div class="flex items-center">
                                <input
                                    id="is_active"
                                    v-model="form.is_active"
                                    type="checkbox"
                                    class="h-4 w-4 text-blue-600 focus:ring-blue-500 border-gray-300 rounded"
                                />
                                <label for="is_active" class="ml-2 text-sm font-medium text-gray-700">
                                    Active Account
                                </label>
                            </div>
                            <p class="mt-1 text-xs text-gray-500">
                                Inactive accounts cannot log in to the system
                            </p>
                        </div>

                        <!-- Form Actions -->
                        <div class="border-t border-gray-200 pt-6">
                            <div class="flex items-center justify-end space-x-3">
                                <Link
                                    :href="route('users.index')"
                                    class="inline-flex items-center px-4 py-2 text-sm font-medium text-gray-700 bg-white border border-gray-300 rounded-lg hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500 transition-colors duration-200"
                                >
                                    Cancel
                                </Link>
                                <button
                                    type="submit"
                                    :disabled="form.processing"
                                    class="inline-flex items-center px-4 py-2 text-sm font-medium text-white bg-blue-600 border border-transparent rounded-lg hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500 disabled:opacity-50 disabled:cursor-not-allowed transition-colors duration-200"
                                >
                                    <svg v-if="form.processing" class="w-4 h-4 mr-2 animate-spin" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15" />
                                    </svg>
                                    {{ form.processing ? 'Updating...' : 'Update User' }}
                                </button>
                            </div>
                        </div>

                    </form>
                </div>
            </div>
        </div>
    </AuthenticatedLayout>
</template>
