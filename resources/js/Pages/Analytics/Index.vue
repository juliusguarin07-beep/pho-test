<template>
  <Head title="Epidemiologic Surveillance Analytics" />
  <AuthenticatedLayout>
    <template #header>
      <div class="flex items-center justify-between">
        <h2 class="text-xl font-semibold leading-tight text-gray-800">
          Epidemiologic Surveillance Analytics Dashboard
        </h2>
        <div class="flex items-center space-x-4">
          <!-- Automatic Alerts Button for PESU Admin -->
          <Link
            v-if="isPESUAdmin"
            :href="route('automatic-alerts.index')"
            class="inline-flex items-center px-4 py-2 bg-red-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-red-700 focus:bg-red-700 active:bg-red-900 focus:outline-none focus:ring-2 focus:ring-red-500 focus:ring-offset-2 transition ease-in-out duration-150"
          >
            <svg class="w-4 h-4 mr-2" fill="currentColor" viewBox="0 0 20 20">
              <path fill-rule="evenodd" d="M8.257 3.099c.765-1.36 2.722-1.36 3.487 0l5.58 9.92c.75 1.334-.213 2.98-1.742 2.98H4.42c-1.53 0-2.493-1.646-1.743-2.98l5.58-9.92zM11 13a1 1 0 11-2 0 1 1 0 012 0zm-1-8a1 1 0 00-1 1v3a1 1 0 002 0V6a1 1 0 00-1-1z" clip-rule="evenodd" />
            </svg>
            Automatic Alerts
          </Link>
        </div>
      </div>
    </template>

    <div class="py-12">
      <div class="mx-auto space-y-8 max-w-7xl sm:px-6 lg:px-8">

        <!-- Summary Cards -->
        <div class="grid grid-cols-1 gap-5 sm:grid-cols-2 lg:grid-cols-4">
          <div class="p-6 bg-white shadow sm:rounded-lg">
            <dt class="text-sm font-medium text-gray-500 truncate">Total Cases</dt>
            <dd class="mt-1 text-3xl font-semibold text-gray-900">{{ summary?.total_cases || 0 }}</dd>
          </div>
          <div class="p-6 bg-white shadow sm:rounded-lg">
            <dt class="text-sm font-medium text-gray-500 truncate">Confirmed</dt>
            <dd class="mt-1 text-3xl font-semibold text-green-900">{{ summary?.confirmed_cases || 0 }}</dd>
          </div>
          <div class="p-6 bg-white shadow sm:rounded-lg">
            <dt class="text-sm font-medium text-gray-500 truncate">Deaths</dt>
            <dd class="mt-1 text-3xl font-semibold text-red-900">{{ summary?.deaths || 0 }}</dd>
          </div>
          <div class="p-6 bg-white shadow sm:rounded-lg">
            <dt class="text-sm font-medium text-gray-500 truncate">Outbreaks</dt>
            <dd class="mt-1 text-3xl font-semibold text-orange-900">{{ summary?.active_outbreaks || 0 }}</dd>
          </div>
        </div>

        <!-- Navigation Tabs -->
        <div class="bg-white shadow sm:rounded-lg">
          <div class="border-b border-gray-200">
            <nav class="flex px-6 -mb-px space-x-8">
                            <button
                @click="activeTab = 'surveillance'"
                :class="[
                  'py-4 px-1 border-b-2 font-medium text-sm whitespace-nowrap transition-colors duration-200',
                  activeTab === 'surveillance'
                    ? 'border-blue-500 text-blue-600'
                    : 'border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300'
                ]"
              >
                Epidemiologic Surveillance Analytics
              </button>
              <button
                @click="activeTab = 'demographic'"
                :class="[
                  'py-4 px-1 border-b-2 font-medium text-sm whitespace-nowrap transition-colors duration-200',
                  activeTab === 'demographic'
                    ? 'border-blue-500 text-blue-600'
                    : 'border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300'
                ]"
              >
                Demographic Analytics
              </button>
              <button
                @click="activeTab = 'municipality'"
                :class="[
                  'py-4 px-1 border-b-2 font-medium text-sm whitespace-nowrap transition-colors duration-200',
                  activeTab === 'municipality'
                    ? 'border-blue-500 text-blue-600'
                    : 'border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300'
                ]"
              >
                Municipality Analytics
              </button>
            </nav>
          </div>
        </div>

        <!-- Tab Content -->

        <!-- 1. Epidemiologic Surveillance Analytics -->
        <div v-show="activeTab === 'surveillance'" class="bg-white shadow sm:rounded-lg">
          <div class="p-6 border-b border-gray-200">
            <h3 class="text-lg font-medium text-gray-900">Epidemiologic Surveillance Analytics</h3>
            <p class="mt-1 text-sm text-gray-600">Tracking disease trends and detecting outbreaks early</p>
          </div>

          <!-- Weekly Case Trend Analysis -->
          <div class="p-6 border-b border-gray-100">
            <h4 class="mb-4 font-medium text-gray-800 text-md">Weekly Case Trend Analysis</h4>
            <p class="mb-4 text-sm text-gray-600">Monitors weekly number of cases per disease and area</p>
            <div class="h-96">
              <canvas id="weeklyTrendChart"></canvas>
            </div>
          </div>

          <!-- Threshold Analysis (Early Warning) -->
          <div class="p-6 border-b border-gray-100">
            <h4 class="mb-4 font-medium text-gray-800 text-md">Threshold Analysis (Early Warning)</h4>
            <p class="mb-4 text-sm text-gray-600">Compares current cases with 5-year mean + 2SD</p>
            <div class="grid grid-cols-1 gap-4 md:grid-cols-2 lg:grid-cols-3">
              <div
                v-for="alert in thresholdAnalysis"
                :key="alert.disease"
                :class="['rounded-lg border-2 p-6 shadow-lg', getAlertColor(alert.alert_level)]"
              >
                <div class="space-y-4">
                  <!-- Header with Disease and Alert Level -->
                  <div class="flex items-start justify-between">
                    <div>
                      <h5 class="text-lg font-bold">{{ getAlertIcon(alert.alert_level) }} {{ alert.disease }}</h5>
                      <div class="mt-1 text-2xl font-extrabold">{{ alert.alert_level.toUpperCase() }}</div>
                    </div>
                  </div>

                  <!-- Current Cases -->
                  <div class="bg-white bg-opacity-20 rounded-lg p-3">
                    <div class="text-sm text-gray-700 font-medium">Current Cases</div>
                    <div class="text-3xl font-bold">{{ alert.current_cases }}</div>
                  </div>

                  <!-- Threshold Information - Made Prominent -->
                  <div class="bg-white bg-opacity-30 rounded-lg p-4 border border-white border-opacity-50">
                    <div class="text-center">
                      <div class="text-sm font-semibold text-gray-800 uppercase tracking-wide">Threshold</div>
                      <div class="text-2xl font-extrabold text-gray-900 mt-1">{{ alert.threshold }}</div>
                      <div class="text-sm text-gray-700 mt-2">
                        {{ alert.percentage_of_threshold.toFixed(1) }}% of threshold reached
                      </div>
                      <div class="w-full bg-gray-200 rounded-full h-2 mt-3">
                        <div
                          class="h-2 rounded-full transition-all duration-300"
                          :class="alert.percentage_of_threshold > 100 ? 'bg-red-500' : alert.percentage_of_threshold > 75 ? 'bg-yellow-500' : 'bg-green-500'"
                          :style="{ width: Math.min(alert.percentage_of_threshold, 100) + '%' }"
                        ></div>
                      </div>
                    </div>
                  </div>
                </div>
              </div>
            </div>
          </div>

          <!-- Epidemic Curve Generation -->
          <div class="p-6 border-b border-gray-100">
            <h4 class="mb-4 font-medium text-gray-800 text-md">Epidemic Curve Generation</h4>
            <p class="mb-4 text-sm text-gray-600">Visualizes onset of cases over time</p>
            <div class="h-96">
              <canvas id="epidemicCurveChart"></canvas>
            </div>
          </div>

          <!-- Geospatial Mapping -->
          <div class="p-6 border-b border-gray-100">
            <h4 class="mb-4 font-medium text-gray-800 text-md">Geospatial Mapping</h4>
            <p class="mb-4 text-sm text-gray-600">Identifies hotspots or clusters</p>
            <div class="grid grid-cols-1 gap-4 md:grid-cols-2 lg:grid-cols-3">
              <div
                v-for="location in geospatialData"
                :key="location.barangay"
                class="p-4 border border-gray-200 rounded-lg"
              >
                <div class="flex items-center justify-between">
                  <div>
                    <h5 class="font-medium">{{ location.barangay }}</h5>
                    <p class="text-sm text-gray-600">{{ location.municipality }}</p>
                    <p class="text-xs text-gray-500">{{ location.diseases.join(', ') }}</p>
                  </div>
                  <div class="text-right">
                    <div class="text-lg font-bold">{{ location.case_count }}</div>
                    <div :class="['rounded-full px-2 py-1 text-xs text-white', getRiskColor(location.risk_level)]">
                      {{ location.risk_level.toUpperCase() }}
                    </div>
                  </div>
                </div>
              </div>
            </div>
          </div>

          <!-- Disease Ranking -->
          <div class="p-6">
            <h4 class="mb-4 font-medium text-gray-800 text-md">Disease Ranking</h4>
            <p class="mb-4 text-sm text-gray-600">Ranks diseases by number of reported cases</p>
            <div class="grid grid-cols-1 gap-6 lg:grid-cols-2">
              <div class="h-96">
                <canvas id="diseaseRankingChart"></canvas>
              </div>
              <div class="space-y-3">
                <div
                  v-for="disease in diseaseRanking"
                  :key="disease.disease"
                  class="flex items-center justify-between p-3 border border-gray-200 rounded-lg"
                >
                  <div class="flex items-center space-x-3">
                    <div class="flex items-center justify-center w-8 h-8 text-sm font-medium text-blue-800 bg-blue-100 rounded-full">
                      {{ disease.rank }}
                    </div>
                    <div>
                      <h5 class="font-medium">{{ disease.disease }}</h5>
                    </div>
                  </div>
                  <div class="text-lg font-bold text-gray-900">{{ disease.total }}</div>
                </div>
              </div>
            </div>
          </div>
        </div>

        <!-- 2. Demographic Analytics -->
        <div v-show="activeTab === 'demographic'" class="bg-white shadow sm:rounded-lg">
          <div class="p-6 border-b border-gray-200">
            <h3 class="text-lg font-medium text-gray-900">Demographic Analytics</h3>
            <p class="mt-1 text-sm text-gray-600">Understanding who is affected</p>
          </div>

          <!-- Age Distribution -->
          <div class="p-6 border-b border-gray-100">
            <h4 class="mb-4 font-medium text-gray-800 text-md">Age Distribution</h4>
            <p class="mb-4 text-sm text-gray-600">Breaks down cases by age groups</p>
            <div class="h-96">
              <canvas id="ageDistributionChart"></canvas>
            </div>
          </div>

          <!-- Sex Distribution -->
          <div class="p-6 border-b border-gray-100">
            <h4 class="mb-4 font-medium text-gray-800 text-md">Sex Distribution</h4>
            <p class="mb-4 text-sm text-gray-600">Male vs. Female cases</p>
            <div class="grid grid-cols-1 gap-6 lg:grid-cols-2">
              <div class="h-96">
                <canvas id="sexDistributionChart"></canvas>
              </div>
              <div class="space-y-4">
                <div
                  v-for="sex in sexDistribution"
                  :key="sex.sex"
                  class="p-4 border border-gray-200 rounded-lg"
                >
                  <div class="flex items-center justify-between">
                    <h5 class="font-medium">{{ sex.sex.charAt(0).toUpperCase() + sex.sex.slice(1) }}</h5>
                    <div class="text-2xl font-bold">{{ sex.total }}</div>
                  </div>
                  <div class="h-2 mt-2 bg-gray-200 rounded-full">
                    <div
                      :class="['h-2 rounded-full', sex.sex === 'Male' ? 'bg-blue-500' : 'bg-pink-500']"
                      :style="{ width: `${(sex.total / summary.total_cases) * 100}%` }"
                    ></div>
                  </div>
                </div>
              </div>
            </div>
          </div>

          <!-- High-Risk Group Identification -->
          <div class="p-6">
            <h4 class="mb-4 font-medium text-gray-800 text-md">High-Risk Group Identification</h4>
            <p class="mb-4 text-sm text-gray-600">Detects groups most affected</p>
            <div class="grid grid-cols-1 gap-4 md:grid-cols-2 lg:grid-cols-3">
              <div
                v-for="group in highRiskGroups"
                :key="group.group"
                class="p-4 border border-orange-200 rounded-lg bg-orange-50"
              >
                <div class="mb-3">
                  <h5 class="font-medium text-orange-900">⚠️ {{ group.group }}</h5>
                  <p class="text-lg font-bold text-orange-800">{{ group.total_cases }} cases</p>
                </div>
                <div class="space-y-1">
                  <div
                    v-for="disease in group.diseases.slice(0, 3)"
                    :key="disease.disease"
                    class="flex justify-between text-sm"
                  >
                    <span class="text-orange-700">{{ disease.disease }}</span>
                    <span class="font-medium text-orange-900">{{ disease.cases }}</span>
                  </div>
                </div>
              </div>
            </div>
          </div>
        </div>

        <!-- 3. Municipality Analytics -->
        <div v-show="activeTab === 'municipality'" class="bg-white shadow sm:rounded-lg">
          <div class="p-6 border-b border-gray-200">
            <h3 class="text-lg font-medium text-gray-900">Municipality Analytics</h3>
            <p class="mt-1 text-sm text-gray-600">Provides an overview of all reported cases per municipality</p>
          </div>

          <!-- Total Cases by Municipality -->
          <div class="p-6 border-b border-gray-100">
            <h4 class="mb-4 font-medium text-gray-800 text-md">Total Cases by Municipality</h4>
            <p class="mb-4 text-sm text-gray-600">Aggregates total reported cases</p>
            <div class="h-96">
              <canvas id="municipalityCasesChart"></canvas>
            </div>
          </div>

          <!-- New Cases (Week-on-Week) -->
          <div class="p-6 border-b border-gray-100">
            <h4 class="mb-4 font-medium text-gray-800 text-md">New Cases (Week-on-Week)</h4>
            <p class="mb-4 text-sm text-gray-600">Shows weekly increase or decrease</p>
            <div class="grid grid-cols-1 gap-4 md:grid-cols-2 lg:grid-cols-3">
              <div
                v-for="change in weekOnWeekChanges"
                :key="change.municipality"
                :class="[
                  'rounded-lg border p-4',
                  change.trend === 'up' ? 'border-red-200 bg-red-50' :
                  change.trend === 'down' ? 'border-green-200 bg-green-50' :
                  'border-gray-200 bg-gray-50'
                ]"
              >
                <div class="flex items-center justify-between">
                  <div>
                    <h5 class="font-medium">{{ getTrendIcon(change.trend) }} {{ change.municipality }}</h5>
                    <p class="text-sm">Current: {{ change.current_week }} | Previous: {{ change.previous_week }}</p>
                  </div>
                  <div class="text-right">
                    <div :class="[
                      'text-lg font-bold',
                      change.percentage_change > 0 ? 'text-red-600' :
                      change.percentage_change < 0 ? 'text-green-600' : 'text-gray-600'
                    ]">
                      {{ change.percentage_change > 0 ? '+' : '' }}{{ change.percentage_change.toFixed(1) }}%
                    </div>
                  </div>
                </div>
              </div>
            </div>
          </div>

          <!-- Top Diseases per Municipality -->
          <div class="p-6">
            <h4 class="mb-4 font-medium text-gray-800 text-md">Top Diseases per Municipality</h4>
            <p class="mb-4 text-sm text-gray-600">Lists most common diseases</p>
            <div class="grid grid-cols-1 gap-6 md:grid-cols-2 lg:grid-cols-3">
              <div
                v-for="municipality in topDiseasesByMunicipality"
                :key="municipality.municipality"
                class="p-4 border border-gray-200 rounded-lg"
              >
                <h5 class="mb-3 font-medium text-gray-900">{{ municipality.municipality }}</h5>
                <div class="space-y-2">
                  <div
                    v-for="(disease, index) in municipality.diseases.slice(0, 5)"
                    :key="disease.disease"
                    class="flex items-center justify-between"
                  >
                    <div class="flex items-center space-x-2">
                      <div class="flex items-center justify-center w-6 h-6 text-xs font-medium text-blue-800 bg-blue-100 rounded-full">
                        {{ index + 1 }}
                      </div>
                      <span class="text-sm">{{ disease.disease }}</span>
                    </div>
                    <span class="font-medium">{{ disease.cases }}</span>
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

