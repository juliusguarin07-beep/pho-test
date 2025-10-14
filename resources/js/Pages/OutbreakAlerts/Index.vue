<script setup lang="ts">
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import { Head, Link, router } from '@inertiajs/vue3';

interface Disease {
    name: string;
}

interface Municipality {
    name: string;
}

interface OutbreakAlert {
    id: number;
    alert_title: string;
    alert_details: string;
    alert_level: string;
    disease: Disease;
    municipality: Municipality;
    affected_barangays: string[];
    alert_start_date: string;
    alert_end_date: string | null;
    status: string;
    created_at: string;
}

interface Props {
    alerts: {
        data: OutbreakAlert[];
        links: any[];
        current_page: number;
        last_page: number;
    };
}

defineProps<Props>();

const getAlertLevelColor = (level: string) => {
    const colors: Record<string, string> = {
        'low': 'bg-yellow-100 text-yellow-800 border-yellow-300',
        'medium': 'bg-orange-100 text-orange-800 border-orange-300',
        'high': 'bg-red-100 text-red-800 border-red-300',
        'critical': 'bg-red-600 text-white border-red-800',
    };
    return colors[level.toLowerCase()] || 'bg-gray-100 text-gray-800 border-gray-300';
};

const getStatusColor = (status: string) => {
    const colors: Record<string, string> = {
        'active': 'bg-green-100 text-green-800',
        'published': 'bg-green-100 text-green-800',
        'resolved': 'bg-blue-100 text-blue-800',
        'archived': 'bg-blue-100 text-blue-800',
        'monitoring': 'bg-yellow-100 text-yellow-800',
        'draft': 'bg-gray-100 text-gray-800',
    };
    return colors[status.toLowerCase()] || 'bg-gray-100 text-gray-800';
};

const formatDate = (date: string | null) => {
    if (!date) return 'N/A';
    return new Date(date).toLocaleDateString('en-US', {
        year: 'numeric',
        month: 'short',
        day: 'numeric'
    });
};

const resolveAlert = (alertId: number) => {
    if (confirm('Are you sure you want to resolve this outbreak alert? This will mark it as archived and finished.')) {
        router.post(route('outbreak-alerts.resolve', alertId), {}, {
            preserveScroll: true,
        });
    }
};

const concludeAlert = (alertId: number) => {
    if (confirm('Are you sure you want to conclude this outbreak alert? This will mark it as completed.')) {
        router.post(route('outbreak-alerts.conclude', alertId), {}, {
            preserveScroll: true,
        });
    }
};

const deleteAlert = (alertId: number) => {
    if (confirm('Are you sure you want to delete this outbreak alert? This action cannot be undone.')) {
        router.delete(route('outbreak-alerts.destroy', alertId), {
            preserveScroll: true,
        });
    }
};
</script>

