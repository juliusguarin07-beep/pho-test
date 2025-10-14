<script setup lang="ts">
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import { Head, useForm } from '@inertiajs/vue3';
import { ref, computed } from 'vue';

interface Props {
    diseases: Array<{ id: number; name: string; category: string }>;
    municipalities: Array<{ id: number; name: string }>;
    barangays: Array<{ id: number; name: string; municipality_id: number }>;
    facilities: Array<{ id: number; name: string; type: string }>;
    userMunicipality?: { id: number; name: string } | null;
    userRole?: string;
    userName?: string;
    userPosition?: string;
    userContact?: string;
}

const props = defineProps<Props>();

// Check if user is encoder
const isEncoder = computed(() => props.userRole === 'encoder');

const currentStep = ref(1);
const totalSteps = 7;

const form = useForm({
    // Section A
    disease_id: '',
    disease_other: '',
    date_reported: new Date().toISOString().split('T')[0],
    case_classification: 'Suspect',
    outcome: 'Alive',
    submitAction: 'draft' as 'draft' | 'submit',

    // Section B
    last_name: '',
    first_name: '',
    middle_name: '',
    sex: 'Male',
    date_of_birth: '',
    civil_status: 'Single',
    occupation: '',
    address: '',
    barangay_id: '',
    municipality_id: (isEncoder.value && props.userMunicipality) ? props.userMunicipality.id.toString() : '',
    contact_number: '',
    pregnancy_status: 'N/A',
    philhealth_number: '',
    nationality: 'Filipino',

    // Section C
    date_of_onset: '',
    date_of_consultation: '',
    admitting_facility_id: '',
    signs_symptoms: [] as string[],
    signs_symptoms_other: '',
    clinical_classification: '',
    complications: '',
    clinical_outcome: 'Alive',
    date_of_death: '',
    final_diagnosis: '',
    clinical_remarks: '',

    // Section D
    specimen_collection_date: '',
    specimen_type: '',
    laboratory_test: '',
    laboratory_result_date: '',
    laboratory_result: '',
    testing_laboratory: '',
    laboratory_file: null as File | null,

    // Section E
    place_of_exposure: '',
    date_of_exposure: '',
    travel_history: '',
    travel_departure_date: '',
    travel_return_date: '',
    mode_of_exposure: '',
    source_of_infection: '',

    // Section F
    contacts_identified: false,
    number_of_contacts: 0,
    contact_details: '',
    relationship_to_case: '',
    date_contacted: '',
    quarantine_status: '',

    // Section G
    reporting_health_worker: props.userName || '',
    health_worker_designation: props.userPosition || '',
    health_worker_contact: props.userContact || '',
});

// Determine if fields should be required (not required for drafts)
const isSubmitting = ref(false);
const shouldRequireFields = computed(() => isSubmitting.value);

const filteredBarangays = computed(() => {
    if (!form.municipality_id) return [];
    return props.barangays.filter(b => b.municipality_id === parseInt(form.municipality_id as any));
});

const selectedDiseaseCategory = computed(() => {
    const disease = props.diseases.find(d => d.id === parseInt(form.disease_id as any));
    return disease?.category || '';
});

// Define contagious and non-contagious diseases
const contagiousDiseases = [
    'Acute Bloody Diarrhea', 'Acute Encephalitis Syndrome', 'Chicken Pox', 'Cholera',
    'COVID-19', 'Diarrhea', 'Food Poisoning', 'Influenza-like Illness', 'Measles',
    'Pneumonia', 'Tuberculosis', 'Typhoid Fever'
];

const nonContagiousDiseases = [
    'Dengue', 'Leptospirosis', 'Rabies'
];

const selectedDiseaseInfo = computed(() => {
    const disease = props.diseases.find(d => d.id === parseInt(form.disease_id as any));
    if (!disease) return null;

    const isContagious = contagiousDiseases.includes(disease.name);
    const isNonContagious = nonContagiousDiseases.includes(disease.name);

    return {
        name: disease.name,
        category: disease.category,
        isContagious,
        isNonContagious,
        displayCategory: isContagious ? 'Contagious Diseases' : isNonContagious ? 'Non-Contagious Diseases' : disease.category
    };
});

// Group diseases by contagiousness
const groupedDiseases = computed(() => {
    const contagious = props.diseases.filter(d => contagiousDiseases.includes(d.name));
    const nonContagious = props.diseases.filter(d => nonContagiousDiseases.includes(d.name));
    const others = props.diseases.filter(d => !contagiousDiseases.includes(d.name) && !nonContagiousDiseases.includes(d.name));

    return [
        { category: 'Contagious Diseases', diseases: contagious },
        { category: 'Non-Contagious Diseases', diseases: nonContagious },
        ...(others.length > 0 ? [{ category: 'Other Diseases', diseases: others }] : [])
    ];
});

// Determine which steps should be available
const availableSteps = computed(() => {
    const diseaseInfo = selectedDiseaseInfo.value;
    if (!diseaseInfo || diseaseInfo.isContagious) {
        // Contagious diseases or no disease selected: all steps available
        return [1, 2, 3, 4, 5, 6, 7];
    } else if (diseaseInfo.isNonContagious) {
        // Non-contagious diseases: skip steps 5 (Exposure) and 6 (Contact Tracing)
        return [1, 2, 3, 4, 7];
    }
    // Default: all steps
    return [1, 2, 3, 4, 5, 6, 7];
});

