<script setup lang="ts">
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import { Head, router } from '@inertiajs/vue3';
import { ref } from 'vue';

interface Props {
    summary: {
        total_cases: number;
        confirmed_cases: number;
        deaths: number;
        active_outbreaks: number;
        case_fatality_rate: number;
    };
    casesByDisease: Array<{ disease: string; total: number }>;
    casesByMunicipality: Array<{ municipality: string; total: number }>;
    casesByStatus: Array<{ status: string; total: number }>;
    casesByClassification: Array<{ classification: string; total: number }>;
    casesByOutcome: Array<{ outcome: string; total: number }>;
    monthlyTrend: Array<{ month: string; total: number }>;
    ageDistribution: Array<{ age_group: string; total: number }>;
    genderDistribution: Array<{ sex: string; total: number }>;
    filters: {
        start_date: string;
        end_date: string;
    };
}

const props = defineProps<Props>();

const filters = ref({
    start_date: props.filters.start_date,
    end_date: props.filters.end_date,
});

const applyFilters = () => {
    router.get('/analytics', filters.value, {
        preserveState: true,
        preserveScroll: true,
    });
};

const getMaxValue = (data: Array<{ total: number }>) => {
    return Math.max(...data.map(d => d.total), 1);
};

const getPercentage = (value: number, max: number) => {
    return (value / max) * 100;
};
</script>