<template>
    <Head title="Outbreak Alerts" />

    <AuthenticatedLayout>
        <template #header>
            <div class="flex justify-between items-center">
                <div>
                    <h2 class="text-xl font-semibold text-gray-900">
                        Outbreak Alerts
                    </h2>
                    <p class="text-sm text-gray-600 mt-1">Monitor and manage disease outbreak alerts</p>
                </div>
                <Link
                    :href="route('outbreak-alerts.create')"
                    class="inline-flex items-center px-4 py-2 bg-red-600 border border-transparent rounded-lg text-sm font-medium text-white hover:bg-red-700 focus:outline-none focus:ring-2 focus:ring-red-500 focus:ring-offset-2 transition-colors duration-200"
                >
                    <svg class="w-4 h-4 mr-2" fill="currentColor" viewBox="0 0 20 20">
                        <path fill-rule="evenodd" d="M8.257 3.099c.765-1.36 2.722-1.36 3.486 0l5.58 9.92c.75 1.334-.213 2.98-1.742 2.98H4.42c-1.53 0-2.493-1.646-1.743-2.98l5.58-9.92zM11 13a1 1 0 11-2 0 1 1 0 012 0zm-1-8a1 1 0 00-1 1v3a1 1 0 002 0V6a1 1 0 00-1-1z" clip-rule="evenodd"/>
                    </svg>
                    Create Alert
                </Link>
            </div>
        </template>

        <div class="py-6">
            <div class="mx-auto max-w-7xl px-4 sm:px-6 lg:px-8">
                <div class="bg-white rounded-lg shadow-sm border border-gray-200">
                    <div class="p-6">

                        <!-- Alerts List -->
                        <div v-if="alerts.data.length > 0" class="space-y-4">
                            <div
                                v-for="alert in alerts.data"
                                :key="alert.id"
                                class="border border-gray-200 rounded-lg p-6 hover:shadow-md transition-shadow duration-200"
                                :class="alert.status === 'active' ? 'border-l-4 border-l-red-500 bg-red-50/30' : ''"
                            >
                                <!-- Alert Header -->
                                <div class="flex justify-between items-start mb-4">
                                    <div class="flex-1">
                                        <div class="flex items-center gap-3 mb-2">
                                            <h3 class="text-lg font-semibold text-gray-900">{{ alert.alert_title }}</h3>
                                            <span :class="getAlertLevelColor(alert.alert_level)"
                                                class="inline-flex items-center px-2 py-1 rounded-full text-xs font-medium border">
                                                {{ alert.alert_level.toUpperCase() }}
                                            </span>
                                        </div>
                                        <div class="flex items-center gap-4 text-sm text-gray-600">
                                            <div class="flex items-center">
                                                <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2" />
                                                </svg>
                                                {{ alert.disease.name }}
                                            </div>
                                            <div class="flex items-center">
                                                <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z" />
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z" />
                                                </svg>
                                                {{ alert.municipality.name }}
                                            </div>
                                            <div class="flex items-center">
                                                <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z" />
                                                </svg>
                                                {{ formatDate(alert.alert_start_date) }}
                                            </div>
                                        </div>
                                    </div>
                                    <span :class="getStatusColor(alert.status)"
                                        class="inline-flex items-center px-2 py-1 rounded-full text-xs font-medium">
                                        <div class="w-2 h-2 rounded-full mr-1.5"
                                             :class="alert.status === 'active' ? 'bg-green-400' : 'bg-gray-400'"></div>
                                        {{ alert.status }}
                                    </span>
                                </div>

                                <!-- Alert Details -->
                                <div class="mb-4">
                                    <p class="text-sm text-gray-600 line-clamp-2">{{ alert.alert_details }}</p>
                                    <div v-if="alert.affected_barangays && alert.affected_barangays.length > 0" class="mt-2">
                                        <span class="text-xs text-gray-500">
                                            {{ alert.affected_barangays.length }} barangay(s) affected
                                        </span>
                                    </div>
                                </div>

                                <!-- Actions -->
                                <div class="flex items-center justify-between pt-3 border-t border-gray-200">
                                    <div class="text-xs text-gray-500">
                                        Created {{ formatDate(alert.created_at) }}
                                    </div>
                                    <div class="flex items-center space-x-2">
                                        <Link :href="route('outbreak-alerts.show', alert.id)"
                                            class="inline-flex items-center px-3 py-1.5 text-xs font-medium text-blue-600 bg-blue-50 rounded-md hover:bg-blue-100 transition-colors duration-150">
                                            <svg class="w-3 h-3 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" />
                                            </svg>
                                            View
                                        </Link>
                                        <Link :href="route('outbreak-alerts.edit', alert.id)"
                                            class="inline-flex items-center px-3 py-1.5 text-xs font-medium text-gray-600 bg-gray-50 rounded-md hover:bg-gray-100 transition-colors duration-150">
                                            <svg class="w-3 h-3 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z" />
                                            </svg>
                                            Edit
                                        </Link>
                                        <button v-if="alert.status === 'published'"
                                            @click="resolveAlert(alert.id)"
                                            class="inline-flex items-center px-3 py-1.5 text-xs font-medium text-green-600 bg-green-50 rounded-md hover:bg-green-100 transition-colors duration-150">
                                            <svg class="w-3 h-3 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                                            </svg>
                                            Resolve
                                        </button>
                                        <button v-if="alert.status === 'published'"
                                            @click="concludeAlert(alert.id)"
                                            class="inline-flex items-center px-3 py-1.5 text-xs font-medium text-purple-600 bg-purple-50 rounded-md hover:bg-purple-100 transition-colors duration-150">
                                            <svg class="w-3 h-3 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v11a2 2 0 002 2h5.586a1 1 0 00.707-.293l5.414-5.414a1 1 0 00.293-.707V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-6 9l2 2 4-4" />
                                            </svg>
                                            Conclude
                                        </button>
                                        <button @click="deleteAlert(alert.id)"
                                            class="inline-flex items-center px-3 py-1.5 text-xs font-medium text-red-600 bg-red-50 rounded-md hover:bg-red-100 transition-colors duration-150">
                                            <svg class="w-3 h-3 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                                            </svg>
                                            Delete
                                        </button>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <!-- Empty State -->
                        <div v-else class="text-center py-12">
                            <div class="mx-auto h-16 w-16 rounded-full bg-gray-100 flex items-center justify-center mb-4">
                                <svg class="h-8 w-8 text-gray-400" fill="currentColor" viewBox="0 0 20 20">
                                    <path fill-rule="evenodd" d="M8.257 3.099c.765-1.36 2.722-1.36 3.486 0l5.58 9.92c.75 1.334-.213 2.98-1.742 2.98H4.42c-1.53 0-2.493-1.646-1.743-2.98l5.58-9.92zM11 13a1 1 0 11-2 0 1 1 0 012 0zm-1-8a1 1 0 00-1 1v3a1 1 0 002 0V6a1 1 0 00-1-1z" clip-rule="evenodd"/>
                                </svg>
                            </div>
                            <h3 class="text-lg font-medium text-gray-900 mb-2">No outbreak alerts</h3>
                            <p class="text-gray-500 mb-4">Get started by creating your first outbreak alert.</p>
                            <Link
                                :href="route('outbreak-alerts.create')"
                                class="inline-flex items-center px-4 py-2 bg-red-600 text-white text-sm font-medium rounded-lg hover:bg-red-700 transition-colors duration-200"
                            >
                                <svg class="w-4 h-4 mr-2" fill="currentColor" viewBox="0 0 20 20">
                                    <path fill-rule="evenodd" d="M10 3a1 1 0 011 1v5h5a1 1 0 110 2h-5v5a1 1 0 11-2 0v-5H4a1 1 0 110-2h5V4a1 1 0 011-1z" clip-rule="evenodd" />
                                </svg>
                                Create First Alert
                            </Link>
                        </div>

                        <!-- Pagination -->
                        <div v-if="alerts.data.length > 0 && alerts.links.length > 3" class="mt-6 flex justify-center border-t border-gray-200 pt-4">
                            <nav class="flex space-x-1">
                                <Link
                                    v-for="(link, index) in alerts.links"
                                    :key="index"
                                    :href="link.url || '#'"
                                    :class="[
                                        'px-3 py-2 text-sm font-medium rounded-md transition-colors duration-150',
                                        link.active
                                            ? 'bg-red-600 text-white'
                                            : 'text-gray-700 bg-white border border-gray-300 hover:bg-gray-50',
                                        !link.url ? 'opacity-50 cursor-not-allowed' : ''
                                    ]"
                                    :disabled="!link.url"
                                    v-html="link.label"
                                />
                            </nav>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </AuthenticatedLayout>
</template>
