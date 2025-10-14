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

interface Props {
    roles: Role[];
    facilities: Facility[];
    municipalities: Municipality[];
}

const props = defineProps<Props>();

const form = useForm({
    name: '',
    email: '',
    password: '',
    password_confirmation: '',
    position: '',
    contact_number: '',
    user_type: '',
    role: '',
    facility_id: null as number | null,
    municipality_id: null as number | null,
    is_active: true,
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

const showPassword = ref(false);
const showPasswordConfirmation = ref(false);

const submit = () => {
    form.post(route('users.store'), {
        preserveScroll: true,
        onSuccess: () => {
            form.reset();
        },
    });
};
</script>

<template>
    <Head title="Add New User" />

    <AuthenticatedLayout>
        <template #header>
            <div class="flex items-center justify-between">
                <div>
                    <h2 class="text-xl font-semibold text-gray-900">
                        Add New User
                    </h2>
                    <p class="mt-1 text-sm text-gray-600">Create a new user account for the system</p>
                </div>
                <Link
                    :href="route('users.index')"
                    class="inline-flex items-center px-4 py-2 text-sm font-medium text-gray-700 transition-colors duration-200 bg-gray-100 border border-gray-300 rounded-lg hover:bg-gray-200 focus:outline-none focus:ring-2 focus:ring-gray-500 focus:ring-offset-2"
                >
                    <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18" />
                    </svg>
                    Back to Users
                </Link>
            </div>
        </template>

        <div class="py-6">
            <div class="max-w-4xl px-4 mx-auto sm:px-6 lg:px-8">
                <div class="bg-white border border-gray-200 rounded-lg shadow-sm">
                    <form @submit.prevent="submit" class="p-6">
                        <div class="space-y-6">

                            <!-- Personal Information Section -->
                            <div>
                                <h3 class="mb-4 text-lg font-medium text-gray-900">Personal Information</h3>
                                <div class="grid grid-cols-1 gap-4 md:grid-cols-2">

                                    <!-- Full Name -->
                                    <div>
                                        <label for="name" class="block mb-1 text-sm font-medium text-gray-700">
                                            Full Name <span class="text-red-500">*</span>
                                        </label>
                                        <input
                                            id="name"
                                            v-model="form.name"
                                            type="text"
                                            required
                                            class="block w-full px-3 py-2 text-sm transition-colors duration-200 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500"
                                            placeholder="e.g., Juan Dela Cruz"
                                        />
                                        <div v-if="form.errors.name" class="mt-1 text-sm text-red-600">
                                            {{ form.errors.name }}
                                        </div>
                                    </div>

                                    <!-- Email -->
                                    <div>
                                        <label for="email" class="block mb-1 text-sm font-medium text-gray-700">
                                            Email Address <span class="text-red-500">*</span>
                                        </label>
                                        <input
                                            id="email"
                                            v-model="form.email"
                                            type="email"
                                            required
                                            class="block w-full px-3 py-2 text-sm transition-colors duration-200 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500"
                                            placeholder="email@example.com"
                                        />
                                        <div v-if="form.errors.email" class="mt-1 text-sm text-red-600">
                                            {{ form.errors.email }}
                                        </div>
                                    </div>

                                    <!-- Position -->
                                    <div>
                                        <label for="position" class="block mb-1 text-sm font-medium text-gray-700">
                                            Position/Designation <span class="text-red-500">*</span>
                                        </label>
                                        <input
                                            id="position"
                                            v-model="form.position"
                                            type="text"
                                            required
                                            class="block w-full px-3 py-2 text-sm transition-colors duration-200 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500"
                                            placeholder="e.g., Disease Surveillance Officer"
                                        />
                                        <div v-if="form.errors.position" class="mt-1 text-sm text-red-600">
                                            {{ form.errors.position }}
                                        </div>
                                    </div>

                                    <!-- Contact Number -->
                                    <div>
                                        <label for="contact_number" class="block mb-1 text-sm font-medium text-gray-700">
                                            Contact Number
                                        </label>
                                        <input
                                            id="contact_number"
                                            v-model="form.contact_number"
                                            type="text"
                                            class="block w-full px-3 py-2 text-sm transition-colors duration-200 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500"
                                            placeholder="e.g., 09171234567"
                                        />
                                        <div v-if="form.errors.contact_number" class="mt-1 text-sm text-red-600">
                                            {{ form.errors.contact_number }}
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <!-- System Access Section -->
                            <div class="pt-6 border-t border-gray-200">
                                <h3 class="mb-4 text-lg font-medium text-gray-900">System Access</h3>
                                <div class="grid grid-cols-1 gap-4 md:grid-cols-2">

                                    <!-- User Type -->
                                    <div>
                                        <label for="user_type" class="block mb-1 text-sm font-medium text-gray-700">
                                            User Type <span class="text-red-500">*</span>
                                        </label>
                                        <select
                                            id="user_type"
                                            v-model="form.user_type"
                                            required
                                            class="block w-full px-3 py-2 text-sm transition-colors duration-200 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500"
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
                                        <label for="role" class="block mb-1 text-sm font-medium text-gray-700">
                                            Role <span class="text-red-500">*</span>
                                            <span class="text-xs text-gray-500">(Auto-filled based on User Type)</span>
                                        </label>
                                        <select
                                            id="role"
                                            v-model="form.role"
                                            required
                                            disabled
                                            class="block w-full px-3 py-2 text-sm text-gray-600 transition-colors duration-200 border border-gray-300 rounded-lg cursor-not-allowed bg-gray-50"
                                        >
                                            <option value="">Select User Type first</option>
                                            <option v-for="role in roles" :key="role.id" :value="role.name">
                                                {{ role.name }}
                                            </option>
                                        </select>
                                        <div v-if="form.errors.role" class="mt-1 text-sm text-red-600">
                                            {{ form.errors.role }}
                                        </div>
                                        <div v-if="form.user_type" class="flex items-center mt-1 text-xs text-green-600">
                                            <svg class="w-3 h-3 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
                                            </svg>
                                            Role automatically set based on selected user type
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <!-- Location Assignment Section -->
                            <div class="pt-6 border-t border-gray-200">
                                <h3 class="mb-4 text-lg font-medium text-gray-900">Location Assignment</h3>
                                <div class="grid grid-cols-1 gap-4 md:grid-cols-2">

                                    <!-- Facility (for encoders/validators) -->
                                    <div v-if="form.user_type === 'encoder' || form.user_type === 'validator'">
                                        <label for="facility_id" class="block mb-1 text-sm font-medium text-gray-700">
                                            Facility <span class="text-red-500">*</span>
                                        </label>
                                        <select
                                            id="facility_id"
                                            v-model="form.facility_id"
                                            class="block w-full px-3 py-2 text-sm transition-colors duration-200 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500"
                                        >
                                            <option :value="null">Select Facility</option>
                                            <option v-for="facility in facilities" :key="facility.id" :value="facility.id">
                                                {{ facility.name }}
                                            </option>
                                        </select>
                                        <div v-if="form.errors.facility_id" class="mt-1 text-sm text-red-600">
                                            {{ form.errors.facility_id }}
                                        </div>
                                    </div>

                                    <!-- Municipality (for encoders/validators/PESU admins) -->
                                    <div v-if="form.user_type === 'encoder' || form.user_type === 'validator' || form.user_type === 'pesu_admin'">
                                        <label for="municipality_id" class="block mb-1 text-sm font-medium text-gray-700">
                                            Municipality
                                            <span v-if="form.user_type === 'encoder' || form.user_type === 'validator'" class="text-red-500">*</span>
                                        </label>
                                        <select
                                            id="municipality_id"
                                            v-model="form.municipality_id"
                                            class="block w-full px-3 py-2 text-sm transition-colors duration-200 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500"
                                        >
                                            <option :value="null">Select Municipality</option>
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

                            <!-- Security Section -->
                            <div class="pt-6 border-t border-gray-200">
                                <h3 class="mb-4 text-lg font-medium text-gray-900">Security</h3>
                                <div class="grid grid-cols-1 gap-4 md:grid-cols-2">

                                    <!-- Password -->
                                    <div>
                                        <label for="password" class="block mb-1 text-sm font-medium text-gray-700">
                                            Password <span class="text-red-500">*</span>
                                        </label>
                                        <div class="relative">
                                            <input
                                                id="password"
                                                v-model="form.password"
                                                :type="showPassword ? 'text' : 'password'"
                                                required
                                                class="block w-full px-3 py-2 pr-10 text-sm transition-colors duration-200 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500"
                                                placeholder="Enter password"
                                            />
                                            <button
                                                type="button"
                                                @click="showPassword = !showPassword"
                                                class="absolute inset-y-0 right-0 flex items-center pr-3 text-gray-400 hover:text-gray-600"
                                            >
                                                <svg v-if="!showPassword" class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" />
                                                </svg>
                                                <svg v-else class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13.875 18.825A10.05 10.05 0 0112 19c-4.478 0-8.268-2.943-9.543-7a9.97 9.97 0 011.563-3.029m5.858.908a3 3 0 114.243 4.243M9.878 9.878l4.242 4.242M9.88 9.88l-3.29-3.29m7.532 7.532l3.29 3.29M3 3l3.59 3.59m0 0A9.953 9.953 0 0112 5c4.478 0 8.268 2.943 9.543 7a10.025 10.025 0 01-4.132 5.411m0 0L21 21" />
                                                </svg>
                                            </button>
                                        </div>
                                        <div v-if="form.errors.password" class="mt-1 text-sm text-red-600">
                                            {{ form.errors.password }}
                                        </div>
                                    </div>

                                    <!-- Confirm Password -->
                                    <div>
                                        <label for="password_confirmation" class="block mb-1 text-sm font-medium text-gray-700">
                                            Confirm Password <span class="text-red-500">*</span>
                                        </label>
                                        <div class="relative">
                                            <input
                                                id="password_confirmation"
                                                v-model="form.password_confirmation"
                                                :type="showPasswordConfirmation ? 'text' : 'password'"
                                                required
                                                class="block w-full px-3 py-2 pr-10 text-sm transition-colors duration-200 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500"
                                                placeholder="Confirm password"
                                            />
                                            <button
                                                type="button"
                                                @click="showPasswordConfirmation = !showPasswordConfirmation"
                                                class="absolute inset-y-0 right-0 flex items-center pr-3 text-gray-400 hover:text-gray-600"
                                            >
                                                <svg v-if="!showPasswordConfirmation" class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" />
                                                </svg>
                                                <svg v-else class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13.875 18.825A10.05 10.05 0 0112 19c-4.478 0-8.268-2.943-9.543-7a9.97 9.97 0 011.563-3.029m5.858.908a3 3 0 114.243 4.243M9.878 9.878l4.242 4.242M9.88 9.88l-3.29-3.29m7.532 7.532l3.29 3.29M3 3l3.59 3.59m0 0A9.953 9.953 0 0112 5c4.478 0 8.268 2.943 9.543 7a10.025 10.025 0 01-4.132 5.411m0 0L21 21" />
                                                </svg>
                                            </button>
                                        </div>
                                    </div>
                                </div>

                                <!-- Active Status -->
                                <div class="mt-4">
                                    <div class="flex items-center">
                                        <input
                                            v-model="form.is_active"
                                            id="is_active"
                                            type="checkbox"
                                            class="w-4 h-4 text-blue-600 border-gray-300 rounded focus:ring-blue-500"
                                        />
                                        <label for="is_active" class="ml-2 text-sm font-medium text-gray-700">
                                            Account is active
                                        </label>
                                    </div>
                                    <p class="mt-1 text-xs text-gray-500">User will be able to log in and access the system</p>
                                </div>
                            </div>
                        </div>

                        <!-- Form Actions -->
                        <div class="flex items-center justify-end pt-6 mt-8 space-x-3 border-t border-gray-200">
                            <Link
                                :href="route('users.index')"
                                class="inline-flex items-center px-4 py-2 text-sm font-medium text-gray-700 transition-colors duration-200 bg-white border border-gray-300 rounded-lg hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-gray-500 focus:ring-offset-2"
                            >
                                Cancel
                            </Link>
                            <button
                                type="submit"
                                :disabled="form.processing"
                                class="inline-flex items-center px-4 py-2 text-sm font-medium text-white transition-colors duration-200 bg-blue-600 border border-transparent rounded-lg hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:ring-offset-2 disabled:opacity-50"
                            >
                                <svg v-if="form.processing" class="w-4 h-4 mr-2 -ml-1 text-white animate-spin" fill="none" viewBox="0 0 24 24">
                                    <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                                    <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                                </svg>
                                {{ form.processing ? 'Creating...' : 'Create User' }}
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </AuthenticatedLayout>
</template>