<template>
    <Head title="Analytics" />

    <AuthenticatedLayout>
        <template #header>
            <div>
                <h2 class="text-xl font-semibold text-gray-900">
                    Analytics Dashboard
                </h2>
                <p class="text-sm text-gray-600 mt-1">Disease surveillance data insights and trends</p>
            </div>
        </template>

        <div class="py-6">
            <div class="mx-auto max-w-7xl px-4 sm:px-6 lg:px-8 space-y-6">

                <!-- Date Filter -->
                <div class="bg-white rounded-lg shadow-sm border border-gray-200">
                    <div class="p-4">
                        <div class="flex items-end gap-4">
                            <div class="flex-1">
                                <label class="block text-sm font-medium text-gray-700 mb-1">Start Date</label>
                                <input
                                    v-model="filters.start_date"
                                    type="date"
                                    class="w-full px-3 py-2 border border-gray-300 rounded-lg text-sm focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition-colors duration-200"
                                />
                            </div>
                            <div class="flex-1">
                                <label class="block text-sm font-medium text-gray-700 mb-1">End Date</label>
                                <input
                                    v-model="filters.end_date"
                                    type="date"
                                    class="w-full px-3 py-2 border border-gray-300 rounded-lg text-sm focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition-colors duration-200"
                                />
                            </div>
                            <button
                                @click="applyFilters"
                                class="px-4 py-2 bg-blue-600 text-white text-sm font-medium rounded-lg hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:ring-offset-2 transition-colors duration-200"
                            >
                                Apply Filters
                            </button>
                        </div>
                    </div>
                </div>

                <!-- Summary Cards -->
                <div class="grid grid-cols-1 md:grid-cols-5 gap-4">
                    <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-6">
                        <div class="flex items-center">
                            <div class="p-2 bg-blue-100 rounded-lg">
                                <svg class="w-5 h-5 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                                </svg>
                            </div>
                            <div class="ml-4">
                                <p class="text-sm font-medium text-gray-600">Total Cases</p>
                                <p class="text-2xl font-semibold text-gray-900">{{ summary.total_cases }}</p>
                            </div>
                        </div>
                    </div>

                    <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-6">
                        <div class="flex items-center">
                            <div class="p-2 bg-green-100 rounded-lg">
                                <svg class="w-5 h-5 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                                </svg>
                            </div>
                            <div class="ml-4">
                                <p class="text-sm font-medium text-gray-600">Confirmed</p>
                                <p class="text-2xl font-semibold text-gray-900">{{ summary.confirmed_cases }}</p>
                            </div>
                        </div>
                    </div>

                    <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-6">
                        <div class="flex items-center">
                            <div class="p-2 bg-red-100 rounded-lg">
                                <svg class="w-5 h-5 text-red-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-2.5L13.732 4c-.77-.833-1.732-.833-2.464 0L4.268 16.5c-.77.833.192 2.5 1.732 2.5z" />
                                </svg>
                            </div>
                            <div class="ml-4">
                                <p class="text-sm font-medium text-gray-600">Deaths</p>
                                <p class="text-2xl font-semibold text-gray-900">{{ summary.deaths }}</p>
                            </div>
                        </div>
                    </div>

                    <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-6">
                        <div class="flex items-center">
                            <div class="p-2 bg-orange-100 rounded-lg">
                                <svg class="w-5 h-5 text-orange-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                                </svg>
                            </div>
                            <div class="ml-4">
                                <p class="text-sm font-medium text-gray-600">Active Outbreaks</p>
                                <p class="text-2xl font-semibold text-gray-900">{{ summary.active_outbreaks }}</p>
                            </div>
                        </div>
                    </div>

                    <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-6">
                        <div class="flex items-center">
                            <div class="p-2 bg-gray-100 rounded-lg">
                                <svg class="w-5 h-5 text-gray-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 12l3-3 3 3 4-4M8 21l4-4 4 4M3 4h18M4 4h16v12a1 1 0 01-1 1H5a1 1 0 01-1-1V4z" />
                                </svg>
                            </div>
                            <div class="ml-4">
                                <p class="text-sm font-medium text-gray-600">Fatality Rate</p>
                                <p class="text-2xl font-semibold text-gray-900">{{ summary.case_fatality_rate }}%</p>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Charts Grid -->
                <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">

                    <!-- Cases by Disease -->
                    <div class="bg-white rounded-lg shadow-sm border border-gray-200">
                        <div class="p-6">
                            <h3 class="text-lg font-medium text-gray-900 mb-4">Top Diseases</h3>
                            <div class="space-y-3">
                                <div v-for="item in casesByDisease" :key="item.disease" class="flex items-center">
                                    <div class="w-32 text-sm text-gray-600 truncate">{{ item.disease }}</div>
                                    <div class="flex-1 mx-3">
                                        <div class="bg-gray-100 rounded-full h-4 overflow-hidden">
                                            <div
                                                class="bg-blue-500 h-full flex items-center justify-end pr-2 text-xs text-white font-medium transition-all duration-300"
                                                :style="{ width: getPercentage(item.total, getMaxValue(casesByDisease)) + '%' }"
                                            >
                                                <span v-if="getPercentage(item.total, getMaxValue(casesByDisease)) > 15">{{ item.total }}</span>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="w-8 text-right text-sm text-gray-900 font-medium">{{ item.total }}</div>
                                </div>
                                <div v-if="casesByDisease.length === 0" class="text-center text-gray-500 py-8">
                                    <svg class="mx-auto h-8 w-8 text-gray-300 mb-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z" />
                                    </svg>
                                    No data available
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Cases by Municipality -->
                    <div class="bg-white rounded-lg shadow-sm border border-gray-200">
                        <div class="p-6">
                            <h3 class="text-lg font-medium text-gray-900 mb-4">Cases by Municipality</h3>
                            <div class="space-y-3">
                                <div v-for="item in casesByMunicipality" :key="item.municipality" class="flex items-center">
                                    <div class="w-32 text-sm text-gray-600 truncate">{{ item.municipality }}</div>
                                    <div class="flex-1 mx-3">
                                        <div class="bg-gray-100 rounded-full h-4 overflow-hidden">
                                            <div
                                                class="bg-green-500 h-full flex items-center justify-end pr-2 text-xs text-white font-medium transition-all duration-300"
                                                :style="{ width: getPercentage(item.total, getMaxValue(casesByMunicipality)) + '%' }"
                                            >
                                                <span v-if="getPercentage(item.total, getMaxValue(casesByMunicipality)) > 15">{{ item.total }}</span>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="w-8 text-right text-sm text-gray-900 font-medium">{{ item.total }}</div>
                                </div>
                                <div v-if="casesByMunicipality.length === 0" class="text-center text-gray-500 py-8">
                                    <svg class="mx-auto h-8 w-8 text-gray-300 mb-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z" />
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z" />
                                    </svg>
                                    No data available
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Cases by Classification -->
                    <div class="bg-white rounded-lg shadow-sm border border-gray-200">
                        <div class="p-6">
                            <h3 class="text-lg font-medium text-gray-900 mb-4">Case Classification</h3>
                            <div class="space-y-3">
                                <div v-for="item in casesByClassification" :key="item.classification" class="flex items-center">
                                    <div class="w-24 text-sm text-gray-600">{{ item.classification }}</div>
                                    <div class="flex-1 mx-3">
                                        <div class="bg-gray-100 rounded-full h-4 overflow-hidden">
                                            <div
                                                :class="[
                                                    'h-full flex items-center justify-end pr-2 text-xs text-white font-medium transition-all duration-300',
                                                    item.classification === 'Confirmed' ? 'bg-red-500' :
                                                    item.classification === 'Probable' ? 'bg-orange-500' : 'bg-yellow-500'
                                                ]"
                                                :style="{ width: getPercentage(item.total, getMaxValue(casesByClassification)) + '%' }"
                                            >
                                                <span v-if="getPercentage(item.total, getMaxValue(casesByClassification)) > 15">{{ item.total }}</span>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="w-8 text-right text-sm text-gray-900 font-medium">{{ item.total }}</div>
                                </div>
                                <div v-if="casesByClassification.length === 0" class="text-center text-gray-500 py-8">
                                    <svg class="mx-auto h-8 w-8 text-gray-300 mb-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v10a2 2 0 002 2h8a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2" />
                                    </svg>
                                    No data available
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Cases by Status -->
                    <div class="bg-white rounded-lg shadow-sm border border-gray-200">
                        <div class="p-6">
                            <h3 class="text-lg font-medium text-gray-900 mb-4">Report Status</h3>
                            <div class="space-y-3">
                                <div v-for="item in casesByStatus" :key="item.status" class="flex items-center">
                                    <div class="w-24 text-sm text-gray-600">{{ item.status }}</div>
                                    <div class="flex-1 mx-3">
                                        <div class="bg-gray-100 rounded-full h-4 overflow-hidden">
                                            <div
                                                class="bg-purple-500 h-full flex items-center justify-end pr-2 text-xs text-white font-medium transition-all duration-300"
                                                :style="{ width: getPercentage(item.total, getMaxValue(casesByStatus)) + '%' }"
                                            >
                                                <span v-if="getPercentage(item.total, getMaxValue(casesByStatus)) > 15">{{ item.total }}</span>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="w-8 text-right text-sm text-gray-900 font-medium">{{ item.total }}</div>
                                </div>
                                <div v-if="casesByStatus.length === 0" class="text-center text-gray-500 py-8">
                                    <svg class="mx-auto h-8 w-8 text-gray-300 mb-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4M7.835 4.697a3.42 3.42 0 001.946-.806 3.42 3.42 0 014.438 0 3.42 3.42 0 001.946.806 3.42 3.42 0 013.138 3.138 3.42 3.42 0 00.806 1.946 3.42 3.42 0 010 4.438 3.42 3.42 0 00-.806 1.946 3.42 3.42 0 01-3.138 3.138 3.42 3.42 0 00-1.946.806 3.42 3.42 0 01-4.438 0 3.42 3.42 0 00-1.946-.806 3.42 3.42 0 01-3.138-3.138 3.42 3.42 0 00-.806-1.946 3.42 3.42 0 010-4.438 3.42 3.42 0 00.806-1.946 3.42 3.42 0 013.138-3.138z" />
                                    </svg>
                                    No data available
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Additional Charts -->
                <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
                    <!-- Age Distribution -->
                    <div class="bg-white rounded-lg shadow-sm border border-gray-200">
                        <div class="p-6">
                            <h3 class="text-lg font-medium text-gray-900 mb-4">Age Distribution</h3>
                            <div class="space-y-3">
                                <div v-for="item in ageDistribution" :key="item.age_group" class="flex items-center">
                                    <div class="w-16 text-sm text-gray-600">{{ item.age_group }}</div>
                                    <div class="flex-1 mx-3">
                                        <div class="bg-gray-100 rounded-full h-4 overflow-hidden">
                                            <div
                                                class="bg-indigo-500 h-full flex items-center justify-end pr-2 text-xs text-white font-medium transition-all duration-300"
                                                :style="{ width: getPercentage(item.total, getMaxValue(ageDistribution)) + '%' }"
                                            >
                                                <span v-if="getPercentage(item.total, getMaxValue(ageDistribution)) > 15">{{ item.total }}</span>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="w-8 text-right text-sm text-gray-900 font-medium">{{ item.total }}</div>
                                </div>
                                <div v-if="ageDistribution.length === 0" class="text-center text-gray-500 py-8">
                                    <svg class="mx-auto h-8 w-8 text-gray-300 mb-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z" />
                                    </svg>
                                    No data available
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Gender Distribution -->
                    <div class="bg-white rounded-lg shadow-sm border border-gray-200">
                        <div class="p-6">
                            <h3 class="text-lg font-medium text-gray-900 mb-4">Gender Distribution</h3>
                            <div class="space-y-3">
                                <div v-for="item in genderDistribution" :key="item.sex" class="flex items-center">
                                    <div class="w-16 text-sm text-gray-600">{{ item.sex }}</div>
                                    <div class="flex-1 mx-3">
                                        <div class="bg-gray-100 rounded-full h-4 overflow-hidden">
                                            <div
                                                :class="[
                                                    'h-full flex items-center justify-end pr-2 text-xs text-white font-medium transition-all duration-300',
                                                    item.sex === 'Male' ? 'bg-blue-500' :
                                                    item.sex === 'Female' ? 'bg-pink-500' : 'bg-gray-500'
                                                ]"
                                                :style="{ width: getPercentage(item.total, getMaxValue(genderDistribution)) + '%' }"
                                            >
                                                <span v-if="getPercentage(item.total, getMaxValue(genderDistribution)) > 15">{{ item.total }}</span>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="w-8 text-right text-sm text-gray-900 font-medium">{{ item.total }}</div>
                                </div>
                                <div v-if="genderDistribution.length === 0" class="text-center text-gray-500 py-8">
                                    <svg class="mx-auto h-8 w-8 text-gray-300 mb-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z" />
                                    </svg>
                                    No data available
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Monthly Trend -->
                <div class="bg-white rounded-lg shadow-sm border border-gray-200">
                    <div class="p-6">
                        <h3 class="text-lg font-medium text-gray-900 mb-4">Monthly Trend (Last 12 Months)</h3>
                        <div class="flex items-end justify-between h-64 gap-2">
                            <div v-for="item in monthlyTrend" :key="item.month" class="flex-1 flex flex-col items-center">
                                <div class="text-xs text-gray-600 mb-1">{{ item.total }}</div>
                                <div
                                    class="w-full bg-gradient-to-t from-blue-500 to-blue-400 rounded-t transition-all hover:from-blue-600 hover:to-blue-500"
                                    :style="{ height: Math.max((item.total / getMaxValue(monthlyTrend) * 200), 4) + 'px' }"
                                ></div>
                                <div class="text-xs text-gray-600 mt-2 transform rotate-45 origin-left whitespace-nowrap">{{ item.month }}</div>
                            </div>
                        </div>
                        <div v-if="monthlyTrend.length === 0" class="text-center text-gray-500 py-16">
                            <svg class="mx-auto h-12 w-12 text-gray-300 mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z" />
                            </svg>
                            No data available for monthly trend
                        </div>
                    </div>
                </div>

            </div>
        </div>
    </AuthenticatedLayout>
</template>