const commonSymptoms = [
    'Fever', 'Cough', 'Headache', 'Body Pain', 'Fatigue',
    'Nausea', 'Vomiting', 'Diarrhea', 'Rash', 'Difficulty Breathing'
];

const nextStep = () => {
    const currentIndex = availableSteps.value.indexOf(currentStep.value);
    if (currentIndex < availableSteps.value.length - 1) {
        currentStep.value = availableSteps.value[currentIndex + 1];
    }
};

const prevStep = () => {
    const currentIndex = availableSteps.value.indexOf(currentStep.value);
    if (currentIndex > 0) {
        currentStep.value = availableSteps.value[currentIndex - 1];
    }
};

const goToStep = (step: number) => {
    if (availableSteps.value.includes(step)) {
        currentStep.value = step;
    }
};

const saveDraft = () => {
    // Immediately disable validation for draft save
    isSubmitting.value = false;
    form.submitAction = 'draft';

    // Use a timeout to ensure the novalidate attribute is applied
    setTimeout(() => {
        form.post(route('case-reports.store'), {
            preserveScroll: true,
            onError: (errors) => {
                console.error('Draft save errors:', errors);
            }
        });
    }, 10);
};

const submitForm = () => {
    isSubmitting.value = true;
    form.submitAction = 'submit';
    form.post(route('case-reports.store'), {
        preserveScroll: true,
        onError: (errors) => {
            console.error('Submit errors:', errors);
        }
    });
};

const handleFileUpload = (event: Event) => {
    const target = event.target as HTMLInputElement;
    if (target.files && target.files[0]) {
        form.laboratory_file = target.files[0];
    }
};
</script>

