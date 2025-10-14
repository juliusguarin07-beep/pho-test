<script setup lang="ts">
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import { Head, Link, useForm } from '@inertiajs/vue3';
import { ref, watch, onMounted } from 'vue';

interface Disease {
    id: number;
    name: string;
    category: string;
}

interface Municipality {
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
    disease: Disease;
    municipality: Municipality;
}

interface Props {
    alert: OutbreakAlert;
    diseases: Disease[];
    municipalities: Municipality[];
}

const props = defineProps<Props>();

const form = useForm({
    disease_id: props.alert.disease_id,
    alert_title: props.alert.alert_title,
    alert_details: props.alert.alert_details,
    alert_level: props.alert.alert_level,
    municipality_id: props.alert.municipality_id,
    affected_barangays: props.alert.affected_barangays || [],
    alert_start_date: props.alert.alert_start_date,
    alert_end_date: props.alert.alert_end_date || '',
    health_advisory: props.alert.health_advisory || '',
    status: props.alert.status,
});

const barangays = ref<Array<{ id: number; name: string }>>([]);
const loadingBarangays = ref(false);

// Load barangays for the selected municipality
const loadBarangays = async (municipalityId: number | null) => {
    if (municipalityId) {
        loadingBarangays.value = true;
        try {
            const response = await fetch(`/api/barangays/${municipalityId}`);
            barangays.value = await response.json();
        } catch (error) {
            console.error('Error loading barangays:', error);
        } finally {
            loadingBarangays.value = false;
        }
    } else {
        barangays.value = [];
        form.affected_barangays = [];
    }
};

// Load barangays on mount for the pre-selected municipality
onMounted(() => {
    if (form.municipality_id) {
        loadBarangays(form.municipality_id);
    }
});

// Watch for municipality changes
watch(() => form.municipality_id, async (municipalityId) => {
    await loadBarangays(municipalityId);
});

const toggleBarangay = (barangayName: string) => {
    const index = form.affected_barangays.indexOf(barangayName);
    if (index > -1) {
        form.affected_barangays.splice(index, 1);
    } else {
        form.affected_barangays.push(barangayName);
    }
};

const submit = () => {
    form.put(route('outbreak-alerts.update', props.alert.id), {
        preserveScroll: true,
    });
};
</script>

