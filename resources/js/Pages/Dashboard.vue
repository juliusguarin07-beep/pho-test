<script setup lang="ts">
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import { Head, Link, usePage } from '@inertiajs/vue3';
import { computed, ref } from 'vue';

interface Props {
    statistics?: {
        total_cases: number;
        pending_validation: number;
        validated_cases: number;
        approved_cases: number;
        draft_cases: number;
        returned_cases: number;
    };
    recentCases?: Array<any>;
    outbreakAlerts?: Array<any>;
    automaticAlerts?: Array<any>;
}

const props = withDefaults(defineProps<Props>(), {
    statistics: () => ({
        total_cases: 0,
        pending_validation: 0,
        validated_cases: 0,
        approved_cases: 0,
        draft_cases: 0,
        returned_cases: 0,
    }),
    recentCases: () => [],
    outbreakAlerts: () => [],
    automaticAlerts: () => [],
});

const page = usePage();
const auth = computed(() => page.props.auth as any);
const user = computed(() => auth.value.user);
const userRoles = computed(() => user.value?.roles || []);
const isEncoder = computed(() => userRoles.value.includes('encoder'));
const isValidator = computed(() => userRoles.value.includes('validator'));
const isPESUAdmin = computed(() => userRoles.value.includes('pesu_admin'));

const getCurrentTime = () => {
    const now = new Date();
    return now.toLocaleTimeString('en-US', { hour: '2-digit', minute: '2-digit' });
};

const getCurrentDate = () => {
    const now = new Date();
    return now.toLocaleDateString('en-US', { weekday: 'long', year: 'numeric', month: 'long', day: 'numeric' });
};

const currentTime = ref(getCurrentTime());
const currentDate = ref(getCurrentDate());

setInterval(() => {
    currentTime.value = getCurrentTime();
}, 60000);

const getAlertColor = (level: string) => {
    const colors: Record<string, string> = {
        'Green': 'bg-green-50 text-green-800 border-green-200',
        'Yellow': 'bg-yellow-50 text-yellow-800 border-yellow-200',
        'Orange': 'bg-orange-50 text-orange-800 border-orange-200',
        'Red': 'bg-red-50 text-red-800 border-red-200',
    };
    return colors[level] || 'bg-gray-50 text-gray-800 border-gray-200';
};

const getStatusColor = (status: string) => {
    const colors: Record<string, string> = {
        'draft': 'bg-gray-100 text-gray-700 border-gray-200',
        'submitted': 'bg-blue-100 text-blue-700 border-blue-200',
        'validated': 'bg-green-100 text-green-700 border-green-200',
        'approved': 'bg-purple-100 text-purple-700 border-purple-200',
        'returned': 'bg-orange-100 text-orange-700 border-orange-200',
    };
    return colors[status] || 'bg-gray-100 text-gray-700 border-gray-200';
};

const getStatusBadge = (status: string) => {
    const badges: Record<string, { bg: string; text: string; icon: string }> = {
        'draft': { bg: 'bg-gray-500', text: 'text-white', icon: 'Draft' },
        'submitted': { bg: 'bg-blue-500', text: 'text-white', icon: 'Submitted' },
        'validated': { bg: 'bg-green-500', text: 'text-white', icon: 'Validated' },
        'approved': { bg: 'bg-purple-500', text: 'text-white', icon: 'Approved' },
        'returned': { bg: 'bg-orange-500', text: 'text-white', icon: '↩️' },
    };
    return badges[status] || badges.draft;
};

const formatDate = (dateString: string) => {
    if (!dateString) return 'N/A';
    try {
        const date = new Date(dateString);
        // Check if the date is valid
        if (isNaN(date.getTime())) return 'Invalid Date';

        return date.toLocaleDateString('en-US', {
            year: 'numeric',
            month: 'short',
            day: 'numeric'
        });
    } catch (error) {
        return 'Invalid Date';
    }
};

const getAutomaticAlertColor = (caseCount: number) => {
    if (caseCount >= 50) {
        return {
            bg: 'bg-red-50',
            border: 'border-red-200',
            text: 'text-red-800',
            icon: 'CRITICAL',
            level: 'CRITICAL ALERT',
            color: '#F44336'
        };
    } else if (caseCount >= 25) {
        return {
            bg: 'bg-orange-50',
            border: 'border-orange-200',
            text: 'text-orange-800',
            icon: 'HIGH',
            level: 'HIGH ALERT',
            color: '#FF9800'
        };
    } else if (caseCount >= 10) {
        return {
            bg: 'bg-yellow-50',
            border: 'border-yellow-200',
            text: 'text-yellow-800',
            icon: 'MODERATE',
            level: 'MODERATE ALERT',
            color: '#FFC107'
        };
    }
    return null;
};
</script>

