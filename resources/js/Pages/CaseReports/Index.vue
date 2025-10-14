<script setup lang="ts">
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import { Head, Link, router, usePage } from '@inertiajs/vue3';
import { ref, computed } from 'vue';

interface CaseReport {
    id: number;
    case_id: string;
    date_reported: string;
    disease: { name: string };
    last_name: string;
    first_name: string;
    municipality: { name: string };
    barangay: { name: string };
    status: string;
    case_classification: string;
    validator_feedback?: string;
}

interface Props {
    caseReports: {
        data: CaseReport[];
        links: any[];
        current_page: number;
        last_page: number;
    };
    diseases: Array<{ id: number; name: string }>;
    municipalities: Array<{ id: number; name: string }>;
    filters: {
        disease?: number;
        municipality?: number;
        status?: string;
        date_from?: string;
        date_to?: string;
    };
    userRole: string;
}

const props = defineProps<Props>();

// Get user role from page props and props
const page = usePage();
const userRoles = computed(() => (page.props.auth as any).user?.roles || []);
const isEncoder = computed(() => userRoles.value.includes('encoder'));
const isPesuAdmin = computed(() => userRoles.value.includes('pesu_admin') || props.userRole === 'pesu_admin');
const isValidator = computed(() => userRoles.value.includes('validator') || props.userRole === 'validator');

const filters = ref({
    disease: props.filters.disease || '',
    municipality: props.filters.municipality || '',
    status: props.filters.status || '',
    date_from: props.filters.date_from || '',
    date_to: props.filters.date_to || '',
});

const applyFilters = () => {
    router.get('/case-reports', filters.value, {
        preserveState: true,
        preserveScroll: true,
    });
};

const resetFilters = () => {
    filters.value = {
        disease: '',
        municipality: '',
        status: '',
        date_from: '',
        date_to: '',
    };
    applyFilters();
};

const getStatusColor = (status: string) => {
    const colors: Record<string, string> = {
        draft: 'bg-gray-100 text-gray-700 border-gray-200',
        submitted: 'bg-blue-100 text-blue-700 border-blue-200',
        validated: 'bg-green-100 text-green-700 border-green-200',
        approved: 'bg-purple-100 text-purple-700 border-purple-200',
        returned: 'bg-orange-100 text-orange-700 border-orange-200',
    };
    return colors[status] || 'bg-gray-100 text-gray-700 border-gray-200';
};

const getStatusIcon = (status: string) => {
    const icons: Record<string, string> = {
        draft: '',
        submitted: '',
        validated: '',
        approved: '',
        returned: '↩️',
    };
    return icons[status] || '';
};

const getClassificationColor = (classification: string) => {
    const colors: Record<string, string> = {
        Suspect: 'bg-yellow-50 text-yellow-700 border-yellow-300 ring-1 ring-yellow-200',
        Confirmed: 'bg-red-50 text-red-700 border-red-300 ring-1 ring-red-200',
    };
    return colors[classification] || 'bg-gray-50 text-gray-700 border-gray-300 ring-1 ring-gray-200';
};

const formatDate = (dateString: string) => {
    const date = new Date(dateString);
    return date.toLocaleDateString('en-US', {
        year: 'numeric',
        month: 'short',
        day: 'numeric'
    });
};

const deleteReport = (caseReport: CaseReport) => {
    const confirmMessage = `Are you sure you want to delete Case Report ${caseReport.case_id}?\n\nPatient: ${caseReport.last_name}, ${caseReport.first_name}\nDisease: ${caseReport.disease.name}\nStatus: ${caseReport.status}\n\nThis action cannot be undone.`;

    if (confirm(confirmMessage)) {
        router.delete(route('case-reports.destroy', caseReport.id), {
            preserveScroll: true,
            onSuccess: () => {
                // The page will automatically refresh with updated data
            },
            onError: (errors) => {
                alert('Failed to delete case report: ' + Object.values(errors).join(', '));
            }
        });
    }
};

</script>