<template>
    <Head title="New Case Report" />

    <AuthenticatedLayout>
        <template #header>
            <div class="flex items-center justify-between">
                <div class="flex items-center space-x-4">
                    <div class="p-2 bg-blue-100 rounded-lg">
                        <svg class="w-6 h-6 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                        </svg>
                    </div>
                    <div>
                        <h2 class="text-2xl font-bold text-gray-900">New Case Investigation Form</h2>
                        <p class="text-sm text-gray-600 mt-0.5">Complete all required sections to submit case report</p>
                    </div>
                </div>
                <div class="flex items-center space-x-3">
                    <div class="text-right">
                        <div class="text-xs text-gray-500 uppercase tracking-wide">Progress</div>
                        <div class="text-lg font-bold text-blue-600">{{ currentStep }}/{{ totalSteps }}</div>
                    </div>
                </div>
            </div>
        </template>

        <div class="py-8 bg-gray-50 min-h-screen">
            <div class="mx-auto max-w-6xl sm:px-6 lg:px-8">

                <!-- Modern Progress Stepper -->
                <div class="mb-8 bg-white rounded-2xl shadow-lg p-8">
                    <div class="relative">
                        <!-- Progress Bar Background -->
                        <div class="absolute top-5 left-0 right-0 h-1 bg-gray-200 rounded-full" style="margin-left: 2.5rem; margin-right: 2.5rem;"></div>
                        <!-- Progress Bar Fill -->
                        <div class="absolute top-5 left-0 h-1 bg-gradient-to-r from-blue-500 to-indigo-600 rounded-full transition-all duration-500"
                             :style="{ width: `calc(${((availableSteps.indexOf(currentStep)) / (availableSteps.length - 1)) * 100}% - ${((availableSteps.indexOf(currentStep)) / (availableSteps.length - 1)) * 5}rem + 2.5rem)`, marginLeft: '2.5rem' }">
                        </div>

                        <!-- Steps -->
                        <div class="relative flex justify-between">
                            <div v-for="step in availableSteps" :key="step" class="flex flex-col items-center">
                                <!-- Step Circle -->
                                <div :class="[
                                    'w-12 h-12 rounded-full flex items-center justify-center text-sm font-bold transition-all duration-300 relative z-10 cursor-pointer hover:scale-105',
                                    availableSteps.indexOf(step) < availableSteps.indexOf(currentStep) ? 'bg-gradient-to-br from-blue-500 to-indigo-600 text-white shadow-lg hover:from-blue-600 hover:to-indigo-700' :
                                    step === currentStep ? 'bg-gradient-to-br from-blue-600 to-indigo-700 text-white shadow-2xl ring-4 ring-blue-200 scale-110' :
                                    'bg-white border-2 border-gray-300 text-gray-400 hover:border-gray-400 hover:text-gray-600'
                                ]"
                                @click="goToStep(step)">
                                    <span v-if="step < currentStep">
                                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M5 13l4 4L19 7" />
                                        </svg>
                                    </span>
                                    <span v-else>{{ step }}</span>
                                </div>

                                <!-- Step Label -->
                                <div class="mt-3 text-center cursor-pointer" @click="goToStep(step)">
                                    <div :class="[
                                        'text-xs font-semibold transition-colors duration-300 hover:text-blue-800',
                                        step <= currentStep ? 'text-blue-600' : 'text-gray-400 hover:text-gray-700'
                                    ]">
                                        <div v-if="step === 1">Case ID</div>
                                        <div v-else-if="step === 2">Patient</div>
                                        <div v-else-if="step === 3">Clinical</div>
                                        <div v-else-if="step === 4">Laboratory</div>
                                        <div v-else-if="step === 5">Exposure</div>
                                        <div v-else-if="step === 6">Contacts</div>
                                        <div v-else-if="step === 7">Reporting</div>
                                    </div>
                                    <div v-if="availableSteps.indexOf(step) < availableSteps.indexOf(currentStep)" class="text-xs text-green-600 mt-1">Complete</div>
                                    <div v-else-if="step === currentStep" class="text-xs text-blue-600 mt-1 font-medium">● Active</div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Error Display -->
                <div v-if="Object.keys(form.errors).length > 0" class="mb-6 bg-red-50 border-l-4 border-red-500 rounded-lg shadow-md overflow-hidden">
                    <div class="p-5">
                        <div class="flex items-center mb-3">
                            <div class="p-2 bg-red-100 rounded-lg mr-3">
                                <svg class="w-5 h-5 text-red-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z" />
                                </svg>
                            </div>
                            <h4 class="text-red-900 font-bold text-lg">Validation Errors</h4>
                        </div>
                        <p class="text-red-800 text-sm mb-3">Please correct the following errors before proceeding:</p>
                        <ul class="space-y-2">
                            <li v-for="(error, field) in form.errors" :key="field" class="flex items-start text-red-700 text-sm">
                                <svg class="w-4 h-4 text-red-500 mr-2 mt-0.5 flex-shrink-0" fill="currentColor" viewBox="0 0 20 20">
                                    <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.707 7.293a1 1 0 00-1.414 1.414L8.586 10l-1.293 1.293a1 1 0 101.414 1.414L10 11.414l1.293 1.293a1 1 0 001.414-1.414L11.414 10l1.293-1.293a1 1 0 00-1.414-1.414L10 8.586 8.707 7.293z" clip-rule="evenodd" />
                                </svg>
                                <span><strong class="font-semibold">{{ field }}:</strong> {{ error }}</span>
                            </li>
                        </ul>
                    </div>
                </div>

                <!-- Form Content Card -->
                <div class="bg-white overflow-hidden shadow-xl rounded-2xl border border-gray-100">
                    <form @submit.prevent="submitForm" :novalidate="!shouldRequireFields">

                        <!-- Section A: Case Identification -->
                        <div v-show="currentStep === 1" class="p-8">
                            <div class="mb-6">
                                <div class="flex items-center mb-4">
                                    <div class="p-3 bg-blue-100 rounded-xl mr-4">
                                        <svg class="w-7 h-7 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                                        </svg>
                                    </div>
                                    <div>
                                        <h3 class="text-2xl font-bold text-gray-900">Case Identification</h3>
                                        <p class="text-sm text-gray-600 mt-1">Enter basic case information and classification</p>
                                    </div>
                                </div>
                                <div class="h-1 bg-gradient-to-r from-blue-500 to-indigo-600 rounded-full"></div>
                            </div>

                            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                                <div>
                                    <label class="block text-sm font-semibold text-gray-700 mb-2">
                                        Disease <span class="text-red-500">*</span>
                                    </label>
                                    <select v-model="form.disease_id" :required="shouldRequireFields"
                                            class="w-full rounded-xl border-gray-300 shadow-sm focus:border-blue-500 focus:ring-2 focus:ring-blue-200 transition-all duration-200 py-3 px-4">
                                        <option value="">Select Disease</option>
                                        <optgroup v-for="group in groupedDiseases" :key="group.category" :label="group.category">
                                            <option v-for="disease in group.diseases" :key="disease.id" :value="disease.id">
                                                {{ disease.name }}
                                            </option>
                                        </optgroup>
                                        <option value="other">Other (Specify)</option>
                                    </select>
                                    <span v-if="form.errors.disease_id" class="text-red-500 text-xs mt-1 block">{{ form.errors.disease_id }}</span>
                                </div>

                                <!-- Disease Category Information -->
                                <div v-if="selectedDiseaseInfo && selectedDiseaseInfo.isNonContagious"
                                     class="mt-4 p-4 bg-amber-50 border border-amber-200 rounded-lg">
                                    <div class="flex items-start">
                                        <div class="flex-shrink-0">
                                            <svg class="w-5 h-5 text-amber-600 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                                            </svg>
                                        </div>
                                        <div class="ml-3">
                                            <h4 class="text-sm font-medium text-amber-800">Non-Contagious Disease Selected</h4>
                                            <p class="text-sm text-amber-700 mt-1">
                                                Since <strong>{{ selectedDiseaseInfo.name }}</strong> is a non-contagious disease,
                                                the <strong>Exposure History</strong> and <strong>Contact Tracing</strong> sections will be skipped.
                                                You will fill sections 1, 2, 3, 4, and 7 only.
                                            </p>
                                        </div>
                                    </div>
                                </div>

                                <div v-if="form.disease_id === 'other'">
                                    <label class="block text-sm font-semibold text-gray-700 mb-2">Specify Other Disease</label>
                                    <input v-model="form.disease_other" type="text"
                                           class="w-full rounded-xl border-gray-300 shadow-sm focus:border-blue-500 focus:ring-2 focus:ring-blue-200 transition-all duration-200 py-3 px-4"
                                           placeholder="Enter disease name" />
                                </div>

                                <div>
                                    <label class="block text-sm font-semibold text-gray-700 mb-2">
                                        Date Reported <span class="text-red-500">*</span>
                                    </label>
                                    <input v-model="form.date_reported" type="date" :required="shouldRequireFields"
                                           class="w-full rounded-xl border-gray-300 shadow-sm focus:border-blue-500 focus:ring-2 focus:ring-blue-200 transition-all duration-200 py-3 px-4" />
                                </div>

                                <div>
                                    <label class="block text-sm font-semibold text-gray-700 mb-2">
                                        Case Classification <span class="text-red-500">*</span>
                                    </label>
                                    <select v-model="form.case_classification" :required="shouldRequireFields"
                                            class="w-full rounded-xl border-gray-300 shadow-sm focus:border-blue-500 focus:ring-2 focus:ring-blue-200 transition-all duration-200 py-3 px-4">
                                        <option value="Suspect">Suspect</option>
                                        <option value="Confirmed">Confirmed</option>
                                    </select>
                                </div>

                                <div>
                                    <label class="block text-sm font-semibold text-gray-700 mb-2">
                                        Outcome <span class="text-red-500">*</span>
                                    </label>
                                    <select v-model="form.outcome" :required="shouldRequireFields"
                                            class="w-full rounded-xl border-gray-300 shadow-sm focus:border-blue-500 focus:ring-2 focus:ring-blue-200 transition-all duration-200 py-3 px-4">
                                        <option value="Alive">Alive</option>
                                        <option value="Died">Died</option>
                                        <option value="Ongoing Treatment">Ongoing Treatment</option>
                                    </select>
                                </div>
                            </div>
                        </div>

                        <!-- Section B: Patient Information -->
                        <div v-show="currentStep === 2" class="p-8">
                            <div class="mb-6">
                                <div class="flex items-center mb-4">
                                    <div class="p-3 bg-green-100 rounded-xl mr-4">
                                        <svg class="w-7 h-7 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z" />
                                        </svg>
                                    </div>
                                    <div>
                                        <h3 class="text-2xl font-bold text-gray-900">Patient Information</h3>
                                        <p class="text-sm text-gray-600 mt-1">Personal details and demographics</p>
                                    </div>
                                </div>
                                <div class="h-1 bg-gradient-to-r from-green-500 to-emerald-600 rounded-full"></div>
                            </div>

                            <div class="space-y-6">
                                <!-- Name Section -->
                                <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                                    <div>
                                        <label class="block text-sm font-semibold text-gray-700 mb-2">
                                            Last Name <span class="text-red-500">*</span>
                                        </label>
                                        <input v-model="form.last_name" type="text" :required="shouldRequireFields"
                                               class="w-full rounded-xl border-gray-300 shadow-sm focus:border-green-500 focus:ring-2 focus:ring-green-200 transition-all duration-200 py-3 px-4"
                                               placeholder="Enter last name" />
                                    </div>

                                    <div>
                                        <label class="block text-sm font-semibold text-gray-700 mb-2">
                                            First Name <span class="text-red-500">*</span>
                                        </label>
                                        <input v-model="form.first_name" type="text" :required="shouldRequireFields"
                                               class="w-full rounded-xl border-gray-300 shadow-sm focus:border-green-500 focus:ring-2 focus:ring-green-200 transition-all duration-200 py-3 px-4"
                                               placeholder="Enter first name" />
                                    </div>

                                    <div>
                                        <label class="block text-sm font-semibold text-gray-700 mb-2">Middle Name</label>
                                        <input v-model="form.middle_name" type="text"
                                               class="w-full rounded-xl border-gray-300 shadow-sm focus:border-green-500 focus:ring-2 focus:ring-green-200 transition-all duration-200 py-3 px-4"
                                               placeholder="Enter middle name" />
                                    </div>
                                </div>

                                <!-- Demographics Section -->
                                <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                                    <div>
                                        <label class="block text-sm font-semibold text-gray-700 mb-3">
                                            Sex <span class="text-red-500">*</span>
                                        </label>
                                        <div class="flex gap-4">
                                            <label class="flex items-center px-4 py-3 bg-gray-50 rounded-xl cursor-pointer hover:bg-green-50 transition-colors duration-200 flex-1">
                                                <input v-model="form.sex" type="radio" value="Male" class="text-green-600 focus:ring-green-500 mr-2" />
                                                <span class="text-sm font-medium text-gray-700">Male</span>
                                            </label>
                                            <label class="flex items-center px-4 py-3 bg-gray-50 rounded-xl cursor-pointer hover:bg-green-50 transition-colors duration-200 flex-1">
                                                <input v-model="form.sex" type="radio" value="Female" class="text-green-600 focus:ring-green-500 mr-2" />
                                                <span class="text-sm font-medium text-gray-700">Female</span>
                                            </label>
                                        </div>
                                    </div>

                                    <div>
                                        <label class="block text-sm font-semibold text-gray-700 mb-2">
                                            Date of Birth <span class="text-red-500">*</span>
                                        </label>
                                        <input v-model="form.date_of_birth" type="date" :required="shouldRequireFields"
                                               class="w-full rounded-xl border-gray-300 shadow-sm focus:border-green-500 focus:ring-2 focus:ring-green-200 transition-all duration-200 py-3 px-4" />
                                    </div>

                                    <div>
                                        <label class="block text-sm font-semibold text-gray-700 mb-2">Civil Status</label>
                                        <select v-model="form.civil_status"
                                                class="w-full rounded-xl border-gray-300 shadow-sm focus:border-green-500 focus:ring-2 focus:ring-green-200 transition-all duration-200 py-3 px-4">
                                            <option value="Single">Single</option>
                                            <option value="Married">Married</option>
                                            <option value="Widowed">Widowed</option>
                                            <option value="Separated">Separated</option>
                                        </select>
                                    </div>
                                </div>

                                <!-- Contact & Location Section -->
                                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                                    <div>
                                        <label class="block text-sm font-semibold text-gray-700 mb-2">Occupation</label>
                                        <input v-model="form.occupation" type="text"
                                               class="w-full rounded-xl border-gray-300 shadow-sm focus:border-green-500 focus:ring-2 focus:ring-green-200 transition-all duration-200 py-3 px-4"
                                               placeholder="Enter occupation" />
                                    </div>

                                    <div>
                                        <label class="block text-sm font-semibold text-gray-700 mb-2">Contact Number</label>
                                        <input v-model="form.contact_number" type="tel"
                                               class="w-full rounded-xl border-gray-300 shadow-sm focus:border-green-500 focus:ring-2 focus:ring-green-200 transition-all duration-200 py-3 px-4"
                                               placeholder="+63 XXX XXX XXXX" />
                                    </div>
                                </div>

                                <div>
                                    <label class="block text-sm font-semibold text-gray-700 mb-2">
                                        Complete Address <span class="text-red-500">*</span>
                                    </label>
                                    <textarea v-model="form.address" :required="shouldRequireFields" rows="2"
                                              class="w-full rounded-xl border-gray-300 shadow-sm focus:border-green-500 focus:ring-2 focus:ring-green-200 transition-all duration-200 py-3 px-4"
                                              placeholder="Enter complete address"></textarea>
                                </div>

                                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                                    <div>
                                        <label class="block text-sm font-semibold text-gray-700 mb-2">
                                            Municipality <span class="text-red-500">*</span>
                                        </label>
                                        <select v-model="form.municipality_id" :required="shouldRequireFields"
                                                :disabled="isEncoder"
                                                :class="{
                                                    'w-full rounded-xl border-gray-300 shadow-sm focus:border-green-500 focus:ring-2 focus:ring-green-200 transition-all duration-200 py-3 px-4': true,
                                                    'bg-gray-100 cursor-not-allowed': isEncoder,
                                                    'hover:bg-gray-50': !isEncoder
                                                }">
                                            <option value="">Select Municipality</option>
                                            <option v-for="municipality in municipalities" :key="municipality.id" :value="municipality.id">
                                                {{ municipality.name }}
                                            </option>
                                        </select>
                                        <p v-if="isEncoder" class="mt-1 text-xs text-gray-500">
                                            Auto-filled based on your assigned municipality
                                        </p>
                                    </div>

                                    <div>
                                        <label class="block text-sm font-semibold text-gray-700 mb-2">
                                            Barangay <span class="text-red-500">*</span>
                                        </label>
                                        <select v-model="form.barangay_id" :required="shouldRequireFields" :disabled="!form.municipality_id"
                                                class="w-full rounded-xl border-gray-300 shadow-sm focus:border-green-500 focus:ring-2 focus:ring-green-200 transition-all duration-200 py-3 px-4 disabled:bg-gray-100">
                                            <option value="">Select Barangay</option>
                                            <option v-for="barangay in filteredBarangays" :key="barangay.id" :value="barangay.id">
                                                {{ barangay.name }}
                                            </option>
                                        </select>
                                    </div>
                                </div>

                                <div v-if="form.sex === 'Female'" class="grid grid-cols-1 md:grid-cols-2 gap-6">
                                    <div>
                                        <label class="block text-sm font-semibold text-gray-700 mb-2">Pregnancy Status</label>
                                        <select v-model="form.pregnancy_status"
                                                class="w-full rounded-xl border-gray-300 shadow-sm focus:border-green-500 focus:ring-2 focus:ring-green-200 transition-all duration-200 py-3 px-4">
                                            <option value="N/A">Not Applicable</option>
                                            <option value="Pregnant">Pregnant</option>
                                            <option value="Not Pregnant">Not Pregnant</option>
                                        </select>
                                    </div>
                                </div>

                                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                                    <div>
                                        <label class="block text-sm font-semibold text-gray-700 mb-2">PhilHealth Number</label>
                                        <input v-model="form.philhealth_number" type="text"
                                               class="w-full rounded-xl border-gray-300 shadow-sm focus:border-green-500 focus:ring-2 focus:ring-green-200 transition-all duration-200 py-3 px-4"
                                               placeholder="XX-XXXXXXXXX-X" />
                                    </div>

                                    <div>
                                        <label class="block text-sm font-semibold text-gray-700 mb-2">Nationality</label>
                                        <input v-model="form.nationality" type="text"
                                               class="w-full rounded-xl border-gray-300 shadow-sm focus:border-green-500 focus:ring-2 focus:ring-green-200 transition-all duration-200 py-3 px-4"
                                               placeholder="Filipino" />
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Section C: Clinical Information -->
                        <div v-show="currentStep === 3" class="space-y-4">
                            <h3 class="text-lg font-semibold text-gray-900 mb-4">Section C: Clinical Information</h3>

                            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                                <div>
                                    <label class="block text-sm font-medium text-gray-700 mb-1">Date of Onset of Illness <span class="text-red-500">*</span></label>
                                    <input v-model="form.date_of_onset" type="date" :required="shouldRequireFields" class="w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500" />
                                </div>

                                <div>
                                    <label class="block text-sm font-medium text-gray-700 mb-1">Date of Consultation</label>
                                    <input v-model="form.date_of_consultation" type="date" class="w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500" />
                                </div>
                            </div>

                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-1">Admitting Facility</label>
                                <select v-model="form.admitting_facility_id" class="w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500">
                                    <option value="">Select Facility</option>
                                    <optgroup v-for="type in ['District Hospital', 'Provincial Hospital', 'RHU']" :key="type" :label="type">
                                        <option v-for="facility in facilities.filter(f => f.type === type)" :key="facility.id" :value="facility.id">
                                            {{ facility.name }}
                                        </option>
                                    </optgroup>
                                </select>
                            </div>

                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-2">Signs and Symptoms</label>
                                <div class="grid grid-cols-2 md:grid-cols-3 gap-2">
                                    <label v-for="symptom in commonSymptoms" :key="symptom" class="flex items-center">
                                        <input v-model="form.signs_symptoms" type="checkbox" :value="symptom" class="mr-2 rounded" />
                                        {{ symptom }}
                                    </label>
                                </div>
                                <input v-model="form.signs_symptoms_other" type="text" placeholder="Other symptoms" class="mt-2 w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500" />
                            </div>

                            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                                <div>
                                    <label class="block text-sm font-medium text-gray-700 mb-1">Clinical Outcome</label>
                                    <select v-model="form.clinical_outcome" class="w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500">
                                        <option value="Alive">Alive</option>
                                        <option value="Died">Died</option>
                                        <option value="Unknown">Unknown</option>
                                    </select>
                                </div>

                                <div v-if="form.clinical_outcome === 'Died'">
                                    <label class="block text-sm font-medium text-gray-700 mb-1">Date of Death</label>
                                    <input v-model="form.date_of_death" type="date" class="w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500" />
                                </div>
                            </div>

                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-1">Complications</label>
                                <textarea v-model="form.complications" rows="2" class="w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500"></textarea>
                            </div>

                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-1">Final Diagnosis</label>
                                <input v-model="form.final_diagnosis" type="text" class="w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500" />
                            </div>

                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-1">Clinical Remarks</label>
                                <textarea v-model="form.clinical_remarks" rows="3" class="w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500"></textarea>
                            </div>
                        </div>

                        <!-- Section D: Laboratory Information -->
                        <div v-show="currentStep === 4" class="space-y-4">
                            <h3 class="text-lg font-semibold text-gray-900 mb-4">Section D: Laboratory Information</h3>

                            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                                <div>
                                    <label class="block text-sm font-medium text-gray-700 mb-1">Date of Specimen Collection</label>
                                    <input v-model="form.specimen_collection_date" type="date" class="w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500" />
                                </div>

                                <div>
                                    <label class="block text-sm font-medium text-gray-700 mb-1">Specimen Type</label>
                                    <select v-model="form.specimen_type" class="w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500">
                                        <option value="">Select Type</option>
                                        <option value="Blood">Blood</option>
                                        <option value="Swab">Swab</option>
                                        <option value="Urine">Urine</option>
                                        <option value="Stool">Stool</option>
                                        <option value="Sputum">Sputum</option>
                                        <option value="Other">Other</option>
                                    </select>
                                </div>
                            </div>

                            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                                <div>
                                    <label class="block text-sm font-medium text-gray-700 mb-1">Laboratory Test Done</label>
                                    <select v-model="form.laboratory_test" class="w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500">
                                        <option value="">Select Test</option>
                                        <option value="RT-PCR">RT-PCR</option>
                                        <option value="ELISA">ELISA</option>
                                        <option value="Culture">Culture</option>
                                        <option value="Rapid Test">Rapid Test</option>
                                        <option value="Microscopy">Microscopy</option>
                                        <option value="Other">Other</option>
                                    </select>
                                </div>

                                <div>
                                    <label class="block text-sm font-medium text-gray-700 mb-1">Date of Laboratory Result</label>
                                    <input v-model="form.laboratory_result_date" type="date" class="w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500" />
                                </div>
                            </div>

                            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                                <div>
                                    <label class="block text-sm font-medium text-gray-700 mb-1">Laboratory Result</label>
                                    <select v-model="form.laboratory_result" class="w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500">
                                        <option value="">Select Result</option>
                                        <option value="Positive">Positive</option>
                                        <option value="Negative">Negative</option>
                                        <option value="Pending">Pending</option>
                                    </select>
                                </div>

                                <div>
                                    <label class="block text-sm font-medium text-gray-700 mb-1">Testing Laboratory Name</label>
                                    <input v-model="form.testing_laboratory" type="text" class="w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500" />
                                </div>
                            </div>

                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-1">Upload Laboratory Result (PDF/Image, max 5MB)</label>
                                <input @change="handleFileUpload" type="file" accept=".pdf,.jpg,.jpeg,.png" class="w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500" />
                                <p class="text-xs text-gray-500 mt-1">Accepted formats: PDF, JPG, PNG (Max 5MB)</p>
                            </div>
                        </div>

                        <!-- Section E: Exposure & Travel History -->
                        <div v-show="currentStep === 5" class="space-y-4">
                            <h3 class="text-lg font-semibold text-gray-900 mb-4">Section E: Exposure & Travel History</h3>

                            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                                <div>
                                    <label class="block text-sm font-medium text-gray-700 mb-1">Place of Exposure</label>
                                    <input v-model="form.place_of_exposure" type="text" class="w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500" />
                                </div>

                                <div>
                                    <label class="block text-sm font-medium text-gray-700 mb-1">Date of Exposure</label>
                                    <input v-model="form.date_of_exposure" type="date" class="w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500" />
                                </div>
                            </div>

                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-1">Travel History (14 days prior)</label>
                                <textarea v-model="form.travel_history" rows="3" placeholder="List all travel destinations in the 14 days before onset of symptoms" class="w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500"></textarea>
                            </div>

                            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                                <div>
                                    <label class="block text-sm font-medium text-gray-700 mb-1">Date of Departure</label>
                                    <input v-model="form.travel_departure_date" type="date" class="w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500" />
                                </div>

                                <div>
                                    <label class="block text-sm font-medium text-gray-700 mb-1">Date of Return</label>
                                    <input v-model="form.travel_return_date" type="date" class="w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500" />
                                </div>
                            </div>

                            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                                <div>
                                    <label class="block text-sm font-medium text-gray-700 mb-1">Mode of Exposure</label>
                                    <select v-model="form.mode_of_exposure" class="w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500">
                                        <option value="">Select Mode</option>
                                        <option value="Contact">Contact</option>
                                        <option value="Foodborne">Foodborne</option>
                                        <option value="Waterborne">Waterborne</option>
                                        <option value="Airborne">Airborne</option>
                                        <option value="Environmental">Environmental</option>
                                        <option value="Unknown">Unknown</option>
                                    </select>
                                </div>

                                <div>
                                    <label class="block text-sm font-medium text-gray-700 mb-1">Possible Source of Infection</label>
                                    <select v-model="form.source_of_infection" class="w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500">
                                        <option value="">Select Source</option>
                                        <option value="Human">Human</option>
                                        <option value="Animal">Animal</option>
                                        <option value="Water">Water</option>
                                        <option value="Food">Food</option>
                                        <option value="Unknown">Unknown</option>
                                    </select>
                                </div>
                            </div>
                        </div>

                        <!-- Section F: Contact Tracing -->
                        <div v-show="currentStep === 6" class="space-y-4">
                            <h3 class="text-lg font-semibold text-gray-900 mb-4">Section F: Contact Tracing</h3>

                            <div>
                                <label class="flex items-center">
                                    <input v-model="form.contacts_identified" type="checkbox" class="mr-2 rounded" />
                                    <span class="text-sm font-medium text-gray-700">Contacts Identified</span>
                                </label>
                            </div>

                            <div v-if="form.contacts_identified" class="space-y-4">
                                <div>
                                    <label class="block text-sm font-medium text-gray-700 mb-1">Number of Contacts</label>
                                    <input v-model.number="form.number_of_contacts" type="number" min="0" class="w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500" />
                                </div>

                                <div>
                                    <label class="block text-sm font-medium text-gray-700 mb-1">Contact Names/IDs</label>
                                    <textarea v-model="form.contact_details" rows="3" placeholder="List contact names and identifiers" class="w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500"></textarea>
                                </div>

                                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                                    <div>
                                        <label class="block text-sm font-medium text-gray-700 mb-1">Relationship to Case</label>
                                        <select v-model="form.relationship_to_case" class="w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500">
                                            <option value="">Select Relationship</option>
                                            <option value="Family">Family</option>
                                            <option value="Co-worker">Co-worker</option>
                                            <option value="Schoolmate">Schoolmate</option>
                                            <option value="Neighbor">Neighbor</option>
                                            <option value="Healthcare Worker">Healthcare Worker</option>
                                            <option value="Other">Other</option>
                                        </select>
                                    </div>

                                    <div>
                                        <label class="block text-sm font-medium text-gray-700 mb-1">Date Contacted</label>
                                        <input v-model="form.date_contacted" type="date" class="w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500" />
                                    </div>
                                </div>

                                <div>
                                    <label class="block text-sm font-medium text-gray-700 mb-1">Quarantine Status</label>
                                    <select v-model="form.quarantine_status" class="w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500">
                                        <option value="">Select Status</option>
                                        <option value="Home">Home Quarantine</option>
                                        <option value="Facility">Facility Quarantine</option>
                                        <option value="Completed">Completed</option>
                                        <option value="None">None</option>
                                    </select>
                                </div>
                            </div>
                        </div>

                        <!-- Section G: Reporting Information -->
                        <div v-show="currentStep === 7" class="space-y-4">
                            <h3 class="text-lg font-semibold text-gray-900 mb-4">Section G: Reporting Information</h3>

                            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                                <div>
                                    <label class="block text-sm font-medium text-gray-700 mb-1">Name of Reporting Health Worker <span class="text-red-500">*</span></label>
                                    <input v-model="form.reporting_health_worker"
                                           type="text"
                                           :required="shouldRequireFields"
                                           :disabled="isEncoder"
                                           :class="{
                                               'w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500': true,
                                               'bg-gray-100 cursor-not-allowed': isEncoder,
                                               'hover:bg-gray-50': !isEncoder
                                           }" />
                                    <p v-if="isEncoder" class="mt-1 text-xs text-gray-500">
                                        Auto-filled with your account name
                                    </p>
                                </div>

                                <div>
                                    <label class="block text-sm font-medium text-gray-700 mb-1">Designation</label>
                                    <select v-model="form.health_worker_designation"
                                            :disabled="isEncoder"
                                            :class="{
                                                'w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500': true,
                                                'bg-gray-100 cursor-not-allowed': isEncoder,
                                                'hover:bg-gray-50': !isEncoder
                                            }">
                                        <option value="">Select Designation</option>
                                        <option value="Nurse">Nurse</option>
                                        <option value="Medical Technologist">Medical Technologist</option>
                                        <option value="Physician">Physician</option>
                                        <option value="Disease Surveillance Officer">Disease Surveillance Officer</option>
                                        <option value="Other">Other</option>
                                    </select>
                                    <p v-if="isEncoder" class="mt-1 text-xs text-gray-500">
                                        Auto-filled with your account position
                                    </p>
                                </div>
                            </div>

                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-1">Contact Number / Email</label>
                                <input v-model="form.health_worker_contact"
                                       type="text"
                                       :disabled="isEncoder"
                                       :class="{
                                           'w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500': true,
                                           'bg-gray-100 cursor-not-allowed': isEncoder,
                                           'hover:bg-gray-50': !isEncoder
                                       }" />
                                <p v-if="isEncoder" class="mt-1 text-xs text-gray-500">
                                    Auto-filled with your account contact number
                                </p>
                            </div>

                            <div class="bg-blue-50 border border-blue-200 rounded-lg p-4">
                                <h4 class="text-sm font-semibold text-blue-900 mb-2">Summary</h4>
                                <dl class="grid grid-cols-2 gap-2 text-sm">
                                    <dt class="text-blue-700">Patient:</dt>
                                    <dd class="text-blue-900 font-medium">{{ form.first_name }} {{ form.last_name }}</dd>
                                    <dt class="text-blue-700">Disease:</dt>
                                    <dd class="text-blue-900 font-medium">{{ diseases.find(d => d.id === parseInt(form.disease_id as any))?.name }}</dd>
                                    <dt class="text-blue-700">Municipality:</dt>
                                    <dd class="text-blue-900 font-medium">{{ municipalities.find(m => m.id === parseInt(form.municipality_id as any))?.name }}</dd>
                                    <dt class="text-blue-700">Date Reported:</dt>
                                    <dd class="text-blue-900 font-medium">{{ form.date_reported }}</dd>
                                </dl>
                            </div>
                        </div>

                        <!-- Modern Navigation Buttons -->
                        <div class="bg-gray-50 px-8 py-6 border-t-2 border-gray-100">
                            <div class="flex items-center justify-between">
                                <!-- Previous Button -->
                                <button
                                    v-if="currentStep > 1"
                                    @click.prevent="prevStep"
                                    type="button"
                                    class="group inline-flex items-center px-6 py-3 border-2 border-gray-300 rounded-xl shadow-sm text-sm font-semibold text-gray-700 bg-white hover:bg-gray-50 hover:border-gray-400 focus:outline-none focus:ring-4 focus:ring-gray-200 transition-all duration-200 transform hover:scale-105"
                                >
                                    <svg class="w-5 h-5 mr-2 transition-transform group-hover:-translate-x-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7" />
                                    </svg>
                                    Previous Step
                                </button>
                                <div v-else></div>

                                <!-- Right Side Buttons -->
                                <div class="flex items-center gap-3">
                                    <!-- Save Draft Button -->
                                    <button
                                        @click.prevent="saveDraft"
                                        type="button"
                                        class="group inline-flex items-center px-6 py-3 border-2 border-gray-300 rounded-xl shadow-sm text-sm font-semibold text-gray-700 bg-white hover:bg-gray-50 hover:border-gray-400 focus:outline-none focus:ring-4 focus:ring-gray-200 transition-all duration-200"
                                        :disabled="form.processing"
                                    >
                                        <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7H5a2 2 0 00-2 2v9a2 2 0 002 2h14a2 2 0 002-2V9a2 2 0 00-2-2h-3m-1 4l-3 3m0 0l-3-3m3 3V4" />
                                        </svg>
                                        {{ form.processing ? 'Saving...' : 'Save as Draft' }}
                                    </button>

                                    <!-- Next Button -->
                                    <button
                                        v-if="currentStep < totalSteps"
                                        @click.prevent="nextStep"
                                        type="button"
                                        class="group inline-flex items-center px-8 py-3 border-2 border-transparent rounded-xl shadow-lg text-sm font-bold text-white bg-gradient-to-r from-blue-600 to-indigo-600 hover:from-blue-700 hover:to-indigo-700 focus:outline-none focus:ring-4 focus:ring-blue-300 transition-all duration-200 transform hover:scale-105"
                                    >
                                        Next Step
                                        <svg class="w-5 h-5 ml-2 transition-transform group-hover:translate-x-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7" />
                                        </svg>
                                    </button>

                                    <!-- Submit Button -->
                                    <button
                                        v-if="currentStep === totalSteps"
                                        @click.prevent="submitForm"
                                        type="button"
                                        class="group inline-flex items-center px-8 py-3 border-2 border-transparent rounded-xl shadow-lg text-sm font-bold text-white bg-gradient-to-r from-green-600 to-emerald-600 hover:from-green-700 hover:to-emerald-700 focus:outline-none focus:ring-4 focus:ring-green-300 transition-all duration-200 transform hover:scale-105"
                                        :disabled="form.processing"
                                    >
                                        <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                                        </svg>
                                        {{ form.processing ? 'Submitting...' : 'Submit Case Report' }}
                                    </button>
                                </div>
                            </div>

                            <!-- Progress Text -->
                            <div class="mt-4 text-center">
                                <p class="text-sm text-gray-600">
                                    <span class="font-semibold text-blue-600">Step {{ currentStep }} of {{ totalSteps }}</span>
                                    <span class="mx-2">•</span>
                                    <span v-if="currentStep < totalSteps">{{ Math.round((currentStep / totalSteps) * 100) }}% Complete</span>
                                    <span v-else class="text-green-600 font-semibold">Ready to Submit!</span>
                                </p>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </AuthenticatedLayout>
</template>
