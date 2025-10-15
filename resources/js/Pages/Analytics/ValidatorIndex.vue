<template>
  <Head title="Analytics Dashboard" />
  <AuthenticatedLayout>
    <template #header>
      <h2 class="text-xl font-semibold leading-tight text-gray-800">
        Analytics Dashboard
        <span v-if="facility" class="text-sm text-gray-600 font-normal ml-2">
          - {{ facility.name }}
        </span>
      </h2>
    </template>

    <div class="py-12">
      <div class="mx-auto space-y-8 max-w-7xl sm:px-6 lg:px-8">

        <!-- Date Filter -->
        <div class="bg-white shadow sm:rounded-lg">
          <div class="p-6">
            <h3 class="text-lg font-medium text-gray-900 mb-4">Date Range Filter</h3>
            <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
              <div>
                <label class="block text-sm font-medium text-gray-700">Start Date</label>
                <input
                  type="date"
                  v-model="startDate"
                  class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500"
                >
              </div>
              <div>
                <label class="block text-sm font-medium text-gray-700">End Date</label>
                <input
                  type="date"
                  v-model="endDate"
                  class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500"
                >
              </div>
              <div class="flex items-end">
                <button
                  @click="applyFilter"
                  class="px-4 py-2 bg-blue-600 text-white rounded-md hover:bg-blue-700 focus:ring-2 focus:ring-blue-500"
                >
                  Apply Filter
                </button>
              </div>
            </div>
          </div>
        </div>

        <!-- Facility Summary Cards -->
        <div class="grid grid-cols-1 gap-5 sm:grid-cols-2 lg:grid-cols-4">
          <div class="p-6 bg-white shadow sm:rounded-lg">
            <dt class="text-sm font-medium text-gray-500 truncate">Total Cases (Facility)</dt>
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
            <dt class="text-sm font-medium text-gray-500 truncate">CFR</dt>
            <dd class="mt-1 text-3xl font-semibold text-purple-900">{{ (summary?.case_fatality_rate || 0).toFixed(2) }}%</dd>
          </div>
        </div>

        <!-- 1. DEMOGRAPHICS -->
        <div class="bg-white shadow sm:rounded-lg">
          <div class="p-6 border-b border-gray-200">
            <h3 class="text-lg font-medium text-gray-900">Demographics</h3>
            <p class="mt-1 text-sm text-gray-600">Age and sex distribution of cases</p>
          </div>

          <div class="p-6">
            <div class="grid grid-cols-1 lg:grid-cols-2 gap-8">

              <!-- Age and Sex Distribution Table -->
              <div>
                <h4 class="text-md font-medium text-gray-800 mb-4">Age and Sex Distribution</h4>
                <div class="overflow-hidden">
                  <table class="min-w-full divide-y divide-gray-200">
                    <thead class="bg-gray-50">
                      <tr>
                        <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Age Group</th>
                        <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Male</th>
                        <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Female</th>
                        <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Total</th>
                      </tr>
                    </thead>
                    <tbody class="bg-white divide-y divide-gray-200">
                      <tr v-for="group in ageGroups" :key="group.age_group">
                        <td class="px-4 py-3 text-sm font-medium text-gray-900">{{ group.age_group }}</td>
                        <td class="px-4 py-3 text-sm text-blue-600">{{ group.male }}</td>
                        <td class="px-4 py-3 text-sm text-pink-600">{{ group.female }}</td>
                        <td class="px-4 py-3 text-sm font-semibold text-gray-900">{{ group.total }}</td>
                      </tr>
                    </tbody>
                  </table>
                </div>
              </div>

              <!-- Sex Distribution Chart -->
              <div>
                <h4 class="text-md font-medium text-gray-800 mb-4">Sex Distribution</h4>
                <div class="h-64">
                  <canvas ref="sexChart"></canvas>
                </div>
              </div>

            </div>
          </div>
        </div>

        <!-- 2. TOTAL CASES IN MUNICIPALITY -->
        <div class="bg-white shadow sm:rounded-lg">
          <div class="p-6 border-b border-gray-200">
            <h3 class="text-lg font-medium text-gray-900">Total Cases in Municipality</h3>
            <p class="mt-1 text-sm text-gray-600">{{ municipalityCases?.municipality_name || 'Unknown Municipality' }} - Overview</p>
          </div>

          <div class="p-6">
            <div class="grid grid-cols-1 lg:grid-cols-2 gap-8">

              <!-- Municipality Statistics -->
              <div>
                <div class="grid grid-cols-1 gap-4">
                  <div class="bg-blue-50 p-4 rounded-lg">
                    <div class="text-2xl font-bold text-blue-900">{{ municipalityCases?.total_cases || 0 }}</div>
                    <div class="text-sm text-blue-700">Total Cases in Municipality</div>
                  </div>
                  <div class="bg-green-50 p-4 rounded-lg">
                    <div class="text-2xl font-bold text-green-900">{{ municipalityCases?.confirmed_cases || 0 }}</div>
                    <div class="text-sm text-green-700">Confirmed Cases</div>
                  </div>
                  <div class="bg-red-50 p-4 rounded-lg">
                    <div class="text-2xl font-bold text-red-900">{{ municipalityCases?.deaths || 0 }}</div>
                    <div class="text-sm text-red-700">Deaths</div>
                  </div>
                </div>
              </div>

              <!-- Disease Breakdown in Municipality -->
              <div>
                <h4 class="text-md font-medium text-gray-800 mb-4">Disease Breakdown</h4>
                <div class="space-y-3">
                  <div v-for="disease in municipalityDiseases" :key="disease.disease" class="flex justify-between items-center py-2 border-b">
                    <span class="text-sm font-medium text-gray-700">{{ disease.disease }}</span>
                    <span class="text-lg font-semibold text-blue-600">{{ disease.total }}</span>
                  </div>
                  <div v-if="municipalityDiseases.length === 0" class="text-sm text-gray-500 italic">
                    No cases found for the selected period
                  </div>
                </div>
              </div>

            </div>
          </div>
        </div>

        <!-- 3. COMPARISON OF CASES PER BARANGAY -->
        <div class="bg-white shadow sm:rounded-lg">
          <div class="p-6 border-b border-gray-200">
            <h3 class="text-lg font-medium text-gray-900">Comparison of Cases per Barangay</h3>
            <p class="mt-1 text-sm text-gray-600">Cases distribution across barangays in {{ municipalityCases?.municipality_name || 'the municipality' }}</p>
          </div>

          <div class="p-6">
            <div class="grid grid-cols-1 lg:grid-cols-2 gap-8">

              <!-- Barangay Cases Chart -->
              <div>
                <h4 class="text-md font-medium text-gray-800 mb-4">Cases per Barangay</h4>
                <div class="h-64">
                  <canvas ref="barangayChart"></canvas>
                </div>
              </div>

              <!-- Barangay List with Counts -->
              <div>
                <h4 class="text-md font-medium text-gray-800 mb-4">Barangay Rankings</h4>
                <div class="space-y-2 max-h-64 overflow-y-auto">
                  <div v-for="(barangay, index) in barangayComparison" :key="barangay.barangay"
                       class="flex justify-between items-center py-2 px-3 rounded"
                       :class="index < 3 ? 'bg-red-50' : 'bg-gray-50'">
                    <div class="flex items-center space-x-3">
                      <span class="text-sm font-medium text-gray-600">#{{ index + 1 }}</span>
                      <span class="text-sm font-medium text-gray-900">{{ barangay.barangay }}</span>
                    </div>
                    <span class="text-lg font-semibold"
                          :class="index < 3 ? 'text-red-600' : 'text-orange-600'">
                      {{ barangay.total_cases }}
                    </span>
                  </div>
                  <div v-if="barangayComparison.length === 0" class="text-sm text-gray-500 italic py-4">
                    No barangay data available for the selected period
                  </div>
                </div>
              </div>

            </div>

            <!-- Detailed Barangay Disease Breakdown -->
            <div v-if="barangayDiseaseBreakdown.length > 0" class="mt-8">
              <h4 class="text-md font-medium text-gray-800 mb-4">Disease Breakdown by Top Barangays</h4>
              <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4">
                <div v-for="barangay in barangayDiseaseBreakdown.slice(0, 6)" :key="barangay.barangay"
                     class="bg-gray-50 p-4 rounded-lg">
                  <h5 class="font-semibold text-gray-900 mb-2">{{ barangay.barangay }}</h5>
                  <div class="text-lg font-bold text-blue-600 mb-2">{{ barangay.total_cases }} cases</div>
                  <div class="space-y-1">
                    <div v-for="disease in barangay.diseases.slice(0, 3)" :key="disease.disease"
                         class="flex justify-between text-sm">
                      <span class="text-gray-700">{{ disease.disease }}</span>
                      <span class="font-medium">{{ disease.cases }}</span>
                    </div>
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

