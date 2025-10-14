<script setup lang="ts">
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import { Head, Link } from '@inertiajs/vue3';

interface Disease {
    id: number;
    name: string;
    category: string;
}

interface Municipality {
    id: number;
    name: string;
}

interface User {
    id: number;
    name: string;
}

interface OutbreakAlert {
    id: number;
    disease_id: number;
    alert_title: string;
    alert_details: string;
    alert_level: string;
    municipality_id: number;
    affected_barangays: string[];
    alert_start_date: string;
    alert_end_date: string | null;
    health_advisory: string | null;
    status: string;
    created_by: number;
    published_at: string | null;
    created_at: string;
    updated_at: string;
    disease: Disease;
    municipality: Municipality;
    creator: User;
}

interface Props {
    alert: OutbreakAlert;
}

const props = defineProps<Props>();

const getAlertLevelColor = (level: string) => {
    const colors: Record<string, string> = {
        'Green': 'bg-green-500',
        'Yellow': 'bg-yellow-500',
        'Orange': 'bg-orange-500',
        'Red': 'bg-red-600',
    };
    return colors[level] || 'bg-gray-500';
};

const getStatusBadge = (status: string) => {
    const badges: Record<string, string> = {
        'draft': 'bg-gray-100 text-gray-800',
        'published': 'bg-green-100 text-green-800',
        'archived': 'bg-blue-100 text-blue-800',
    };
    return badges[status] || 'bg-gray-100 text-gray-800';
};

const formatDate = (date: string | null) => {
    if (!date) return 'N/A';
    return new Date(date).toLocaleDateString('en-US', {
        year: 'numeric',
        month: 'long',
        day: 'numeric'
    });
};
</script>