<script setup lang="ts">
import AuthenticatedLayout from "@/Layouts/AuthenticatedLayout.vue";
import { Head, Link, usePage } from "@inertiajs/vue3";
import { ref, onMounted, nextTick, watch, computed } from "vue";
import { Chart, registerables } from "chart.js";

Chart.register(...registerables);

// Get user roles for role-based UI
const page = usePage();
const auth = computed(() => page.props.auth as any);
const user = computed(() => auth.value.user);
const userRoles = computed(() => user.value?.roles || []);
const isPESUAdmin = computed(() => userRoles.value.includes('pesu_admin'));

interface Summary {
    total_cases: number;
    confirmed_cases: number;
    deaths: number;
    active_outbreaks: number;
    case_fatality_rate: number;
}

interface WeeklyTrend {
    week: string;
    total: number;
    diseases: Array<{ disease: string; total: number }>;
}

interface ThresholdAnalysis {
    disease: string;
    current_cases: number;
    threshold: number;
    alert_level: string;
    percentage_of_threshold: number;
}

interface EpidemicCurve {
    date: string;
    cases: number;
}

interface GeospatialData {
    municipality: string;
    barangay: string;
    case_count: number;
    diseases: string[];
    risk_level: string;
}

interface DiseaseRanking {
    rank: number;
    disease: string;
    total: number;
}