<template>
    <Head title="Case Reports" />

    <AuthenticatedLayout>
        <template #header>
            <div class="flex items-center justify-between">
                <div class="flex items-center space-x-4">
                    <div class="p-3 bg-blue-100 rounded-xl">
                        <svg class="text-blue-600 w-7 h-7" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                        </svg>
                    </div>
                    <div>
                        <h2 class="text-2xl font-bold text-gray-900">Case Reports</h2>
                        <p class="text-sm text-gray-600 mt-0.5">
                            <span v-if="isEncoder">Manage your submitted case reports</span>
                            <span v-else-if="isValidator">Review and validate case reports from your facility</span>
                            <span v-else-if="isPesuAdmin">Manage and track disease surveillance case investigations across all municipalities</span>
                            <span v-else>Manage and track disease surveillance case investigations</span>
                        </p>
                    </div>
                </div>
                <Link
                    v-if="isEncoder"
                    :href="route('case-reports.create')"
                    class="inline-flex items-center px-6 py-3 text-sm font-bold tracking-wide text-white uppercase transition-all duration-200 transform border border-transparent shadow-lg group bg-gradient-to-r from-blue-600 to-indigo-600 rounded-xl hover:from-blue-700 hover:to-indigo-700 focus:outline-none focus:ring-4 focus:ring-blue-300 hover:shadow-xl hover:scale-105"
                >
                    <svg class="w-5 h-5 mr-2 transition-transform duration-300 group-hover:rotate-90" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M12 4v16m8-8H4" />
                    </svg>
                    New Case Report
                </Link>
            </div>
        </template>

        <div class="min-h-screen py-8 bg-gray-50">
            <div class="mx-auto space-y-6 max-w-7xl sm:px-6 lg:px-8">

                <!-- Modern Filters Card -->
                <div class="overflow-hidden bg-white border border-gray-100 shadow-xl rounded-2xl">
                    <div class="p-6">
                        <div class="flex items-center mb-6">
                            <div class="p-2 mr-3 bg-indigo-100 rounded-lg">
                                <svg class="w-5 h-5 text-indigo-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 4a1 1 0 011-1h16a1 1 0 011 1v2.586a1 1 0 01-.293.707l-6.414 6.414a1 1 0 00-.293.707V17l-4 4v-6.586a1 1 0 00-.293-.707L3.293 7.293A1 1 0 013 6.586V4z" />
                                </svg>
                            </div>
                            <h3 class="text-lg font-bold text-gray-900">Filters</h3>
                        </div>

                        <div class="grid grid-cols-1 gap-4 md:grid-cols-2" :class="isPesuAdmin ? 'lg:grid-cols-5' : 'lg:grid-cols-4'">
                            <div>
                                <label class="block mb-2 text-sm font-semibold text-gray-700">Disease</label>
                                <select v-model="filters.disease"
                                        class="w-full rounded-xl border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-2 focus:ring-indigo-200 transition-all duration-200 py-2.5 px-3">
                                    <option value="">All Diseases</option>
                                    <option v-for="disease in diseases" :key="disease.id" :value="disease.id">
                                        {{ disease.name }}
                                    </option>
                                </select>
                            </div>

                            <div v-if="isPesuAdmin">
                                <label class="block mb-2 text-sm font-semibold text-gray-700">Municipality</label>
                                <select v-model="filters.municipality"
                                        class="w-full rounded-xl border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-2 focus:ring-indigo-200 transition-all duration-200 py-2.5 px-3">
                                    <option value="">All Municipalities</option>
                                    <option v-for="municipality in municipalities" :key="municipality.id" :value="municipality.id">
                                        {{ municipality.name }}
                                    </option>
                                </select>
                            </div>

                            <div>
                                <label class="block mb-2 text-sm font-semibold text-gray-700">Status</label>
                                <select v-model="filters.status"
                                        class="w-full rounded-xl border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-2 focus:ring-indigo-200 transition-all duration-200 py-2.5 px-3">
                                    <option value="">All Status</option>
                                    <option value="draft">Draft</option>
                                    <option value="submitted">Submitted</option>
                                    <option value="validated">Validated</option>
                                    <option value="approved">Approved</option>
                                    <option value="returned">↩️ Returned</option>
                                </select>
                            </div>

                            <div>
                                <label class="block mb-2 text-sm font-semibold text-gray-700">Date From</label>
                                <input
                                    v-model="filters.date_from"
                                    type="date"
                                    class="w-full rounded-xl border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-2 focus:ring-indigo-200 transition-all duration-200 py-2.5 px-3"
                                />
                            </div>

                            <div>
                                <label class="block mb-2 text-sm font-semibold text-gray-700">Date To</label>
                                <input
                                    v-model="filters.date_to"
                                    type="date"
                                    class="w-full rounded-xl border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-2 focus:ring-indigo-200 transition-all duration-200 py-2.5 px-3"
                                />
                            </div>
                        </div>

                        <div class="flex flex-wrap gap-3 mt-6">
                            <button
                                @click="applyFilters"
                                class="inline-flex items-center px-6 py-2.5 bg-gradient-to-r from-blue-600 to-indigo-600 text-white rounded-xl hover:from-blue-700 hover:to-indigo-700 text-sm font-bold shadow-md hover:shadow-lg transition-all duration-200 transform hover:scale-105"
                            >
                                <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
                                </svg>
                                Apply Filters
                            </button>
                            <button
                                @click="resetFilters"
                                class="inline-flex items-center px-6 py-2.5 bg-white border-2 border-gray-300 text-gray-700 rounded-xl hover:bg-gray-50 hover:border-gray-400 text-sm font-semibold transition-all duration-200"
                            >
                                <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15" />
                                </svg>
                                Reset
                            </button>
                            <a
                                :href="route('case-reports.export')"
                                class="ml-auto inline-flex items-center px-6 py-2.5 bg-gradient-to-r from-green-600 to-emerald-600 text-white rounded-xl hover:from-green-700 hover:to-emerald-700 text-sm font-bold shadow-md hover:shadow-lg transition-all duration-200 transform hover:scale-105"
                            >
                                <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                                </svg>
                                Export to Excel
                            </a>
                        </div>
                    </div>
                </div>

                <!-- Modern Case Reports Table -->
                <div class="overflow-hidden bg-white border border-gray-100 shadow-xl rounded-2xl">
                    <div class="p-6">
                        <div class="flex items-center justify-between mb-6">
                            <div>
                                <h3 class="text-lg font-bold text-gray-900">Case Reports List</h3>
                                <p class="mt-1 text-sm text-gray-600">{{ caseReports.data.length }} records found</p>
                            </div>
                            <div class="text-sm text-gray-600">
                                Page {{ caseReports.current_page }} of {{ caseReports.last_page }}
                            </div>
                        </div>

                        <div class="overflow-x-auto">
                            <table class="min-w-full divide-y divide-gray-200">
                                <thead>
                                    <tr class="bg-gradient-to-r from-gray-50 to-gray-100">
                                        <th class="px-6 py-4 text-xs font-bold tracking-wider text-left text-gray-700 uppercase">Case ID</th>
                                        <th class="px-6 py-4 text-xs font-bold tracking-wider text-left text-gray-700 uppercase">Disease</th>
                                        <th class="px-6 py-4 text-xs font-bold tracking-wider text-left text-gray-700 uppercase">Patient Name</th>
                                        <th class="px-6 py-4 text-xs font-bold tracking-wider text-left text-gray-700 uppercase">Location</th>
                                        <th class="px-6 py-4 text-xs font-bold tracking-wider text-left text-gray-700 uppercase">Classification</th>
                                        <th class="px-6 py-4 text-xs font-bold tracking-wider text-left text-gray-700 uppercase">Status</th>
                                        <th class="px-6 py-4 text-xs font-bold tracking-wider text-left text-gray-700 uppercase">Date Reported</th>
                                        <th class="px-6 py-4 text-xs font-bold tracking-wider text-left text-gray-700 uppercase">Actions</th>
                                    </tr>
                                </thead>
                                <tbody class="bg-white divide-y divide-gray-100">
                                    <tr v-for="caseReport in caseReports.data" :key="caseReport.id"
                                        class="transition-colors duration-150 hover:bg-blue-50">
                                        <td class="px-6 py-4 whitespace-nowrap">
                                            <Link :href="`/case-reports/${caseReport.id}`"
                                                  class="text-sm font-bold text-blue-600 hover:text-blue-800 hover:underline">
                                                {{ caseReport.case_id }}
                                            </Link>
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap">
                                            <div class="flex items-center">
                                                <div class="flex-shrink-0 w-2 h-2 mr-2 bg-blue-500 rounded-full"></div>
                                                <div class="text-sm font-medium text-gray-900">{{ caseReport.disease.name }}</div>
                                            </div>
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap">
                                            <div class="text-sm font-medium text-gray-900">
                                                {{ caseReport.last_name }}, {{ caseReport.first_name }}
                                            </div>
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap">
                                            <div class="text-sm text-gray-700">
                                                <div class="font-medium">{{ caseReport.barangay.name }}</div>
                                                <div class="text-xs text-gray-500">{{ caseReport.municipality.name }}</div>
                                            </div>
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap">
                                            <span :class="['px-3 py-1.5 inline-flex text-xs font-bold rounded-lg border-2', getClassificationColor(caseReport.case_classification)]">
                                                {{ caseReport.case_classification }}
                                            </span>
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap">
                                            <div class="flex flex-col gap-1">
                                                <span :class="['px-3 py-1.5 inline-flex items-center text-xs font-bold rounded-full border', getStatusColor(caseReport.status)]">
                                                    <span class="mr-1">{{ getStatusIcon(caseReport.status) }}</span>
                                                    {{ caseReport.status.charAt(0).toUpperCase() + caseReport.status.slice(1) }}
                                                </span>
                                                <div v-if="caseReport.status === 'returned' && caseReport.validator_feedback"
                                                     class="flex items-center text-xs text-red-600">
                                                    <svg class="w-3 h-3 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 8h10m0 0V6a2 2 0 00-2-2H9a2 2 0 00-2 2v2m0 0v10a2 2 0 002 2h6a2 2 0 002-2V8z" />
                                                    </svg>
                                                    Has Feedback
                                                </div>
                                            </div>
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap">
                                            <div class="text-sm text-gray-700">{{ formatDate(caseReport.date_reported) }}</div>
                                        </td>
                                        <td class="px-6 py-4 text-sm font-medium whitespace-nowrap">
                                            <div class="flex items-center space-x-2">
                                                <Link
                                                    :href="`/case-reports/${caseReport.id}`"
                                                    class="inline-flex items-center px-3 py-1.5 bg-blue-100 text-blue-700 rounded-lg hover:bg-blue-200 transition-colors duration-150 font-semibold"
                                                >
                                                    <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" />
                                                    </svg>
                                                    View
                                                </Link>
                                                <Link
                                                    v-if="!userRoles.includes('validator') && !isPesuAdmin && (caseReport.status === 'draft' || caseReport.status === 'returned' || caseReport.status === 'submitted')"
                                                    :href="route('case-reports.edit', caseReport.id)"
                                                    class="inline-flex items-center px-3 py-1.5 bg-green-100 text-green-700 rounded-lg hover:bg-green-200 transition-colors duration-150 font-semibold"
                                                >
                                                    <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z" />
                                                    </svg>
                                                    Edit
                                                </Link>
                                                <button
                                                    v-if="isPesuAdmin"
                                                    @click="deleteReport(caseReport)"
                                                    class="inline-flex items-center px-3 py-1.5 bg-red-100 text-red-700 rounded-lg hover:bg-red-200 transition-colors duration-150 font-semibold"
                                                >
                                                    <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                                                    </svg>
                                                    Delete
                                                </button>
                                            </div>
                                        </td>
                                    </tr>
                                    <tr v-if="caseReports.data.length === 0">
                                        <td colspan="8" class="px-6 py-12 text-center">
                                            <div class="flex flex-col items-center justify-center">
                                                <div class="p-4 mb-4 bg-gray-100 rounded-full">
                                                    <svg class="w-12 h-12 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                                                    </svg>
                                                </div>
                                                <h3 class="mb-1 text-lg font-semibold text-gray-900">No case reports found</h3>
                                                <p class="text-sm text-gray-500">Try adjusting your filters or create a new case report.</p>
                                            </div>
                                        </td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>

                        <!-- Modern Pagination -->
                        <div v-if="caseReports.links.length > 3" class="flex flex-col items-center justify-between gap-4 pt-6 mt-6 border-t border-gray-200 sm:flex-row">
                            <div class="text-sm text-gray-600">
                                Showing page <span class="font-bold text-gray-900">{{ caseReports.current_page }}</span> of
                                <span class="font-bold text-gray-900">{{ caseReports.last_page }}</span>
                            </div>
                            <div class="flex flex-wrap justify-center gap-1">
                                <Link
                                    v-for="(link, index) in caseReports.links"
                                    :key="index"
                                    :href="link.url"
                                    :class="[
                                        'px-4 py-2 text-sm font-semibold border rounded-lg transition-all duration-200',
                                        link.active
                                            ? 'bg-gradient-to-r from-blue-600 to-indigo-600 text-white border-transparent shadow-md'
                                            : 'bg-white text-gray-700 border-gray-300 hover:bg-gray-50 hover:border-gray-400',
                                        !link.url && 'opacity-50 cursor-not-allowed'
                                    ]"
                                    :disabled="!link.url"
                                    v-html="link.label"
                                />
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </AuthenticatedLayout>
</template>