<template>
    <Head :title="`Outbreak Alert: ${alert.alert_title}`" />

    <AuthenticatedLayout>
        <template #header>
            <div class="flex justify-between items-center">
                <h2 class="text-xl font-semibold text-gray-900">
                    Outbreak Alert Details
                </h2>
                <div class="flex gap-3">
                    <Link
                        :href="route('outbreak-alerts.index')"
                        class="inline-flex items-center px-4 py-2 text-sm font-medium text-gray-600 bg-white border border-gray-300 rounded-lg hover:bg-gray-50 transition-colors duration-200"
                    >
                        <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18" />
                        </svg>
                        Back to Alerts
                    </Link>
                    <Link
                        :href="route('outbreak-alerts.edit', alert.id)"
                        class="inline-flex items-center px-4 py-2 text-sm font-medium text-white bg-blue-600 border border-transparent rounded-lg hover:bg-blue-700 transition-colors duration-200"
                    >
                        <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z" />
                        </svg>
                        Edit Alert
                    </Link>
                </div>
            </div>
        </template>

        <div class="py-6">
            <div class="mx-auto max-w-5xl px-4 sm:px-6 lg:px-8">
                <!-- Alert Header Card -->
                <div class="bg-white rounded-lg shadow-sm border border-gray-200 mb-6">
                    <div class="p-6 border-b border-gray-200">
                        <div class="flex items-start justify-between">
                            <div class="flex-1">
                                <div class="flex items-center gap-3 mb-3">
                                    <div class="w-12 h-12 rounded-full bg-red-50 flex items-center justify-center">
                                        <svg class="w-6 h-6 text-red-600" fill="currentColor" viewBox="0 0 20 20">
                                            <path fill-rule="evenodd" d="M8.257 3.099c.765-1.36 2.722-1.36 3.486 0l5.58 9.92c.75 1.334-.213 2.98-1.742 2.98H4.42c-1.53 0-2.493-1.646-1.743-2.98l5.58-9.92zM11 13a1 1 0 11-2 0 1 1 0 012 0zm-1-8a1 1 0 00-1 1v3a1 1 0 002 0V6a1 1 0 00-1-1z" clip-rule="evenodd"/>
                                        </svg>
                                    </div>
                                    <div class="flex-1">
                                        <h1 class="text-2xl font-semibold text-gray-900 mb-2">{{ alert.alert_title }}</h1>
                                        <div class="flex items-center gap-2">
                                            <span :class="[
                                                'inline-flex items-center px-2 py-1 rounded-full text-xs font-medium border',
                                                alert.alert_level === 'Red' ? 'bg-red-50 text-red-700 border-red-200' :
                                                alert.alert_level === 'Orange' ? 'bg-orange-50 text-orange-700 border-orange-200' :
                                                alert.alert_level === 'Yellow' ? 'bg-yellow-50 text-yellow-700 border-yellow-200' :
                                                'bg-green-50 text-green-700 border-green-200'
                                            ]">
                                                {{ alert.alert_level }} Alert
                                            </span>
                                            <span :class="[
                                                'inline-flex items-center px-2 py-1 rounded-full text-xs font-medium',
                                                alert.status === 'published' ? 'bg-green-100 text-green-800' :
                                                alert.status === 'archived' ? 'bg-blue-100 text-blue-800' :
                                                'bg-gray-100 text-gray-800'
                                            ]">
                                                <div :class="[
                                                    'w-2 h-2 rounded-full mr-1.5',
                                                    alert.status === 'published' ? 'bg-green-400' :
                                                    alert.status === 'archived' ? 'bg-blue-400' :
                                                    'bg-gray-400'
                                                ]"></div>
                                                {{ alert.status }}
                                            </span>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Quick Info Grid -->
                    <div class="p-6">
                        <div class="grid grid-cols-1 md:grid-cols-4 gap-4">
                            <!-- Disease -->
                            <div class="text-center p-4 bg-gray-50 rounded-lg">
                                <svg class="w-8 h-8 text-red-600 mx-auto mb-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2" />
                                </svg>
                                <h3 class="text-sm font-medium text-gray-900">Disease</h3>
                                <p class="text-lg font-semibold text-gray-900 mt-1">{{ alert.disease.name }}</p>
                                <p class="text-xs text-gray-500">{{ alert.disease.category }}</p>
                            </div>

                            <!-- Location -->
                            <div class="text-center p-4 bg-gray-50 rounded-lg">
                                <svg class="w-8 h-8 text-blue-600 mx-auto mb-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z" />
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z" />
                                </svg>
                                <h3 class="text-sm font-medium text-gray-900">Location</h3>
                                <p class="text-lg font-semibold text-gray-900 mt-1">{{ alert.municipality.name }}</p>
                                <p v-if="alert.affected_barangays && alert.affected_barangays.length > 0" class="text-xs text-gray-500">
                                    {{ alert.affected_barangays.length }} barangay(s)
                                </p>
                            </div>

                            <!-- Start Date -->
                            <div class="text-center p-4 bg-gray-50 rounded-lg">
                                <svg class="w-8 h-8 text-green-600 mx-auto mb-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z" />
                                </svg>
                                <h3 class="text-sm font-medium text-gray-900">Start Date</h3>
                                <p class="text-lg font-semibold text-gray-900 mt-1">{{ formatDate(alert.alert_start_date) }}</p>
                            </div>

                            <!-- Created By -->
                            <div class="text-center p-4 bg-gray-50 rounded-lg">
                                <svg class="w-8 h-8 text-purple-600 mx-auto mb-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z" />
                                </svg>
                                <h3 class="text-sm font-medium text-gray-900">Issued By</h3>
                                <p class="text-lg font-semibold text-gray-900 mt-1">{{ alert.creator.name }}</p>
                                <p class="text-xs text-gray-500">{{ formatDate(alert.created_at) }}</p>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Content Cards -->
                <div class="space-y-6">
                    <!-- Alert Details -->
                    <div class="bg-white rounded-lg shadow-sm border border-gray-200">
                        <div class="p-6">
                            <h3 class="text-lg font-medium text-gray-900 mb-4">Alert Details</h3>
                            <div class="prose max-w-none">
                                <p class="text-gray-700 whitespace-pre-line leading-relaxed">{{ alert.alert_details }}</p>
                            </div>
                        </div>
                    </div>

                    <!-- Affected Barangays -->
                    <div v-if="alert.affected_barangays && alert.affected_barangays.length > 0" class="bg-white rounded-lg shadow-sm border border-gray-200">
                        <div class="p-6">
                            <h3 class="text-lg font-medium text-gray-900 mb-4">Affected Barangays</h3>
                            <div class="flex flex-wrap gap-2">
                                <span
                                    v-for="(barangay, index) in alert.affected_barangays"
                                    :key="index"
                                    class="inline-flex items-center px-3 py-1 rounded-full text-sm font-medium bg-blue-50 text-blue-700 border border-blue-200"
                                >
                                    {{ barangay }}
                                </span>
                            </div>
                        </div>
                    </div>

                    <!-- Health Advisory -->
                    <div v-if="alert.health_advisory" class="bg-white rounded-lg shadow-sm border border-gray-200">
                        <div class="p-6">
                            <div class="flex items-center gap-3 mb-4">
                                <div class="w-8 h-8 rounded-full bg-amber-100 flex items-center justify-center">
                                    <svg class="w-5 h-5 text-amber-600" fill="currentColor" viewBox="0 0 20 20">
                                        <path fill-rule="evenodd" d="M8.257 3.099c.765-1.36 2.722-1.36 3.486 0l5.58 9.92c.75 1.334-.213 2.98-1.742 2.98H4.42c-1.53 0-2.493-1.646-1.743-2.98l5.58-9.92zM11 13a1 1 0 11-2 0 1 1 0 012 0zm-1-8a1 1 0 00-1 1v3a1 1 0 002 0V6a1 1 0 00-1-1z" clip-rule="evenodd" />
                                    </svg>
                                </div>
                                <h3 class="text-lg font-medium text-gray-900">Health Advisory</h3>
                            </div>
                            <div class="bg-amber-50 border border-amber-200 rounded-lg p-4">
                                <p class="text-amber-800 whitespace-pre-line leading-relaxed">{{ alert.health_advisory }}</p>
                            </div>
                        </div>
                    </div>

                    <!-- Timeline & Metadata -->
                    <div class="bg-white rounded-lg shadow-sm border border-gray-200">
                        <div class="p-6">
                            <h3 class="text-lg font-medium text-gray-900 mb-4">Timeline & Information</h3>
                            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                                <div>
                                    <h4 class="text-sm font-medium text-gray-900 mb-2">Alert Period</h4>
                                    <div class="space-y-1 text-sm text-gray-600">
                                        <p><span class="font-medium">Start:</span> {{ formatDate(alert.alert_start_date) }}</p>
                                        <p><span class="font-medium">End:</span> {{ formatDate(alert.alert_end_date) }}</p>
                                    </div>
                                </div>
                                <div>
                                    <h4 class="text-sm font-medium text-gray-900 mb-2">Record Information</h4>
                                    <div class="space-y-1 text-sm text-gray-600">
                                        <p><span class="font-medium">Created:</span> {{ formatDate(alert.created_at) }}</p>
                                        <p><span class="font-medium">Updated:</span> {{ formatDate(alert.updated_at) }}</p>
                                        <p v-if="alert.published_at"><span class="font-medium">Published:</span> {{ formatDate(alert.published_at) }}</p>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </AuthenticatedLayout>
</template>