interface AgeDistribution {
    age_group: string;
    male: number;
    female: number;
    total: number;
}

interface SexDistribution {
    sex: string;
    total: number;
}

interface HighRiskGroup {
    group: string;
    total_cases: number;
    diseases: Array<{ disease: string; cases: number }>;
}

interface MunicipalityCase {
    municipality: string;
    total_cases: number;
    disease_count: number;
}

interface WeekOnWeekChange {
    municipality: string;
    current_week: number;
    previous_week: number;
    percentage_change: number;
    trend: string;
}

interface TopDiseasesByMunicipality {
    municipality: string;
    diseases: Array<{ disease: string; cases: number }>;
}

interface Props {
    summary: Summary;
    weeklyTrend?: WeeklyTrend[];
    thresholdAnalysis?: ThresholdAnalysis[];
    epidemicCurve?: EpidemicCurve[];
    geospatialData?: GeospatialData[];
    diseaseRanking?: DiseaseRanking[];
    ageDistribution?: AgeDistribution[];
    sexDistribution?: SexDistribution[];
    highRiskGroups?: HighRiskGroup[];
    municipalityCases?: MunicipalityCase[];
    weekOnWeekChanges?: WeekOnWeekChange[];
    topDiseasesByMunicipality?: TopDiseasesByMunicipality[];
    [key: string]: any;
}