<script setup>
import { ref, onMounted, nextTick } from 'vue'
import { Head } from '@inertiajs/vue3'
import { router } from '@inertiajs/vue3'
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue'
import Chart from 'chart.js/auto'

// Props
const props = defineProps({
  summary: Object,
  ageGroups: Array,
  sexDistribution: Array,
  municipalityCases: Object,
  municipalityDiseases: Array,
  barangayComparison: Array,
  barangayDiseaseBreakdown: Array,
  filters: Object,
  facility: Object,
})

// Reactive data
const startDate = ref(props.filters?.start_date || '')
const endDate = ref(props.filters?.end_date || '')
const sexChart = ref(null)
const barangayChart = ref(null)

// Methods
const applyFilter = () => {
  router.get('/analytics/validator', {
    start_date: startDate.value,
    end_date: endDate.value,
  }, {
    preserveState: true,
    preserveScroll: true,
  })
}

const initializeCharts = async () => {
  await nextTick()

  // Sex Distribution Pie Chart
  if (sexChart.value && props.sexDistribution) {
    new Chart(sexChart.value, {
      type: 'doughnut',
      data: {
        labels: props.sexDistribution.map(item => item.sex),
        datasets: [{
          data: props.sexDistribution.map(item => item.total),
          backgroundColor: [
            'rgba(59, 130, 246, 0.8)',  // Blue for Male
            'rgba(236, 72, 153, 0.8)',  // Pink for Female
            'rgba(156, 163, 175, 0.8)'  // Gray for Other
          ],
          borderColor: [
            'rgb(59, 130, 246)',
            'rgb(236, 72, 153)',
            'rgb(156, 163, 175)'
          ],
          borderWidth: 2
        }]
      },
      options: {
        responsive: true,
        maintainAspectRatio: false,
        plugins: {
          legend: {
            position: 'bottom'
          }
        }
      }
    })
  }

  // Barangay Cases Bar Chart
  if (barangayChart.value && props.barangayComparison) {
    const topBarangays = props.barangayComparison.slice(0, 10) // Show top 10 only

    new Chart(barangayChart.value, {
      type: 'bar',
      data: {
        labels: topBarangays.map(item => item.barangay),
        datasets: [{
          label: 'Cases',
          data: topBarangays.map(item => item.total_cases),
          backgroundColor: 'rgba(239, 68, 68, 0.6)',
          borderColor: 'rgb(239, 68, 68)',
          borderWidth: 1
        }]
      },
      options: {
        responsive: true,
        maintainAspectRatio: false,
        plugins: {
          legend: {
            display: false
          }
        },
        scales: {
          x: {
            ticks: {
              maxRotation: 45,
              minRotation: 45
            }
          },
          y: {
            beginAtZero: true
          }
        }
      }
    })
  }
}

onMounted(() => {
  initializeCharts()
})
</script>