<template>
    <Head title="Dashboard" />

    <AuthenticatedLayout>
        <template #header>
            <h2 class="text-xl font-semibold leading-tight text-gray-800">
                <span v-if="isEncoder">Encoder Dashboard - My Case Reports</span>
                <span v-else-if="isValidator">Validator Dashboard - Facility Review</span>
                <span v-else-if="isPESUAdmin">PESU Admin Dashboard - Provincial Overview</span>
                <span v-else>Dashboard</span>
            </h2>
        </template>

        <div class="py-8">
            <div class="mx-auto max-w-7xl sm:px-6 lg:px-8 space-y-6">

                <!-- AUTOMATIC OUTBREAK ALERTS - Shown at the top for all users -->
                <div v-if="automaticAlerts && automaticAlerts.length > 0" class="space-y-3">
                    <div
                        v-for="alert in automaticAlerts"
                        :key="alert.id"
                        class="relative overflow-hidden rounded-lg border-l-4 p-4 shadow-lg"
                        :class="[
                            getAutomaticAlertColor(alert.case_count)?.bg,
                            getAutomaticAlertColor(alert.case_count)?.border
                        ]"
                        :style="{
                            borderLeftColor: getAutomaticAlertColor(alert.case_count)?.color
                        }"
                    >
                        <div class="flex items-start justify-between">
                            <div class="flex items-start space-x-3">
                                <div class="flex-shrink-0">
                                    <div
                                        class="flex h-8 w-8 items-center justify-center rounded-full text-sm font-bold"
                                        :style="{
                                            backgroundColor: getAutomaticAlertColor(alert.case_count)?.color,
                                            color: 'white'
                                        }"
                                    >
                                        {{ getAutomaticAlertColor(alert.case_count)?.icon }}
                                    </div>
                                </div>
                                <div class="min-w-0 flex-1">
                                    <div class="flex items-center space-x-2 mb-1">
                                        <h3
                                            class="text-sm font-bold uppercase tracking-wide"
                                            :class="getAutomaticAlertColor(alert.case_count)?.text"
                                        >
                                            {{ getAutomaticAlertColor(alert.case_count)?.level }}
                                        </h3>
                                        <span
                                            class="inline-flex items-center rounded-full px-2 py-1 text-xs font-medium"
                                            :style="{
                                                backgroundColor: getAutomaticAlertColor(alert.case_count)?.color + '20',
                                                color: getAutomaticAlertColor(alert.case_count)?.color
                                            }"
                                        >
                                            {{ alert.case_count }} cases
                                        </span>
                                        <!-- Status Badge -->
                                        <span
                                            class="inline-flex items-center rounded-full px-2 py-1 text-xs font-medium"
                                            :class="alert.status === 'published' ? 'bg-green-100 text-green-800' : 'bg-yellow-100 text-yellow-800'"
                                        >
                                            {{ alert.status === 'published' ? '📢 Published' : '📝 Draft' }}
                                        </span>
                                    </div>
                                    <p
                                        class="text-sm font-semibold mb-1"
                                        :class="getAutomaticAlertColor(alert.case_count)?.text"
                                    >
                                        {{ alert.disease?.name }} outbreak in {{ alert.municipality?.name }}
                                    </p>
                                    <p
                                        class="text-xs"
                                        :class="getAutomaticAlertColor(alert.case_count)?.text"
                                    >
                                        {{ alert.case_count }} cases reported in the last 7 days • Threshold: {{ alert.threshold_reached }} cases
                                        <span v-if="alert.status === 'draft'" class="block mt-1 text-yellow-700 font-medium">
                                            ⚠️ This is an automatic draft alert. PESU admin can review and publish for residents.
                                        </span>
                                    </p>
                                </div>
                            </div>
                            <div class="flex-shrink-0">
                                <svg
                                    class="h-5 w-5"
                                    :class="getAutomaticAlertColor(alert.case_count)?.text"
                                    fill="currentColor"
                                    viewBox="0 0 20 20"
                                >
                                    <path fill-rule="evenodd" d="M8.257 3.099c.765-1.36 2.722-1.36 3.486 0l5.58 9.92c.75 1.334-.213 2.98-1.742 2.98H4.42c-1.53 0-2.493-1.646-1.743-2.98l5.58-9.92zM11 13a1 1 0 11-2 0 1 1 0 012 0zm-1-8a1 1 0 00-1 1v3a1 1 0 002 0V6a1 1 0 00-1-1z" clip-rule="evenodd" />
                                </svg>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- ENCODER DASHBOARD -->
                <div v-if="isEncoder">
                    <!-- Modern Welcome Banner for Encoder -->
                    <div class="relative overflow-hidden bg-gradient-to-r from-indigo-600 via-blue-600 to-cyan-600 rounded-2xl shadow-2xl">
                        <div class="absolute inset-0 bg-black opacity-10"></div>
                        <div class="absolute inset-0" style="background-image: url('data:image/svg+xml,%3Csvg width=&quot;60&quot; height=&quot;60&quot; viewBox=&quot;0 0 60 60&quot; xmlns=&quot;http://www.w3.org/2000/svg&quot;%3E%3Cg fill=&quot;none&quot; fill-rule=&quot;evenodd&quot;%3E%3Cg fill=&quot;%23ffffff&quot; fill-opacity=&quot;0.05&quot;%3E%3Cpath d=&quot;M36 34v-4h-2v4h-4v2h4v4h2v-4h4v-2h-4zm0-30V0h-2v4h-4v2h4v4h2V6h4V4h-4zM6 34v-4H4v4H0v2h4v4h2v-4h4v-2H6zM6 4V0H4v4H0v2h4v4h2V6h4V4H6z&quot;/%3E%3C/g%3E%3C/g%3E%3C/svg%3E'); opacity: 0.1;"></div>
                        <div class="relative px-8 py-10">
                            <div class="flex items-center justify-between">
                                <div class="flex items-center space-x-6">
                                    <div class="flex-shrink-0">
                                        <div class="h-20 w-20 rounded-full bg-white bg-opacity-20 backdrop-blur-sm flex items-center justify-center border-4 border-white border-opacity-30">
                                            <svg class="w-10 h-10 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z" />
                                            </svg>
                                        </div>
                                    </div>
                                    <div class="text-white">
                                        <h1 class="text-3xl font-bold mb-1">Welcome back, {{ user?.name }}!</h1>
                                        <p class="text-blue-100 text-lg font-medium">
                                            Disease Surveillance Encoder
                                            <span v-if="user?.municipality?.name" class="text-blue-200 font-normal"> - {{ user.municipality.name }}</span>
                                        </p>
                                        <p class="text-blue-200 text-sm mt-1">{{ currentDate }}</p>
                                    </div>
                                </div>
                                <div class="hidden lg:flex flex-col items-end space-y-2">
                                    <div class="text-white text-5xl font-bold">{{ currentTime }}</div>
                                    <div class="text-blue-200 text-sm">Your Facility Dashboard</div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Enhanced Encoder Stats Cards -->
                    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6">
                        <!-- Total Cases Card -->
                        <Link :href="route('case-reports.index')" class="group relative bg-white rounded-xl shadow-md hover:shadow-2xl transition-all duration-300 overflow-hidden transform hover:-translate-y-1 cursor-pointer">
                            <div class="absolute top-0 right-0 w-32 h-32 bg-gradient-to-br from-blue-400 to-blue-600 rounded-bl-full opacity-10 group-hover:opacity-20 transition-opacity"></div>
                            <div class="p-6 relative">
                                <div class="flex items-center justify-between mb-4">
                                    <div class="p-3 bg-blue-100 rounded-lg">
                                        <svg class="w-6 h-6 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                                        </svg>
                                    </div>
                                    <div class="text-blue-600 text-xs font-semibold uppercase tracking-wide">Total</div>
                                </div>
                                <h3 class="text-gray-600 text-sm font-medium mb-1">My Total Cases</h3>
                                <div class="flex items-end justify-between">
                                    <div class="text-4xl font-extrabold text-gray-900">{{ statistics.total_cases }}</div>
                                    <div class="text-xs text-gray-500">All submissions</div>
                                </div>
                            </div>
                            <div class="h-1 bg-gradient-to-r from-blue-400 to-blue-600"></div>
                        </Link>

                        <!-- Draft Cases Card -->
                        <Link :href="route('case-reports.index', { status: 'draft' })" class="group relative bg-white rounded-xl shadow-md hover:shadow-2xl transition-all duration-300 overflow-hidden transform hover:-translate-y-1 cursor-pointer">
                            <div class="absolute top-0 right-0 w-32 h-32 bg-gradient-to-br from-gray-400 to-gray-600 rounded-bl-full opacity-10 group-hover:opacity-20 transition-opacity"></div>
                            <div class="p-6 relative">
                                <div class="flex items-center justify-between mb-4">
                                    <div class="p-3 bg-gray-100 rounded-lg">
                                        <svg class="w-6 h-6 text-gray-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z" />
                                        </svg>
                                    </div>
                                    <div class="text-gray-600 text-xs font-semibold uppercase tracking-wide">Draft</div>
                                </div>
                                <h3 class="text-gray-600 text-sm font-medium mb-1">Draft Reports</h3>
                                <div class="flex items-end justify-between">
                                    <div class="text-4xl font-extrabold text-gray-900">{{ statistics.draft_cases }}</div>
                                    <div class="text-xs text-gray-500">Incomplete</div>
                                </div>
                            </div>
                            <div class="h-1 bg-gradient-to-r from-gray-400 to-gray-600"></div>
                        </Link>

                        <!-- Submitted Card -->
                        <Link :href="route('case-reports.index', { status: 'submitted' })" class="group relative bg-white rounded-xl shadow-md hover:shadow-2xl transition-all duration-300 overflow-hidden transform hover:-translate-y-1 cursor-pointer">
                            <div class="absolute top-0 right-0 w-32 h-32 bg-gradient-to-br from-green-400 to-green-600 rounded-bl-full opacity-10 group-hover:opacity-20 transition-opacity"></div>
                            <div class="p-6 relative">
                                <div class="flex items-center justify-between mb-4">
                                    <div class="p-3 bg-green-100 rounded-lg">
                                        <svg class="w-6 h-6 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
                                        </svg>
                                    </div>
                                    <div class="text-green-600 text-xs font-semibold uppercase tracking-wide">Active</div>
                                </div>
                                <h3 class="text-gray-600 text-sm font-medium mb-1">Submitted</h3>
                                <div class="flex items-end justify-between">
                                    <div class="text-4xl font-extrabold text-gray-900">{{ statistics.pending_validation }}</div>
                                    <div class="text-xs text-gray-500">Under review</div>
                                </div>
                            </div>
                            <div class="h-1 bg-gradient-to-r from-green-400 to-green-600"></div>
                        </Link>

                        <!-- Returned Card -->
                        <Link :href="route('case-reports.index', { status: 'returned' })" class="group relative bg-white rounded-xl shadow-md hover:shadow-2xl transition-all duration-300 overflow-hidden transform hover:-translate-y-1 cursor-pointer">
                            <div class="absolute top-0 right-0 w-32 h-32 bg-gradient-to-br from-orange-400 to-orange-600 rounded-bl-full opacity-10 group-hover:opacity-20 transition-opacity"></div>
                            <div class="p-6 relative">
                                <div class="flex items-center justify-between mb-4">
                                    <div class="p-3 bg-orange-100 rounded-lg">
                                        <svg class="w-6 h-6 text-orange-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z" />
                                        </svg>
                                    </div>
                                    <div class="text-orange-600 text-xs font-semibold uppercase tracking-wide">Action</div>
                                </div>
                                <h3 class="text-gray-600 text-sm font-medium mb-1">Need Correction</h3>
                                <div class="flex items-end justify-between">
                                    <div class="text-4xl font-extrabold text-gray-900">{{ statistics.returned_cases }}</div>
                                    <div class="text-xs text-gray-500">Requires fix</div>
                                </div>
                            </div>
                            <div class="h-1 bg-gradient-to-r from-orange-400 to-orange-600"></div>
                        </Link>
                    </div>

                    <!-- Modern Quick Actions for Encoder -->
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <!-- Create New Case - Primary Action -->
                        <Link :href="route('case-reports.create')" class="group relative bg-gradient-to-br from-blue-500 via-blue-600 to-indigo-600 rounded-2xl shadow-xl hover:shadow-2xl transition-all duration-300 overflow-hidden transform hover:scale-105">
                            <div class="absolute inset-0 bg-black opacity-0 group-hover:opacity-10 transition-opacity"></div>
                            <div class="p-8 relative">
                                <div class="flex items-center justify-between">
                                    <div class="flex-1">
                                        <div class="inline-flex items-center justify-center w-14 h-14 bg-white bg-opacity-20 rounded-xl mb-4 backdrop-blur-sm">
                                            <svg class="w-8 h-8 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M12 4v16m8-8H4" />
                                            </svg>
                                        </div>
                                        <h3 class="text-2xl font-bold text-white mb-2">Create New Case Report</h3>
                                        <p class="text-blue-100 text-sm">Start documenting a new disease surveillance case investigation</p>
                                    </div>
                                    <div class="ml-6">
                                        <svg class="w-12 h-12 text-white opacity-50 group-hover:opacity-100 transition-opacity" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 7l5 5m0 0l-5 5m5-5H6" />
                                        </svg>
                                    </div>
                                </div>
                            </div>
                        </Link>

                        <!-- View All Cases -->
                        <Link :href="route('case-reports.index')" class="group relative bg-white rounded-2xl shadow-xl hover:shadow-2xl transition-all duration-300 overflow-hidden transform hover:scale-105 border-2 border-gray-100">
                            <div class="p-8 relative">
                                <div class="flex items-center justify-between">
                                    <div class="flex-1">
                                        <div class="inline-flex items-center justify-center w-14 h-14 bg-gradient-to-br from-blue-500 to-indigo-600 rounded-xl mb-4">
                                            <svg class="w-8 h-8 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2" />
                                            </svg>
                                        </div>
                                        <h3 class="text-2xl font-bold text-gray-900 mb-2">My Case Reports</h3>
                                        <p class="text-gray-600 text-sm">View, manage, and track all your submitted reports</p>
                                    </div>
                                    <div class="ml-6">
                                        <svg class="w-12 h-12 text-gray-400 group-hover:text-blue-600 transition-colors" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 7l5 5m0 0l-5 5m5-5H6" />
                                        </svg>
                                    </div>
                                </div>
                            </div>
                        </Link>
                    </div>
                </div>

                <!-- VALIDATOR DASHBOARD -->
                <div v-else-if="isValidator">
                    <!-- Modern Welcome Banner for Validator -->
                    <div class="relative overflow-hidden bg-gradient-to-r from-emerald-600 via-green-600 to-teal-600 rounded-2xl shadow-2xl">
                        <div class="absolute inset-0 bg-black opacity-10"></div>
                        <div class="absolute inset-0" style="background-image: url('data:image/svg+xml,%3Csvg width=&quot;60&quot; height=&quot;60&quot; viewBox=&quot;0 0 60 60&quot; xmlns=&quot;http://www.w3.org/2000/svg&quot;%3E%3Cg fill=&quot;none&quot; fill-rule=&quot;evenodd&quot;%3E%3Cg fill=&quot;%23ffffff&quot; fill-opacity=&quot;0.05&quot;%3E%3Cpath d=&quot;M36 34v-4h-2v4h-4v2h4v4h2v-4h4v-2h-4zm0-30V0h-2v4h-4v2h4v4h2V6h4V4h-4zM6 34v-4H4v4H0v2h4v4h2v-4h4v-2H6zM6 4V0H4v4H0v2h4v4h2V6h4V4H6z&quot;/%3E%3C/g%3E%3C/g%3E%3C/svg%3E'); opacity: 0.1;"></div>
                        <div class="relative px-8 py-10">
                            <div class="flex items-center justify-between">
                                <div class="flex items-center space-x-6">
                                    <div class="flex-shrink-0">
                                        <div class="h-20 w-20 rounded-full bg-white bg-opacity-20 backdrop-blur-sm flex items-center justify-center border-4 border-white border-opacity-30">
                                            <svg class="w-10 h-10 text-white" fill="currentColor" viewBox="0 0 20 20">
                                                <path fill-rule="evenodd" d="M6.267 3.455a3.066 3.066 0 001.745-.723 3.066 3.066 0 013.976 0 3.066 3.066 0 001.745.723 3.066 3.066 0 012.812 2.812c.051.643.304 1.254.723 1.745a3.066 3.066 0 010 3.976 3.066 3.066 0 00-.723 1.745 3.066 3.066 0 01-2.812 2.812 3.066 3.066 0 00-1.745.723 3.066 3.066 0 01-3.976 0 3.066 3.066 0 00-1.745-.723 3.066 3.066 0 01-2.812-2.812 3.066 3.066 0 00-.723-1.745 3.066 3.066 0 010-3.976 3.066 3.066 0 00.723-1.745 3.066 3.066 0 012.812-2.812zm7.44 5.252a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/>
                                            </svg>
                                        </div>
                                    </div>
                                    <div class="text-white">
                                        <h1 class="text-3xl font-bold mb-1">Welcome back, {{ user?.name }}!</h1>
                                        <p class="text-emerald-100 text-lg font-medium">
                                            District Hospital Validator
                                            <span v-if="user?.municipality?.name" class="text-emerald-200 font-normal"> - {{ user.municipality.name }}</span>
                                        </p>
                                        <p class="text-emerald-200 text-sm mt-1">{{ currentDate }}</p>
                                    </div>
                                </div>
                                <div class="hidden lg:flex flex-col items-end space-y-2">
                                    <div class="text-white text-5xl font-bold">{{ currentTime }}</div>
                                    <div class="text-emerald-200 text-sm">Facility Review Dashboard</div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Enhanced Validator Stats Cards -->
                    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-5 gap-5">
                        <!-- Facility Cases -->
                        <Link :href="route('case-reports.index')" class="group relative bg-white rounded-xl shadow-md hover:shadow-2xl transition-all duration-300 overflow-hidden transform hover:-translate-y-1 cursor-pointer">
                            <div class="absolute top-0 right-0 w-24 h-24 bg-gradient-to-br from-green-400 to-emerald-600 rounded-bl-full opacity-10 group-hover:opacity-20 transition-opacity"></div>
                            <div class="p-5 relative">
                                <div class="flex items-center justify-between mb-3">
                                    <div class="p-2.5 bg-emerald-100 rounded-lg">
                                        <svg class="w-5 h-5 text-emerald-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4" />
                                        </svg>
                                    </div>
                                </div>
                                <h3 class="text-gray-600 text-xs font-medium mb-1 uppercase tracking-wide">Facility Total</h3>
                                <div class="text-3xl font-extrabold text-gray-900">{{ statistics.total_cases }}</div>
                                <div class="text-xs text-gray-500 mt-1">All cases from facility</div>
                            </div>
                            <div class="h-1 bg-gradient-to-r from-green-400 to-emerald-600"></div>
                        </Link>

                        <!-- Pending Review -->
                        <Link :href="route('case-reports.index', { status: 'submitted' })" class="group relative bg-white rounded-xl shadow-md hover:shadow-2xl transition-all duration-300 overflow-hidden transform hover:-translate-y-1 ring-2 ring-yellow-200 cursor-pointer">
                            <div class="absolute top-0 right-0 w-24 h-24 bg-gradient-to-br from-yellow-400 to-amber-600 rounded-bl-full opacity-10 group-hover:opacity-20 transition-opacity"></div>
                            <div class="p-5 relative">
                                <div class="flex items-center justify-between mb-3">
                                    <div class="p-2.5 bg-yellow-100 rounded-lg animate-pulse">
                                        <svg class="w-5 h-5 text-yellow-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" />
                                        </svg>
                                    </div>
                                    <span class="text-yellow-600 text-xs font-bold uppercase">Urgent</span>
                                </div>
                                <h3 class="text-gray-600 text-xs font-medium mb-1 uppercase tracking-wide">Pending Review</h3>
                                <div class="text-3xl font-extrabold text-yellow-600">{{ statistics.pending_validation }}</div>
                                <div class="text-xs text-gray-500 mt-1">Awaiting validation</div>
                            </div>
                            <div class="h-1 bg-gradient-to-r from-yellow-400 to-amber-600"></div>
                        </Link>

                        <!-- Validated -->
                        <Link :href="route('case-reports.index', { status: 'validated' })" class="group relative bg-white rounded-xl shadow-md hover:shadow-2xl transition-all duration-300 overflow-hidden transform hover:-translate-y-1 cursor-pointer">
                            <div class="absolute top-0 right-0 w-24 h-24 bg-gradient-to-br from-green-400 to-green-600 rounded-bl-full opacity-10 group-hover:opacity-20 transition-opacity"></div>
                            <div class="p-5 relative">
                                <div class="flex items-center justify-between mb-3">
                                    <div class="p-2.5 bg-green-100 rounded-lg">
                                        <svg class="w-5 h-5 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                                        </svg>
                                    </div>
                                </div>
                                <h3 class="text-gray-600 text-xs font-medium mb-1 uppercase tracking-wide">Validated</h3>
                                <div class="text-3xl font-extrabold text-green-600">{{ statistics.validated_cases }}</div>
                                <div class="text-xs text-gray-500 mt-1">By you</div>
                            </div>
                            <div class="h-1 bg-gradient-to-r from-green-400 to-green-600"></div>
                        </Link>

                        <!-- Approved -->
                        <Link :href="route('case-reports.index', { status: 'approved' })" class="group relative bg-white rounded-xl shadow-md hover:shadow-2xl transition-all duration-300 overflow-hidden transform hover:-translate-y-1 cursor-pointer">
                            <div class="absolute top-0 right-0 w-24 h-24 bg-gradient-to-br from-purple-400 to-purple-600 rounded-bl-full opacity-10 group-hover:opacity-20 transition-opacity"></div>
                            <div class="p-5 relative">
                                <div class="flex items-center justify-between mb-3">
                                    <div class="p-2.5 bg-purple-100 rounded-lg">
                                        <svg class="w-5 h-5 text-purple-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 3v4M3 5h4M6 17v4m-2-2h4m5-16l2.286 6.857L21 12l-5.714 2.143L13 21l-2.286-6.857L5 12l5.714-2.143L13 3z" />
                                        </svg>
                                    </div>
                                </div>
                                <h3 class="text-gray-600 text-xs font-medium mb-1 uppercase tracking-wide">Approved</h3>
                                <div class="text-3xl font-extrabold text-purple-600">{{ statistics.approved_cases }}</div>
                                <div class="text-xs text-gray-500 mt-1">Final approval</div>
                            </div>
                            <div class="h-1 bg-gradient-to-r from-purple-400 to-purple-600"></div>
                        </Link>

                        <!-- Returned -->
                        <Link :href="route('case-reports.index', { status: 'returned' })" class="group relative bg-white rounded-xl shadow-md hover:shadow-2xl transition-all duration-300 overflow-hidden transform hover:-translate-y-1 cursor-pointer">
                            <div class="absolute top-0 right-0 w-24 h-24 bg-gradient-to-br from-orange-400 to-orange-600 rounded-bl-full opacity-10 group-hover:opacity-20 transition-opacity"></div>
                            <div class="p-5 relative">
                                <div class="flex items-center justify-between mb-3">
                                    <div class="p-2.5 bg-orange-100 rounded-lg">
                                        <svg class="w-5 h-5 text-orange-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z" />
                                        </svg>
                                    </div>
                                </div>
                                <h3 class="text-gray-600 text-xs font-medium mb-1 uppercase tracking-wide">Returned</h3>
                                <div class="text-3xl font-extrabold text-orange-600">{{ statistics.returned_cases }}</div>
                                <div class="text-xs text-gray-500 mt-1">For correction</div>
                            </div>
                            <div class="h-1 bg-gradient-to-r from-orange-400 to-orange-600"></div>
                        </Link>
                    </div>

                    <!-- Modern Validator Quick Actions -->
                    <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                        <!-- Review Cases - Primary Action -->
                        <Link :href="route('case-reports.index')" class="group relative bg-gradient-to-br from-emerald-500 via-green-600 to-teal-600 rounded-2xl shadow-xl hover:shadow-2xl transition-all duration-300 overflow-hidden transform hover:scale-105">
                            <div class="absolute inset-0 bg-black opacity-0 group-hover:opacity-10 transition-opacity"></div>
                            <div class="p-6 relative">
                                <div class="flex items-center justify-between">
                                    <div class="flex-1">
                                        <div class="inline-flex items-center justify-center w-12 h-12 bg-white bg-opacity-20 rounded-xl mb-3 backdrop-blur-sm">
                                            <svg class="w-7 h-7 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-6 9l2 2 4-4" />
                                            </svg>
                                        </div>
                                        <h3 class="text-xl font-bold text-white mb-1">Review Cases</h3>
                                        <p class="text-emerald-100 text-sm">Validate pending reports</p>
                                        <div class="mt-3 inline-flex items-center px-3 py-1 bg-white bg-opacity-20 rounded-full">
                                            <span class="text-white text-xs font-bold">{{ statistics.pending_validation }} Pending</span>
                                        </div>
                                    </div>
                                    <svg class="w-8 h-8 text-white opacity-50 group-hover:opacity-100 transition-opacity ml-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 7l5 5m0 0l-5 5m5-5H6" />
                                    </svg>
                                </div>
                            </div>
                        </Link>

                        <!-- Analytics -->
                        <Link :href="route('analytics.index')" class="group relative bg-white rounded-2xl shadow-xl hover:shadow-2xl transition-all duration-300 overflow-hidden transform hover:scale-105 border-2 border-gray-100">
                            <div class="p-6 relative">
                                <div class="flex items-center justify-between">
                                    <div class="flex-1">
                                        <div class="inline-flex items-center justify-center w-12 h-12 bg-gradient-to-br from-blue-500 to-indigo-600 rounded-xl mb-3">
                                            <svg class="w-7 h-7 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z" />
                                            </svg>
                                        </div>
                                        <h3 class="text-xl font-bold text-gray-900 mb-1">Analytics</h3>
                                        <p class="text-gray-600 text-sm">View facility insights</p>
                                    </div>
                                    <svg class="w-8 h-8 text-gray-400 group-hover:text-blue-600 transition-colors ml-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 7l5 5m0 0l-5 5m5-5H6" />
                                    </svg>
                                </div>
                            </div>
                        </Link>

                        <!-- Export Data -->
                        <a :href="route('case-reports.export')" class="group relative bg-white rounded-2xl shadow-xl hover:shadow-2xl transition-all duration-300 overflow-hidden transform hover:scale-105 border-2 border-gray-100 cursor-pointer block">
                            <div class="p-6 relative">
                                <div class="flex items-center justify-between">
                                    <div class="flex-1">
                                        <div class="inline-flex items-center justify-center w-12 h-12 bg-gradient-to-br from-purple-500 to-pink-600 rounded-xl mb-3">
                                            <svg class="w-7 h-7 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                                            </svg>
                                        </div>
                                        <h3 class="text-xl font-bold text-gray-900 mb-1">Export Data</h3>
                                        <p class="text-gray-600 text-sm">Download facility reports</p>
                                    </div>
                                    <svg class="w-8 h-8 text-gray-400 group-hover:text-purple-600 transition-colors ml-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 7l5 5m0 0l-5 5m5-5H6" />
                                    </svg>
                                </div>
                            </div>
                        </a>
                    </div>
                </div>

                <!-- PESU ADMIN DASHBOARD -->
                <div v-else-if="isPESUAdmin">
                    <!-- Modern Welcome Banner for PESU Admin -->
                    <div class="relative bg-gradient-to-br from-purple-600 via-violet-600 to-purple-700 overflow-hidden shadow-2xl rounded-2xl border-2 border-purple-400">
                        <!-- Decorative Pattern -->
                        <div class="absolute inset-0 opacity-10">
                            <div class="absolute inset-0" style="background-image: radial-gradient(circle at 2px 2px, white 1px, transparent 0); background-size: 32px 32px;"></div>
                        </div>

                        <div class="relative p-8">
                            <div class="flex items-center justify-between">
                                <div class="flex items-center space-x-5">
                                    <!-- User Icon with Animation -->
                                    <div class="flex-shrink-0">
                                        <div class="relative">
                                            <div class="w-20 h-20 bg-white bg-opacity-20 rounded-2xl flex items-center justify-center backdrop-blur-sm shadow-xl ring-4 ring-white ring-opacity-30">
                                                <svg class="w-12 h-12 text-white" fill="currentColor" viewBox="0 0 20 20">
                                                    <path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-6-3a2 2 0 11-4 0 2 2 0 014 0zm-2 4a5 5 0 00-4.546 2.916A5.986 5.986 0 0010 16a5.986 5.986 0 004.546-2.084A5 5 0 0010 11z" clip-rule="evenodd"/>
                                                </svg>
                                            </div>
                                            <!-- Pulse Animation -->
                                            <div class="absolute -top-1 -right-1 w-4 h-4">
                                                <span class="animate-ping absolute inline-flex h-full w-full rounded-full bg-purple-200 opacity-75"></span>
                                                <span class="relative inline-flex rounded-full h-4 w-4 bg-purple-300"></span>
                                            </div>
                                        </div>
                                    </div>

                                    <!-- Welcome Text -->
                                    <div>
                                        <h3 class="text-3xl font-bold text-white mb-2 flex items-center">
                                            Welcome, {{ user?.name }}
                                        </h3>
                                        <div class="flex items-center space-x-2 mb-1">
                                            <span class="px-3 py-1 bg-white bg-opacity-25 rounded-full text-sm font-bold text-white backdrop-blur-sm border border-white border-opacity-30">
                                                PESU Super Administrator
                                                <span v-if="user?.municipality?.name" class="font-normal"> - {{ user.municipality.name }}</span>
                                            </span>
                                            <span class="px-3 py-1 bg-purple-800 bg-opacity-50 rounded-full text-xs font-semibold text-purple-100 backdrop-blur-sm">
                                                Full Access
                                            </span>
                                        </div>
                                        <p class="text-purple-100 text-sm font-medium mt-2">
                                            Provincial Epidemiology & Surveillance Unit - Complete System Control
                                        </p>
                                    </div>
                                </div>

                                <!-- Real-time Display -->
                                <div class="hidden lg:block text-right">
                                    <div class="bg-white bg-opacity-20 rounded-xl px-4 py-3 backdrop-blur-sm border border-white border-opacity-30">
                                        <div class="text-2xl font-bold text-white mb-1">{{ currentTime }}</div>
                                        <div class="text-xs text-purple-100 font-medium">{{ currentDate }}</div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Modern PESU Admin Stats Grid -->
                    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-6 gap-4">
                        <!-- Provincial Total -->
                        <Link :href="route('case-reports.index')" class="group bg-white overflow-hidden shadow-xl rounded-2xl p-6 border-l-4 border-blue-500 hover:shadow-2xl transition-all duration-300 transform hover:translate-y-[-4px] cursor-pointer">
                            <div class="flex items-start justify-between">
                                <div class="flex-1">
                                    <div class="flex items-center space-x-2 mb-2">
                                        <div class="w-10 h-10 bg-blue-100 rounded-xl flex items-center justify-center">
                                            <svg class="w-6 h-6 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z" />
                                            </svg>
                                        </div>
                                    </div>
                                    <div class="text-sm font-bold text-gray-500 uppercase tracking-wide mb-1">Provincial Total</div>
                                    <div class="text-4xl font-extrabold bg-gradient-to-r from-blue-600 to-indigo-600 bg-clip-text text-transparent mb-2">
                                        {{ statistics.total_cases }}
                                    </div>
                                    <div class="text-xs text-gray-500 font-medium">All cases</div>
                                </div>
                            </div>
                        </Link>

                        <!-- Pending -->
                        <Link :href="route('case-reports.index', { status: 'submitted' })" class="group bg-white overflow-hidden shadow-xl rounded-2xl p-6 border-l-4 border-yellow-500 hover:shadow-2xl transition-all duration-300 transform hover:translate-y-[-4px] cursor-pointer">
                            <div class="flex items-start justify-between">
                                <div class="flex-1">
                                    <div class="flex items-center space-x-2 mb-2">
                                        <div class="w-10 h-10 bg-yellow-100 rounded-xl flex items-center justify-center">
                                            <svg class="w-6 h-6 text-yellow-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" />
                                            </svg>
                                        </div>
                                    </div>
                                    <div class="text-sm font-bold text-gray-500 uppercase tracking-wide mb-1">Pending</div>
                                    <div class="text-4xl font-extrabold text-yellow-600 mb-2">
                                        {{ statistics.pending_validation }}
                                    </div>
                                    <div class="text-xs text-gray-500 font-medium">Submitted</div>
                                </div>
                            </div>
                        </Link>

                        <!-- Validated -->
                        <Link :href="route('case-reports.index', { status: 'validated' })" class="group bg-white overflow-hidden shadow-xl rounded-2xl p-6 border-l-4 border-green-500 hover:shadow-2xl transition-all duration-300 transform hover:translate-y-[-4px] cursor-pointer">
                            <div class="flex items-start justify-between">
                                <div class="flex-1">
                                    <div class="flex items-center space-x-2 mb-2">
                                        <div class="w-10 h-10 bg-green-100 rounded-xl flex items-center justify-center">
                                            <svg class="w-6 h-6 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                                            </svg>
                                        </div>
                                    </div>
                                    <div class="text-sm font-bold text-gray-500 uppercase tracking-wide mb-1">Validated</div>
                                    <div class="text-4xl font-extrabold text-green-600 mb-2">
                                        {{ statistics.validated_cases }}
                                    </div>
                                    <div class="text-xs text-gray-500 font-medium">By validators</div>
                                </div>
                            </div>
                        </Link>

                        <!-- Approved -->
                        <Link :href="route('case-reports.index', { status: 'approved' })" class="group bg-white overflow-hidden shadow-xl rounded-2xl p-6 border-l-4 border-purple-500 hover:shadow-2xl transition-all duration-300 transform hover:translate-y-[-4px] cursor-pointer">
                            <div class="flex items-start justify-between">
                                <div class="flex-1">
                                    <div class="flex items-center space-x-2 mb-2">
                                        <div class="w-10 h-10 bg-purple-100 rounded-xl flex items-center justify-center">
                                            <svg class="w-6 h-6 text-purple-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4M7.835 4.697a3.42 3.42 0 001.946-.806 3.42 3.42 0 014.438 0 3.42 3.42 0 001.946.806 3.42 3.42 0 013.138 3.138 3.42 3.42 0 00.806 1.946 3.42 3.42 0 010 4.438 3.42 3.42 0 00-.806 1.946 3.42 3.42 0 01-3.138 3.138 3.42 3.42 0 00-1.946.806 3.42 3.42 0 01-4.438 0 3.42 3.42 0 00-1.946-.806 3.42 3.42 0 01-3.138-3.138 3.42 3.42 0 00-.806-1.946 3.42 3.42 0 010-4.438 3.42 3.42 0 00.806-1.946 3.42 3.42 0 013.138-3.138z" />
                                            </svg>
                                        </div>
                                    </div>
                                    <div class="text-sm font-bold text-gray-500 uppercase tracking-wide mb-1">Approved</div>
                                    <div class="text-4xl font-extrabold text-purple-600 mb-2">
                                        {{ statistics.approved_cases }}
                                    </div>
                                    <div class="text-xs text-gray-500 font-medium">Final approval</div>
                                </div>
                            </div>
                        </Link>

                        <!-- Drafts -->
                        <Link :href="route('case-reports.index', { status: 'draft' })" class="group bg-white overflow-hidden shadow-xl rounded-2xl p-6 border-l-4 border-gray-500 hover:shadow-2xl transition-all duration-300 transform hover:translate-y-[-4px] cursor-pointer">
                            <div class="flex items-start justify-between">
                                <div class="flex-1">
                                    <div class="flex items-center space-x-2 mb-2">
                                        <div class="w-10 h-10 bg-gray-100 rounded-xl flex items-center justify-center">
                                            <svg class="w-6 h-6 text-gray-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z" />
                                            </svg>
                                        </div>
                                    </div>
                                    <div class="text-sm font-bold text-gray-500 uppercase tracking-wide mb-1">Drafts</div>
                                    <div class="text-4xl font-extrabold text-gray-600 mb-2">
                                        {{ statistics.draft_cases }}
                                    </div>
                                    <div class="text-xs text-gray-500 font-medium">Incomplete</div>
                                </div>
                            </div>
                        </Link>

                        <!-- Returned -->
                        <Link :href="route('case-reports.index', { status: 'returned' })" class="group bg-white overflow-hidden shadow-xl rounded-2xl p-6 border-l-4 border-orange-500 hover:shadow-2xl transition-all duration-300 transform hover:translate-y-[-4px] cursor-pointer">
                            <div class="flex items-start justify-between">
                                <div class="flex-1">
                                    <div class="flex items-center space-x-2 mb-2">
                                        <div class="w-10 h-10 bg-orange-100 rounded-xl flex items-center justify-center">
                                            <svg class="w-6 h-6 text-orange-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 15v-1a4 4 0 00-4-4H8m0 0l3 3m-3-3l3-3m9 14V5a2 2 0 00-2-2H6a2 2 0 00-2 2v16l4-2 4 2 4-2 4 2z" />
                                            </svg>
                                        </div>
                                    </div>
                                    <div class="text-sm font-bold text-gray-500 uppercase tracking-wide mb-1">Returned</div>
                                    <div class="text-4xl font-extrabold text-orange-600 mb-2">
                                        {{ statistics.returned_cases }}
                                    </div>
                                    <div class="text-xs text-gray-500 font-medium">For correction</div>
                                </div>
                            </div>
                        </Link>
                    </div>

                    <!-- Modern PESU Admin Action Cards -->
                    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6">
                        <!-- All Cases -->
                        <Link :href="route('case-reports.index')" class="group relative overflow-hidden rounded-2xl shadow-xl hover:shadow-2xl transition-all duration-300 transform hover:scale-105 border-2 border-purple-200">
                            <div class="absolute inset-0 bg-gradient-to-br from-purple-500 via-purple-600 to-violet-600"></div>
                            <div class="absolute inset-0 bg-black opacity-0 group-hover:opacity-10 transition-opacity"></div>
                            <div class="relative p-8 text-center">
                                <div class="flex justify-center mb-4">
                                    <div class="p-4 bg-white bg-opacity-20 rounded-2xl backdrop-blur-sm ring-4 ring-white ring-opacity-30 group-hover:scale-110 transition-transform duration-300">
                                        <svg class="w-10 h-10 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-6 9l2 2 4-4" />
                                        </svg>
                                    </div>
                                </div>
                                <h4 class="text-xl font-bold text-white mb-2">All Cases</h4>
                                <p class="text-purple-100 text-sm font-medium">Review all reports</p>
                                <div class="mt-4 flex items-center justify-center text-white opacity-75 group-hover:opacity-100 transition-opacity">
                                    <span class="text-sm font-semibold">View Details</span>
                                    <svg class="w-5 h-5 ml-2 group-hover:translate-x-1 transition-transform" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 7l5 5m0 0l-5 5m5-5H6" />
                                    </svg>
                                </div>
                            </div>
                        </Link>

                        <!-- Outbreak Alerts -->
                        <Link :href="route('outbreak-alerts.index')" class="group relative overflow-hidden rounded-2xl shadow-xl hover:shadow-2xl transition-all duration-300 transform hover:scale-105 border-2 border-red-200">
                            <div class="absolute inset-0 bg-gradient-to-br from-red-500 via-red-600 to-rose-600"></div>
                            <div class="absolute inset-0 bg-black opacity-0 group-hover:opacity-10 transition-opacity"></div>
                            <div class="relative p-8 text-center">
                                <div class="flex justify-center mb-4">
                                    <div class="p-4 bg-white bg-opacity-20 rounded-2xl backdrop-blur-sm ring-4 ring-white ring-opacity-30 group-hover:scale-110 transition-transform duration-300">
                                        <svg class="w-10 h-10 text-white" fill="currentColor" viewBox="0 0 20 20">
                                            <path fill-rule="evenodd" d="M8.257 3.099c.765-1.36 2.722-1.36 3.486 0l5.58 9.92c.75 1.334-.213 2.98-1.742 2.98H4.42c-1.53 0-2.493-1.646-1.743-2.98l5.58-9.92zM11 13a1 1 0 11-2 0 1 1 0 012 0zm-1-8a1 1 0 00-1 1v3a1 1 0 002 0V6a1 1 0 00-1-1z" clip-rule="evenodd"/>
                                        </svg>
                                    </div>
                                </div>
                                <h4 class="text-xl font-bold text-white mb-2">Outbreak Alerts</h4>
                                <p class="text-red-100 text-sm font-medium">Manage alerts</p>
                                <div class="mt-4 flex items-center justify-center text-white opacity-75 group-hover:opacity-100 transition-opacity">
                                    <span class="text-sm font-semibold">View Details</span>
                                    <svg class="w-5 h-5 ml-2 group-hover:translate-x-1 transition-transform" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 7l5 5m0 0l-5 5m5-5H6" />
                                    </svg>
                                </div>
                            </div>
                        </Link>

                        <!-- User Management -->
                        <Link :href="route('users.index')" class="group relative overflow-hidden rounded-2xl shadow-xl hover:shadow-2xl transition-all duration-300 transform hover:scale-105 border-2 border-blue-200">
                            <div class="absolute inset-0 bg-gradient-to-br from-blue-500 via-blue-600 to-indigo-600"></div>
                            <div class="absolute inset-0 bg-black opacity-0 group-hover:opacity-10 transition-opacity"></div>
                            <div class="relative p-8 text-center">
                                <div class="flex justify-center mb-4">
                                    <div class="p-4 bg-white bg-opacity-20 rounded-2xl backdrop-blur-sm ring-4 ring-white ring-opacity-30 group-hover:scale-110 transition-transform duration-300">
                                        <svg class="w-10 h-10 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z" />
                                        </svg>
                                    </div>
                                </div>
                                <h4 class="text-xl font-bold text-white mb-2">User Management</h4>
                                <p class="text-blue-100 text-sm font-medium">Manage users</p>
                                <div class="mt-4 flex items-center justify-center text-white opacity-75 group-hover:opacity-100 transition-opacity">
                                    <span class="text-sm font-semibold">View Details</span>
                                    <svg class="w-5 h-5 ml-2 group-hover:translate-x-1 transition-transform" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 7l5 5m0 0l-5 5m5-5H6" />
                                    </svg>
                                </div>
                            </div>
                        </Link>

                        <!-- Analytics -->
                        <Link :href="route('analytics.index')" class="group relative overflow-hidden rounded-2xl shadow-xl hover:shadow-2xl transition-all duration-300 transform hover:scale-105 border-2 border-green-200">
                            <div class="absolute inset-0 bg-gradient-to-br from-green-500 via-green-600 to-emerald-600"></div>
                            <div class="absolute inset-0 bg-black opacity-0 group-hover:opacity-10 transition-opacity"></div>
                            <div class="relative p-8 text-center">
                                <div class="flex justify-center mb-4">
                                    <div class="p-4 bg-white bg-opacity-20 rounded-2xl backdrop-blur-sm ring-4 ring-white ring-opacity-30 group-hover:scale-110 transition-transform duration-300">
                                        <svg class="w-10 h-10 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z" />
                                        </svg>
                                    </div>
                                </div>
                                <h4 class="text-xl font-bold text-white mb-2">Analytics</h4>
                                <p class="text-green-100 text-sm font-medium">View reports</p>
                                <div class="mt-4 flex items-center justify-center text-white opacity-75 group-hover:opacity-100 transition-opacity">
                                    <span class="text-sm font-semibold">View Details</span>
                                    <svg class="w-5 h-5 ml-2 group-hover:translate-x-1 transition-transform" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 7l5 5m0 0l-5 5m5-5H6" />
                                    </svg>
                                </div>
                            </div>
                        </Link>
                    </div>
                </div>



                <!-- Active Outbreak Alerts -->
                <div v-if="outbreakAlerts && outbreakAlerts.length > 0" class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                    <div class="p-6">
                        <h3 class="text-lg font-semibold text-gray-900 mb-4 flex items-center">
                            <svg class="w-5 h-5 mr-2 text-red-600" fill="currentColor" viewBox="0 0 20 20">
                                <path fill-rule="evenodd" d="M8.257 3.099c.765-1.36 2.722-1.36 3.486 0l5.58 9.92c.75 1.334-.213 2.98-1.742 2.98H4.42c-1.53 0-2.493-1.646-1.743-2.98l5.58-9.92zM11 13a1 1 0 11-2 0 1 1 0 012 0zm-1-8a1 1 0 00-1 1v3a1 1 0 002 0V6a1 1 0 00-1-1z" clip-rule="evenodd" />
                            </svg>
                            Active Outbreak Alerts
                        </h3>
                        <div class="space-y-3">
                            <div
                                v-for="alert in outbreakAlerts"
                                :key="alert.id"
                                :class="['p-4 rounded-lg border-2', getAlertColor(alert.alert_level)]"
                            >
                                <div class="flex justify-between items-start">
                                    <div class="flex-1">
                                        <h4 class="font-semibold text-lg">{{ alert.alert_title }}</h4>
                                        <p class="text-sm mt-1">{{ alert.disease?.name }} - {{ alert.municipality?.name || 'Provincial Wide' }}</p>
                                        <p class="text-sm mt-2 italic">{{ alert.health_advisory }}</p>
                                    </div>
                                    <span class="px-3 py-1 rounded-full text-xs font-bold whitespace-nowrap">
                                        {{ alert.alert_level }} Alert
                                    </span>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Recent Cases -->
                <div v-if="recentCases && recentCases.length > 0" class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                    <div class="p-6">
                        <div class="flex justify-between items-center mb-4">
                            <h3 class="text-lg font-semibold text-gray-900">Recent Case Reports</h3>
                            <Link :href="route('case-reports.index')" class="text-blue-600 hover:text-blue-800 text-sm font-medium">
                                View All →
                            </Link>
                        </div>
                        <div class="overflow-x-auto">
                            <table class="min-w-full divide-y divide-gray-200">
                                <thead class="bg-gray-50">
                                    <tr>
                                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Case ID</th>
                                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Disease</th>
                                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Patient</th>
                                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Municipality</th>
                                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Status</th>
                                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Date</th>
                                    </tr>
                                </thead>
                                <tbody class="bg-white divide-y divide-gray-200">
                                    <tr v-for="caseReport in recentCases" :key="caseReport.id" class="hover:bg-gray-50">
                                        <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-blue-600">
                                            <Link :href="`/case-reports/${caseReport.id}`">{{ caseReport.case_id }}</Link>
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">{{ caseReport.disease?.name }}</td>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                            {{ caseReport.last_name }}, {{ caseReport.first_name }}
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">{{ caseReport.municipality?.name }}</td>
                                        <td class="px-6 py-4 whitespace-nowrap">
                                            <span :class="['px-2 py-1 inline-flex text-xs leading-5 font-semibold rounded-full', getStatusColor(caseReport.status)]">
                                                {{ caseReport.status.charAt(0).toUpperCase() + caseReport.status.slice(1) }}
                                            </span>
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">{{ formatDate(caseReport.date_reported) }}</td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </AuthenticatedLayout>
</template>