const props = defineProps<Props>();
const {
    summary,
    weeklyTrend = [],
    thresholdAnalysis = [],
    epidemicCurve = [],
    geospatialData = [],
    diseaseRanking = [],
    ageDistribution = [],
    sexDistribution = [],
    highRiskGroups = [],
    municipalityCases = [],
    weekOnWeekChanges = [],
    topDiseasesByMunicipality = []
} = props;

const chartInstances = ref<{ [key: string]: Chart }>({});
const activeTab = ref('surveillance');

const destroyCharts = () => {
    Object.values(chartInstances.value).forEach(chart => {
        if (chart) chart.destroy();
    });
    chartInstances.value = {};
};

const getAlertColor = (level: string) => {
    const colors = {
        red: 'bg-red-100 text-red-800 border-red-200',
        orange: 'bg-orange-100 text-orange-800 border-orange-200',
        yellow: 'bg-yellow-100 text-yellow-800 border-yellow-200'
    };
    return colors[level as keyof typeof colors] || 'bg-green-100 text-green-800 border-green-200';
};

const getAlertIcon = (level: string) => {
    const icons = { red: '🔴', orange: '🟠', yellow: '🟡' };
    return icons[level as keyof typeof icons] || '🟢';
};

const getRiskColor = (level: string) => {
    const colors = { high: 'bg-red-500', medium: 'bg-orange-500' };
    return colors[level as keyof typeof colors] || 'bg-yellow-500';
};