<template>
    <Head title="Edit Outbreak Alert" />

    <AuthenticatedLayout>
        <template #header>
            <div class="flex justify-between items-center">
                <h2 class="text-xl font-semibold text-gray-900">
                    Edit Outbreak Alert
                </h2>
                <div class="flex gap-3">
                    <Link
                        :href="route('outbreak-alerts.show', alert.id)"
                        class="inline-flex items-center px-4 py-2 text-sm font-medium text-gray-600 bg-white border border-gray-300 rounded-lg hover:bg-gray-50 transition-colors duration-200"
                    >
                        <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" />
                        </svg>
                        View Alert
                    </Link>
                    <Link
                        :href="route('outbreak-alerts.index')"
                        class="inline-flex items-center px-4 py-2 text-sm font-medium text-gray-600 bg-white border border-gray-300 rounded-lg hover:bg-gray-50 transition-colors duration-200"
                    >
                        <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18" />
                        </svg>
                        Back to Alerts
                    </Link>
                </div>
            </div>
        </template>

        <div class="py-6">
            <div class="mx-auto max-w-4xl px-4 sm:px-6 lg:px-8">
                <div class="bg-white rounded-lg shadow-sm border border-gray-200">
                    <form @submit.prevent="submit" class="p-6">
                        <!-- Basic Information Section -->
                        <div class="mb-8">
                            <h3 class="text-lg font-medium text-gray-900 mb-4">Basic Information</h3>
                            <div class="space-y-4">
                                <!-- Alert Title -->
                                <div>
                                    <label for="alert_title" class="block text-sm font-medium text-gray-700 mb-1">
                                        Alert Title <span class="text-red-500">*</span>
                                    </label>
                                    <input
                                        id="alert_title"
                                        v-model="form.alert_title"
                                        type="text"
                                        required
                                        class="w-full px-3 py-2 border border-gray-300 rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-red-500 focus:border-red-500"
                                        placeholder="e.g., Dengue Outbreak in Dagupan City"
                                    />
                                    <div v-if="form.errors.alert_title" class="mt-1 text-sm text-red-600">
                                        {{ form.errors.alert_title }}
                                    </div>
                                </div>

                                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                                    <!-- Disease -->
                                    <div>
                                        <label for="disease_id" class="block text-sm font-medium text-gray-700 mb-1">
                                            Disease <span class="text-red-500">*</span>
                                        </label>
                                        <select
                                            id="disease_id"
                                            v-model="form.disease_id"
                                            required
                                            class="w-full px-3 py-2 border border-gray-300 rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-red-500 focus:border-red-500"
                                        >
                                            <option :value="null">Select Disease</option>
                                            <optgroup v-for="category in [...new Set(diseases.map(d => d.category))]" :key="category" :label="category">
                                                <option v-for="disease in diseases.filter(d => d.category === category)" :key="disease.id" :value="disease.id">
                                                    {{ disease.name }}
                                                </option>
                                            </optgroup>
                                        </select>
                                        <div v-if="form.errors.disease_id" class="mt-1 text-sm text-red-600">
                                            {{ form.errors.disease_id }}
                                        </div>
                                    </div>

                                    <!-- Alert Level -->
                                    <div>
                                        <label for="alert_level" class="block text-sm font-medium text-gray-700 mb-1">
                                            Alert Level <span class="text-red-500">*</span>
                                        </label>
                                        <select
                                            id="alert_level"
                                            v-model="form.alert_level"
                                            required
                                            class="w-full px-3 py-2 border border-gray-300 rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-red-500 focus:border-red-500"
                                        >
                                            <option value="Green">🟢 Green - Monitor Situation</option>
                                            <option value="Yellow">🟡 Yellow - Increase Vigilance</option>
                                            <option value="Orange">🟠 Orange - Immediate Action Required</option>
                                            <option value="Red">🔴 Red - Critical Emergency</option>
                                        </select>
                                        <div v-if="form.errors.alert_level" class="mt-1 text-sm text-red-600">
                                            {{ form.errors.alert_level }}
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Location Information Section -->
                        <div class="mb-8 pb-6 border-b border-gray-200">
                            <h3 class="text-lg font-medium text-gray-900 mb-4">Location Information</h3>
                            <div class="space-y-4">
                                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                                    <!-- Municipality -->
                                    <div>
                                        <label for="municipality_id" class="block text-sm font-medium text-gray-700 mb-1">
                                            Municipality <span class="text-red-500">*</span>
                                        </label>
                                        <select
                                            id="municipality_id"
                                            v-model="form.municipality_id"
                                            required
                                            class="w-full px-3 py-2 border border-gray-300 rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-red-500 focus:border-red-500"
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

                                    <!-- Status -->
                                    <div>
                                        <label for="status" class="block text-sm font-medium text-gray-700 mb-1">
                                            Status <span class="text-red-500">*</span>
                                        </label>
                                        <select
                                            id="status"
                                            v-model="form.status"
                                            required
                                            class="w-full px-3 py-2 border border-gray-300 rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-red-500 focus:border-red-500"
                                        >
                                            <option value="draft">📝 Draft - Not Published</option>
                                            <option value="published">📢 Published - Active Alert</option>
                                            <option value="archived">📦 Archived - Ended</option>
                                        </select>
                                        <div v-if="form.errors.status" class="mt-1 text-sm text-red-600">
                                            {{ form.errors.status }}
                                        </div>
                                    </div>
                                </div>

                                <!-- Affected Barangays -->
                                <div v-if="form.municipality_id">
                                    <label class="block text-sm font-medium text-gray-700 mb-2">
                                        Affected Barangays
                                    </label>
                                    <div v-if="loadingBarangays" class="text-sm text-gray-500 py-4">
                                        Loading barangays...
                                    </div>
                                    <div v-else-if="barangays.length > 0" class="border border-gray-200 rounded-lg p-3">
                                        <div class="grid grid-cols-2 md:grid-cols-3 lg:grid-cols-4 gap-2 max-h-48 overflow-y-auto">
                                            <label v-for="barangay in barangays" :key="barangay.id" class="flex items-center p-2 hover:bg-gray-50 rounded">
                                                <input
                                                    type="checkbox"
                                                    :checked="form.affected_barangays.includes(barangay.name)"
                                                    @change="toggleBarangay(barangay.name)"
                                                    class="w-4 h-4 text-red-600 border-gray-300 rounded focus:ring-red-500"
                                                />
                                                <span class="ml-2 text-sm text-gray-700">{{ barangay.name }}</span>
                                            </label>
                                        </div>
                                        <div class="mt-2 pt-2 border-t border-gray-200 text-xs text-gray-500">
                                            {{ form.affected_barangays.length }} barangay(s) selected
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Timeline Section -->
                        <div class="mb-8 pb-6 border-b border-gray-200">
                            <h3 class="text-lg font-medium text-gray-900 mb-4">Timeline</h3>
                            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                                <!-- Start Date -->
                                <div>
                                    <label for="alert_start_date" class="block text-sm font-medium text-gray-700 mb-1">
                                        Alert Start Date <span class="text-red-500">*</span>
                                    </label>
                                    <input
                                        id="alert_start_date"
                                        v-model="form.alert_start_date"
                                        type="date"
                                        required
                                        class="w-full px-3 py-2 border border-gray-300 rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-red-500 focus:border-red-500"
                                    />
                                    <div v-if="form.errors.alert_start_date" class="mt-1 text-sm text-red-600">
                                        {{ form.errors.alert_start_date }}
                                    </div>
                                </div>

                                <!-- End Date -->
                                <div>
                                    <label for="alert_end_date" class="block text-sm font-medium text-gray-700 mb-1">
                                        Alert End Date
                                    </label>
                                    <input
                                        id="alert_end_date"
                                        v-model="form.alert_end_date"
                                        type="date"
                                        :min="form.alert_start_date"
                                        class="w-full px-3 py-2 border border-gray-300 rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-red-500 focus:border-red-500"
                                    />
                                    <p class="mt-1 text-xs text-gray-500">Leave blank if ongoing</p>
                                    <div v-if="form.errors.alert_end_date" class="mt-1 text-sm text-red-600">
                                        {{ form.errors.alert_end_date }}
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Details Section -->
                        <div class="mb-8">
                            <h3 class="text-lg font-medium text-gray-900 mb-4">Details & Recommendations</h3>
                            <div class="space-y-4">
                                <!-- Alert Details -->
                                <div>
                                    <label for="alert_details" class="block text-sm font-medium text-gray-700 mb-1">
                                        Alert Details <span class="text-red-500">*</span>
                                    </label>
                                    <textarea
                                        id="alert_details"
                                        v-model="form.alert_details"
                                        required
                                        rows="4"
                                        class="w-full px-3 py-2 border border-gray-300 rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-red-500 focus:border-red-500"
                                        placeholder="Describe the outbreak situation, number of cases, and current status..."
                                    ></textarea>
                                    <div v-if="form.errors.alert_details" class="mt-1 text-sm text-red-600">
                                        {{ form.errors.alert_details }}
                                    </div>
                                </div>

                                <!-- Health Advisory -->
                                <div>
                                    <label for="health_advisory" class="block text-sm font-medium text-gray-700 mb-1">
                                        Health Advisory / Recommendations
                                    </label>
                                    <textarea
                                        id="health_advisory"
                                        v-model="form.health_advisory"
                                        rows="3"
                                        class="w-full px-3 py-2 border border-gray-300 rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-red-500 focus:border-red-500"
                                        placeholder="Provide health recommendations and preventive measures for the public..."
                                    ></textarea>
                                    <div v-if="form.errors.health_advisory" class="mt-1 text-sm text-red-600">
                                        {{ form.errors.health_advisory }}
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Form Actions -->
                        <div class="flex items-center justify-end gap-3 pt-6 border-t border-gray-200">
                            <Link
                                :href="route('outbreak-alerts.show', alert.id)"
                                class="px-4 py-2 text-sm font-medium text-gray-600 bg-white border border-gray-300 rounded-lg hover:bg-gray-50 transition-colors duration-200"
                            >
                                Cancel
                            </Link>
                            <button
                                type="submit"
                                :disabled="form.processing"
                                class="inline-flex items-center px-4 py-2 text-sm font-medium text-white bg-blue-600 border border-transparent rounded-lg hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:ring-offset-2 disabled:opacity-50 transition-colors duration-200"
                            >
                                <svg v-if="form.processing" class="animate-spin -ml-1 mr-2 h-4 w-4 text-white" fill="none" viewBox="0 0 24 24">
                                    <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                                    <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                                </svg>
                                <svg v-else class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
                                </svg>
                                {{ form.processing ? 'Updating...' : 'Update Alert' }}
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </AuthenticatedLayout>
</template>


