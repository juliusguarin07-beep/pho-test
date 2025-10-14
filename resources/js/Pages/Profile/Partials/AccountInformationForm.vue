<script setup lang="ts">
interface UserDetails {
    name: string;
    email: string;
    position: string;
    contact_number?: string;
    user_type: string;
    roles: string[];
    facility?: string;
    municipality?: string;
    is_active: boolean;
    created_at: string;
}

defineProps<{
    userDetails: UserDetails;
}>();

const formatUserType = (userType: string) => {
    const types: Record<string, string> = {
        'encoder': 'Encoder',
        'validator': 'Validator',
        'pesu_admin': 'PESU Administrator'
    };
    return types[userType] || userType;
};

const formatDate = (dateString: string) => {
    return new Date(dateString).toLocaleDateString('en-US', {
        year: 'numeric',
        month: 'long',
        day: 'numeric'
    });
};
</script>

<template>
    <section>
        <header class="mb-6">
            <h2 class="text-lg font-medium text-gray-900">
                Account Information
            </h2>
            <p class="mt-1 text-sm text-gray-600">
                Your account details as configured by the system administrator. Contact PESU if you need changes.
            </p>
        </header>

        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
            <!-- Personal Information -->
            <div class="space-y-4">
                <h3 class="text-md font-medium text-gray-800 border-b border-gray-200 pb-2">Personal Information</h3>

                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Full Name</label>
                    <div class="px-3 py-2 bg-gray-50 border border-gray-200 rounded-lg text-sm text-gray-900">
                        {{ userDetails.name }}
                    </div>
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Email Address</label>
                    <div class="px-3 py-2 bg-gray-50 border border-gray-200 rounded-lg text-sm text-gray-900">
                        {{ userDetails.email }}
                    </div>
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Position/Designation</label>
                    <div class="px-3 py-2 bg-gray-50 border border-gray-200 rounded-lg text-sm text-gray-900">
                        {{ userDetails.position }}
                    </div>
                </div>

                <div v-if="userDetails.contact_number">
                    <label class="block text-sm font-medium text-gray-700 mb-1">Contact Number</label>
                    <div class="px-3 py-2 bg-gray-50 border border-gray-200 rounded-lg text-sm text-gray-900">
                        {{ userDetails.contact_number }}
                    </div>
                </div>
            </div>

            <!-- System Access & Location -->
            <div class="space-y-4">
                <h3 class="text-md font-medium text-gray-800 border-b border-gray-200 pb-2">System Access & Assignment</h3>

                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">User Type</label>
                    <div class="px-3 py-2 bg-gray-50 border border-gray-200 rounded-lg text-sm">
                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium"
                              :class="{
                                  'bg-blue-100 text-blue-800': userDetails.user_type === 'encoder',
                                  'bg-green-100 text-green-800': userDetails.user_type === 'validator',
                                  'bg-purple-100 text-purple-800': userDetails.user_type === 'pesu_admin'
                              }">
                            {{ formatUserType(userDetails.user_type) }}
                        </span>
                    </div>
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">System Role</label>
                    <div class="px-3 py-2 bg-gray-50 border border-gray-200 rounded-lg text-sm text-gray-900">
                        {{ userDetails.roles.join(', ') }}
                    </div>
                </div>

                <div v-if="userDetails.facility">
                    <label class="block text-sm font-medium text-gray-700 mb-1">Assigned Facility</label>
                    <div class="px-3 py-2 bg-gray-50 border border-gray-200 rounded-lg text-sm text-gray-900">
                        {{ userDetails.facility }}
                    </div>
                </div>

                <div v-if="userDetails.municipality">
                    <label class="block text-sm font-medium text-gray-700 mb-1">Assigned Municipality</label>
                    <div class="px-3 py-2 bg-gray-50 border border-gray-200 rounded-lg text-sm text-gray-900">
                        {{ userDetails.municipality }}
                    </div>
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Account Status</label>
                    <div class="px-3 py-2 bg-gray-50 border border-gray-200 rounded-lg text-sm">
                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium"
                              :class="userDetails.is_active ? 'bg-green-100 text-green-800' : 'bg-red-100 text-red-800'">
                            <div class="w-1.5 h-1.5 rounded-full mr-1.5"
                                 :class="userDetails.is_active ? 'bg-green-400' : 'bg-red-400'"></div>
                            {{ userDetails.is_active ? 'Active' : 'Inactive' }}
                        </span>
                    </div>
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Account Created</label>
                    <div class="px-3 py-2 bg-gray-50 border border-gray-200 rounded-lg text-sm text-gray-900">
                        {{ formatDate(userDetails.created_at) }}
                    </div>
                </div>
            </div>
        </div>

        <!-- Information Notice -->
        <div class="mt-6 p-4 bg-blue-50 border-l-4 border-blue-400 rounded-md">
            <div class="flex">
                <div class="flex-shrink-0">
                    <svg class="h-5 w-5 text-blue-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                    </svg>
                </div>
                <div class="ml-3">
                    <h4 class="text-sm font-medium text-blue-800">
                        Need to update your information?
                    </h4>
                    <div class="mt-1 text-sm text-blue-700">
                        <p>
                            Your account details can only be modified by the PESU Administrator.
                            Please contact your system administrator if you need any changes to your profile information,
                            role assignments, or facility assignments.
                        </p>
                    </div>
                </div>
            </div>
        </div>
    </section>
</template>