const getTrendIcon = (trend: string) => {
    const icons = { up: '↗', down: '↘' };
    return icons[trend as keyof typeof icons] || '→';
};

const initializeCharts = async () => {
    await nextTick();
    destroyCharts();

    // Weekly Case Trend Analysis
    const weeklyTrendCtx = document.getElementById("weeklyTrendChart") as HTMLCanvasElement;
    if (weeklyTrendCtx && weeklyTrend.length) {
        chartInstances.value.weeklyTrend = new Chart(weeklyTrendCtx, {
            type: "line",
            data: {
                labels: weeklyTrend.map(item => item.week),
                datasets: [{
                    label: "Total Cases",
                    data: weeklyTrend.map(item => item.total),
                    borderColor: "rgb(59, 130, 246)",
                    backgroundColor: "rgba(59, 130, 246, 0.1)",
                    tension: 0.4,
                    fill: true
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: {
                    title: { display: true, text: 'Weekly Case Trends' }
                }
            }
        });
    }

    // Epidemic Curve Generation
    const epidemicCurveCtx = document.getElementById("epidemicCurveChart") as HTMLCanvasElement;
    if (epidemicCurveCtx && epidemicCurve.length) {
        chartInstances.value.epidemicCurve = new Chart(epidemicCurveCtx, {
            type: "bar",
            data: {
                labels: epidemicCurve.map(item => item.date),
                datasets: [{
                    label: "Cases by Onset Date",
                    data: epidemicCurve.map(item => item.cases),
                    backgroundColor: "rgba(34, 197, 94, 0.8)",
                    borderColor: "rgb(34, 197, 94)",
                    borderWidth: 1
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: {
                    title: { display: true, text: 'Epidemic Curve' }
                }
            }
        });
    }

    // Disease Ranking
    const diseaseRankingCtx = document.getElementById("diseaseRankingChart") as HTMLCanvasElement;
    if (diseaseRankingCtx && diseaseRanking.length) {
        chartInstances.value.diseaseRanking = new Chart(diseaseRankingCtx, {
            type: "doughnut",
            data: {
                labels: diseaseRanking.map(item => item.disease),
                datasets: [{
                    data: diseaseRanking.map(item => item.total),
                    backgroundColor: [
                        'rgba(239, 68, 68, 0.8)',
                        'rgba(245, 158, 11, 0.8)',
                        'rgba(34, 197, 94, 0.8)',
                        'rgba(59, 130, 246, 0.8)',
                        'rgba(147, 51, 234, 0.8)'
                    ],
                    borderWidth: 2
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: {
                    title: { display: true, text: 'Top 5 Diseases' }
                }
            }
        });
    }

    // Age Distribution (Population Pyramid)
    const ageDistributionCtx = document.getElementById("ageDistributionChart") as HTMLCanvasElement;
    if (ageDistributionCtx && ageDistribution.length) {
        chartInstances.value.ageDistribution = new Chart(ageDistributionCtx, {
            type: "bar",
            data: {
                labels: ageDistribution.map(item => item.age_group),
                datasets: [
                    {
                        label: "Male",
                        data: ageDistribution.map(item => -item.male),
                        backgroundColor: "rgba(59, 130, 246, 0.8)",
                        borderColor: "rgb(59, 130, 246)",
                        borderWidth: 1
                    },
                    {
                        label: "Female",
                        data: ageDistribution.map(item => item.female),
                        backgroundColor: "rgba(236, 72, 153, 0.8)",
                        borderColor: "rgb(236, 72, 153)",
                        borderWidth: 1
                    }
                ]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                indexAxis: 'y',
                plugins: {
                    title: { display: true, text: 'Age-Sex Distribution (Population Pyramid)' }
                },
                scales: {
                    x: {
                        stacked: false,
                        ticks: {
                            callback: function(value: any) { return Math.abs(value); }
                        }
                    },
                    y: { stacked: false }
                }
            }
        });
    }

    // Sex Distribution
    const sexDistributionCtx = document.getElementById("sexDistributionChart") as HTMLCanvasElement;
    if (sexDistributionCtx && sexDistribution.length) {
        // Create colors array based on sex values
        const colors = sexDistribution.map(item => {
            if (item.sex === 'Male') return 'rgba(59, 130, 246, 0.8)'  // Blue for Male
            if (item.sex === 'Female') return 'rgba(236, 72, 153, 0.8)'  // Pink for Female
            return 'rgba(156, 163, 175, 0.8)'  // Gray for Other
        })

        chartInstances.value.sexDistribution = new Chart(sexDistributionCtx, {
            type: "pie",
            data: {
                labels: sexDistribution.map(item => item.sex),
                datasets: [{
                    data: sexDistribution.map(item => item.total),
                    backgroundColor: colors,
                    borderWidth: 2
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: {
                    title: { display: true, text: 'Sex Distribution' }
                }
            }
        });
    }

    // Municipality Cases
    const municipalityCasesCtx = document.getElementById("municipalityCasesChart") as HTMLCanvasElement;
    if (municipalityCasesCtx && municipalityCases.length) {
        chartInstances.value.municipalityCases = new Chart(municipalityCasesCtx, {
            type: "bar",
            data: {
                labels: municipalityCases.map(item => item.municipality),
                datasets: [{
                    label: "Total Cases",
                    data: municipalityCases.map(item => item.total_cases),
                    backgroundColor: "rgba(168, 85, 247, 0.8)",
                    borderColor: "rgb(168, 85, 247)",
                    borderWidth: 1
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: {
                    title: { display: true, text: 'Total Cases by Municipality' }
                }
            }
        });
    }
};

onMounted(() => {
    initializeCharts();
});

// Watch for tab changes and reinitialize charts
watch(activeTab, async () => {
    await nextTick();
    initializeCharts();
});
</script>
