<script setup lang="ts">
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import { Head, useForm } from '@inertiajs/vue3';
import { ref, computed } from 'vue';

interface CaseReport {
    id: number;
    case_id: string;
    disease_id: number;
    disease_other?: string;
    date_reported: string;
    case_classification: string;
    outcome: string;
    last_name: string;
    first_name: string;
    middle_name?: string;
    sex: string;
    date_of_birth: string;
    civil_status: string;
    occupation?: string;
    address: string;
    barangay_id: number;
    municipality_id: number;
    contact_number?: string;
    pregnancy_status: string;
    philhealth_number?: string;
    nationality: string;
    date_of_onset: string;
    date_of_consultation?: string;
    admitting_facility_id?: number;
    signs_symptoms?: string[];
    signs_symptoms_other?: string;
    clinical_classification?: string;
    complications?: string;
    clinical_outcome: string;
    date_of_death?: string;
    final_diagnosis?: string;
    clinical_remarks?: string;
    // Laboratory Information
    specimen_collection_date?: string;
    specimen_type?: string;
    laboratory_test?: string;
    laboratory_result_date?: string;
    laboratory_result?: string;
    testing_laboratory?: string;
    // Exposure & Travel History
    place_of_exposure?: string;
    date_of_exposure?: string;
    travel_history?: string;
    travel_departure_date?: string;
    travel_return_date?: string;
    mode_of_exposure?: string;
    source_of_infection?: string;
    // Contact Tracing
    contacts_identified?: boolean;
    number_of_contacts?: number;
    contact_details?: string;
    relationship_to_case?: string;
    date_contacted?: string;
    quarantine_status?: string;
    // Reporting Information
    reporting_health_worker: string;
    health_worker_designation: string;
    health_worker_contact?: string;
    status: string;
    // Validator feedback
    validator_feedback?: string;
    returned_by?: number;
    returned_at?: string;
}

interface Props {
    caseReport: CaseReport;
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

// Determine if fields should be required (not required for drafts)
const isSubmitting = ref(false);
const shouldRequireFields = computed(() => isSubmitting.value);

const form = useForm({
    // Section A
    disease_id: props.caseReport.disease_id?.toString() || '',
    disease_other: props.caseReport.disease_other || '',
    date_reported: props.caseReport.date_reported || '',
    case_classification: props.caseReport.case_classification || 'Suspect',
    outcome: props.caseReport.outcome || 'Alive',
    submitAction: 'draft' as 'draft' | 'submit',

    // Section B
    last_name: props.caseReport.last_name || '',
    first_name: props.caseReport.first_name || '',
    middle_name: props.caseReport.middle_name || '',
    sex: props.caseReport.sex || 'Male',
    date_of_birth: props.caseReport.date_of_birth || '',
    civil_status: props.caseReport.civil_status || 'Single',
    occupation: props.caseReport.occupation || '',
    address: props.caseReport.address || '',
    barangay_id: props.caseReport.barangay_id?.toString() || '',
    municipality_id: props.caseReport.municipality_id?.toString() || '',
    contact_number: props.caseReport.contact_number || '',
    pregnancy_status: props.caseReport.pregnancy_status || 'N/A',
    philhealth_number: props.caseReport.philhealth_number || '',
    nationality: props.caseReport.nationality || 'Filipino',

    // Section C
    date_of_onset: props.caseReport.date_of_onset || '',
    date_of_consultation: props.caseReport.date_of_consultation || '',
    admitting_facility_id: props.caseReport.admitting_facility_id?.toString() || '',
    signs_symptoms: props.caseReport.signs_symptoms || [],
    signs_symptoms_other: props.caseReport.signs_symptoms_other || '',
    clinical_classification: props.caseReport.clinical_classification || '',
    complications: props.caseReport.complications || '',
    clinical_outcome: props.caseReport.clinical_outcome || 'Alive',
    date_of_death: props.caseReport.date_of_death || '',
    final_diagnosis: props.caseReport.final_diagnosis || '',
    clinical_remarks: props.caseReport.clinical_remarks || '',

    // Section D - Laboratory Information
    specimen_collection_date: props.caseReport.specimen_collection_date || '',
    specimen_type: props.caseReport.specimen_type || '',
    laboratory_test: props.caseReport.laboratory_test || '',
    laboratory_result_date: props.caseReport.laboratory_result_date || '',
    laboratory_result: props.caseReport.laboratory_result || '',
    testing_laboratory: props.caseReport.testing_laboratory || '',
    laboratory_file: null as File | null,

    // Section E - Exposure & Travel History
    place_of_exposure: props.caseReport.place_of_exposure || '',
    date_of_exposure: props.caseReport.date_of_exposure || '',
    travel_history: props.caseReport.travel_history || '',
    travel_departure_date: props.caseReport.travel_departure_date || '',
    travel_return_date: props.caseReport.travel_return_date || '',
    mode_of_exposure: props.caseReport.mode_of_exposure || '',
    source_of_infection: props.caseReport.source_of_infection || '',

    // Section F - Contact Tracing
    contacts_identified: props.caseReport.contacts_identified || false,
    number_of_contacts: props.caseReport.number_of_contacts || 0,
    contact_details: props.caseReport.contact_details || '',
    relationship_to_case: props.caseReport.relationship_to_case || '',
    date_contacted: props.caseReport.date_contacted || '',
    quarantine_status: props.caseReport.quarantine_status || '',

    // Section G - Reporting Information
    reporting_health_worker: props.caseReport.reporting_health_worker || '',
    health_worker_designation: props.caseReport.health_worker_designation || '',
    health_worker_contact: props.caseReport.health_worker_contact || '',
});

const filteredBarangays = computed(() => {
    if (!form.municipality_id) return [];
    return props.barangays.filter(b => b.municipality_id === parseInt(form.municipality_id as any));
});

const formatReturnedDateTime = (dateTimeString: string) => {
    // Parse the datetime string from the database (assumed to be in UTC)
    const date = new Date(dateTimeString + 'Z'); // Adding 'Z' to explicitly treat as UTC

    // Format to a more readable local time
    const options: Intl.DateTimeFormatOptions = {
        year: 'numeric',
        month: 'short',
        day: 'numeric',
        hour: 'numeric',
        minute: '2-digit',
        hour12: true,
        timeZoneName: 'short'
    };

    return date.toLocaleString('en-US', options);
};

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
        form.put(route('case-reports.update', props.caseReport.id), {
            preserveScroll: true,
            onError: (errors) => {
                console.error('Draft save errors:', errors);
            }
        });
    }, 10);
};

const handleFileUpload = (event: Event) => {
    const target = event.target as HTMLInputElement;
    const file = target.files?.[0];

    if (file) {
        // Check file size (5MB limit)
        if (file.size > 5 * 1024 * 1024) {
            alert('File size must be less than 5MB');
            target.value = '';
            return;
        }

        // Check file type
        const allowedTypes = ['application/pdf', 'image/jpeg', 'image/jpg', 'image/png'];
        if (!allowedTypes.includes(file.type)) {
            alert('Please upload a PDF, JPG, or PNG file');
            target.value = '';
            return;
        }

        form.laboratory_file = file;
    }
};

const submitForm = () => {
    isSubmitting.value = true;
    form.submitAction = 'submit';
    form.put(route('case-reports.update', props.caseReport.id), {
        preserveScroll: true,
        onError: (errors) => {
            console.error('Submit errors:', errors);
        }
    });
};
</script>

<template>
    <Head :title="`Edit Case Report - ${props.caseReport.case_id}`" />

    <AuthenticatedLayout>
        <template #header>
            <div class="flex items-center justify-between">
                <div>
                    <h2 class="text-2xl font-bold text-gray-900 sm:text-3xl">
                        Edit Case Report
                    </h2>
                    <p class="mt-2 text-sm text-gray-600">
                        Case ID: {{ props.caseReport.case_id }} | Status: {{ props.caseReport.status }}
                    </p>
                </div>
            </div>
        </template>

        <div class="py-12">
            <div class="mx-auto max-w-7xl sm:px-6 lg:px-8">

                <!-- Validator Feedback (when returned) -->
                <div v-if="props.caseReport.status === 'returned' && props.caseReport.validator_feedback"
                     class="mb-6 overflow-hidden bg-red-50 border border-red-200 shadow-sm sm:rounded-lg">
                    <div class="p-6">
                        <div class="flex items-start">
                            <div class="flex-shrink-0">
                                <svg class="w-6 h-6 text-red-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-2.5L13.732 4c-.77-.833-1.964-.833-2.732 0L3.732 16c-.77.833.192 2.5 1.732 2.5z" />
                                </svg>
                            </div>
                            <div class="ml-3 flex-1">
                                <h3 class="text-lg font-medium text-red-900">
                                    Report Returned for Corrections
                                </h3>
                                <div class="mt-2 text-sm text-red-700">
                                    <p class="mb-2">This report has been returned by the validator with the following feedback:</p>
                                    <div class="p-3 bg-white border border-red-200 rounded-md">
                                        <p class="whitespace-pre-wrap">{{ props.caseReport.validator_feedback }}</p>
                                    </div>
                                    <p v-if="props.caseReport.returned_at" class="mt-2 text-xs text-red-600">
                                        Returned on: {{ formatReturnedDateTime(props.caseReport.returned_at) }}
                                    </p>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Progress Indicator -->
                <div class="mb-8 bg-white overflow-hidden shadow-xl rounded-2xl border border-gray-100">
                    <div class="px-8 py-6 bg-gradient-to-r from-blue-50 to-indigo-50">
                        <div class="flex items-center justify-between">
                            <div
                                v-for="step in availableSteps"
                                :key="step"
                                class="flex items-center"
                                :class="availableSteps.indexOf(step) < availableSteps.length - 1 ? 'flex-1' : ''"
                            >
                                <div class="flex flex-col items-center">
                                    <div
                                        class="flex items-center justify-center w-12 h-12 rounded-full border-2 font-bold text-sm transition-all duration-300 cursor-pointer hover:scale-105"
                                        :class="availableSteps.indexOf(step) <= availableSteps.indexOf(currentStep)
                                            ? 'bg-blue-600 border-blue-600 text-white shadow-lg hover:bg-blue-700'
                                            : 'bg-white border-gray-300 text-gray-400 hover:border-gray-400 hover:text-gray-600'"
                                        @click="goToStep(step)"
                                    >
                                        {{ step }}
                                    </div>
                                    <div class="mt-3 text-center cursor-pointer" @click="goToStep(step)">
                                        <div
                                            class="text-sm font-medium transition-colors duration-300 hover:text-blue-800"
                                            :class="step <= currentStep ? 'text-blue-700' : 'text-gray-500 hover:text-gray-700'"
                                        >
                                            {{
                                                step === 1 ? 'Case ID' :
                                                step === 2 ? 'Patient' :
                                                step === 3 ? 'Clinical' :
                                                step === 4 ? 'Laboratory' :
                                                step === 5 ? 'Exposure' :
                                                step === 6 ? 'Contacts' :
                                                'Reporting'
                                            }}
                                        </div>
                                        <div
                                            class="text-xs mt-1 transition-colors duration-300"
                                            :class="step <= currentStep ? 'text-blue-600' : 'text-gray-400'"
                                        >
                                            {{ step <= currentStep ? '● Active' : '○ Pending' }}
                                        </div>
                                    </div>
                                </div>
                                <div
                                    v-if="step < totalSteps"
                                    class="flex-1 h-1 mx-4 rounded-full transition-all duration-300"
                                    :class="step < currentStep ? 'bg-blue-600' : 'bg-gray-200'"
                                ></div>
                            </div>
                        </div>
                        <div class="mt-6 text-center">
                            <p class="text-sm text-gray-600">Complete all required sections to submit case report</p>
                        </div>
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
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                                        </svg>
                                    </div>
                                    <div>
                                        <h3 class="text-2xl font-bold text-gray-900">Case Identification</h3>
                                        <p class="text-gray-600 mt-1">Enter basic case information and classification</p>
                                    </div>
                                </div>
                                <div class="h-1 bg-gradient-to-r from-blue-500 to-blue-600 rounded-full"></div>
                            </div>

                            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                                <div>
                                    <label class="block text-sm font-semibold text-gray-700 mb-2">Disease *</label>
                                    <select v-model="form.disease_id" :required="shouldRequireFields"
                                        class="w-full rounded-lg border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 transition-colors duration-200">
                                        <option value="">Select Disease</option>
                                        <optgroup v-for="group in groupedDiseases" :key="group.category" :label="group.category">
                                            <option v-for="disease in group.diseases" :key="disease.id" :value="disease.id">
                                                {{ disease.name }}
                                            </option>
                                        </optgroup>
                                        <option value="other">Other (Specify)</option>
                                    </select>

                                    <!-- Disease Category Information -->
                                    <div v-if="selectedDiseaseInfo && selectedDiseaseInfo.isNonContagious"
                                         class="mt-3 p-3 bg-amber-50 border border-amber-200 rounded-lg">
                                        <div class="flex items-start">
                                            <div class="flex-shrink-0">
                                                <svg class="w-4 h-4 text-amber-600 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                                                </svg>
                                            </div>
                                            <div class="ml-3">
                                                <h4 class="text-xs font-medium text-amber-800">Non-Contagious Disease</h4>
                                                <p class="text-xs text-amber-700 mt-1">
                                                    <strong>{{ selectedDiseaseInfo.name }}</strong> is non-contagious.
                                                    Exposure and Contact sections are skipped.
                                                </p>
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                <div>
                                    <label class="block text-sm font-semibold text-gray-700 mb-2">Date Reported *</label>
                                    <input v-model="form.date_reported" type="date" :required="shouldRequireFields"
                                        class="w-full rounded-lg border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 transition-colors duration-200" />
                                </div>

                                <div>
                                    <label class="block text-sm font-semibold text-gray-700 mb-2">Case Classification *</label>
                                    <select v-model="form.case_classification" :required="shouldRequireFields"
                                        class="w-full rounded-lg border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 transition-colors duration-200">
                                        <option value="Suspect">Suspect</option>
                                        <option value="Confirmed">Confirmed</option>
                                    </select>
                                </div>

                                <div>
                                    <label class="block text-sm font-semibold text-gray-700 mb-2">Outcome *</label>
                                    <select v-model="form.outcome" :required="shouldRequireFields"
                                        class="w-full rounded-lg border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 transition-colors duration-200">
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
                                            Municipality is locked. Case is routed to the validator of this municipality
                                        </p>
                                    </div>

                                    <div>
                                        <label class="block text-sm font-semibold text-gray-700 mb-2">
                                            Barangay <span class="text-red-500">*</span>
                                        </label>
                                        <select v-model="form.barangay_id" :required="shouldRequireFields"
                                                class="w-full rounded-xl border-gray-300 shadow-sm focus:border-green-500 focus:ring-2 focus:ring-green-200 transition-all duration-200 py-3 px-4"
                                                :disabled="!form.municipality_id">
                                            <option value="">Select Barangay</option>
                                            <option v-for="barangay in filteredBarangays" :key="barangay.id" :value="barangay.id">
                                                {{ barangay.name }}
                                            </option>
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

                                <!-- Pregnancy Status (conditional) -->
                                <div v-if="form.sex === 'Female'" class="p-4 bg-pink-50 rounded-xl border border-pink-200">
                                    <label class="block text-sm font-semibold text-gray-700 mb-3">Pregnancy Status</label>
                                    <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                                        <label class="flex items-center px-4 py-3 bg-white rounded-xl cursor-pointer hover:bg-pink-100 transition-colors duration-200 border">
                                            <input v-model="form.pregnancy_status" type="radio" value="Pregnant" class="text-pink-600 focus:ring-pink-500 mr-2" />
                                            <span class="text-sm font-medium text-gray-700">Pregnant</span>
                                        </label>
                                        <label class="flex items-center px-4 py-3 bg-white rounded-xl cursor-pointer hover:bg-pink-100 transition-colors duration-200 border">
                                            <input v-model="form.pregnancy_status" type="radio" value="Not Pregnant" class="text-pink-600 focus:ring-pink-500 mr-2" />
                                            <span class="text-sm font-medium text-gray-700">Not Pregnant</span>
                                        </label>
                                        <label class="flex items-center px-4 py-3 bg-white rounded-xl cursor-pointer hover:bg-pink-100 transition-colors duration-200 border">
                                            <input v-model="form.pregnancy_status" type="radio" value="Unknown" class="text-pink-600 focus:ring-pink-500 mr-2" />
                                            <span class="text-sm font-medium text-gray-700">Unknown</span>
                                        </label>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Section C: Clinical Information -->
                        <div v-show="currentStep === 3" class="p-8">
                            <div class="mb-6">
                                <div class="flex items-center mb-4">
                                    <div class="p-3 bg-purple-100 rounded-xl mr-4">
                                        <svg class="w-7 h-7 text-purple-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                                        </svg>
                                    </div>
                                    <div>
                                        <h3 class="text-2xl font-bold text-gray-900">Clinical Information</h3>
                                        <p class="text-sm text-gray-600 mt-1">Medical history and clinical details</p>
                                    </div>
                                </div>
                                <div class="h-1 bg-gradient-to-r from-purple-500 to-purple-600 rounded-full"></div>
                            </div>

                            <div class="space-y-6">
                                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                                    <div>
                                        <label class="block text-sm font-semibold text-gray-700 mb-2">Date of Onset of Illness <span class="text-red-500">*</span></label>
                                        <input v-model="form.date_of_onset" type="date" :required="shouldRequireFields"
                                               class="w-full rounded-xl border-gray-300 shadow-sm focus:border-purple-500 focus:ring-2 focus:ring-purple-200 transition-all duration-200 py-3 px-4" />
                                    </div>

                                    <div>
                                        <label class="block text-sm font-semibold text-gray-700 mb-2">Date of Consultation</label>
                                        <input v-model="form.date_of_consultation" type="date"
                                               class="w-full rounded-xl border-gray-300 shadow-sm focus:border-purple-500 focus:ring-2 focus:ring-purple-200 transition-all duration-200 py-3 px-4" />
                                    </div>
                                </div>

                                <div>
                                    <label class="block text-sm font-semibold text-gray-700 mb-2">Admitting Facility</label>
                                    <select v-model="form.admitting_facility_id"
                                            class="w-full rounded-xl border-gray-300 shadow-sm focus:border-purple-500 focus:ring-2 focus:ring-purple-200 transition-all duration-200 py-3 px-4">
                                        <option value="">Select Facility</option>
                                        <optgroup v-for="type in ['District Hospital', 'Provincial Hospital', 'RHU']" :key="type" :label="type">
                                            <option v-for="facility in facilities.filter(f => f.type === type)" :key="facility.id" :value="facility.id">
                                                {{ facility.name }}
                                            </option>
                                        </optgroup>
                                    </select>
                                </div>

                                <div>
                                    <label class="block text-sm font-semibold text-gray-700 mb-3">Signs and Symptoms</label>
                                    <div class="grid grid-cols-2 md:grid-cols-3 gap-3">
                                        <label v-for="symptom in commonSymptoms" :key="symptom"
                                               class="flex items-center px-4 py-3 bg-gray-50 rounded-xl cursor-pointer hover:bg-purple-50 transition-colors duration-200">
                                            <input v-model="form.signs_symptoms" type="checkbox" :value="symptom"
                                                   class="text-purple-600 focus:ring-purple-500 mr-3 rounded" />
                                            <span class="text-sm font-medium text-gray-700">{{ symptom }}</span>
                                        </label>
                                    </div>
                                    <div class="mt-4">
                                        <label class="block text-sm font-semibold text-gray-700 mb-2">Other Symptoms</label>
                                        <input v-model="form.signs_symptoms_other" type="text" placeholder="Specify other symptoms"
                                               class="w-full rounded-xl border-gray-300 shadow-sm focus:border-purple-500 focus:ring-2 focus:ring-purple-200 transition-all duration-200 py-3 px-4" />
                                    </div>
                                </div>

                                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                                    <div>
                                        <label class="block text-sm font-semibold text-gray-700 mb-2">Clinical Outcome</label>
                                        <select v-model="form.clinical_outcome"
                                                class="w-full rounded-xl border-gray-300 shadow-sm focus:border-purple-500 focus:ring-2 focus:ring-purple-200 transition-all duration-200 py-3 px-4">
                                            <option value="Alive">Alive</option>
                                            <option value="Died">Died</option>
                                            <option value="Unknown">Unknown</option>
                                        </select>
                                    </div>

                                    <div>
                                        <label class="block text-sm font-semibold text-gray-700 mb-2">Clinical Classification</label>
                                        <input v-model="form.clinical_classification" type="text"
                                               class="w-full rounded-xl border-gray-300 shadow-sm focus:border-purple-500 focus:ring-2 focus:ring-purple-200 transition-all duration-200 py-3 px-4"
                                               placeholder="Enter clinical classification" />
                                    </div>
                                </div>

                                <div>
                                    <label class="block text-sm font-semibold text-gray-700 mb-2">Complications</label>
                                    <textarea v-model="form.complications" rows="3"
                                              class="w-full rounded-xl border-gray-300 shadow-sm focus:border-purple-500 focus:ring-2 focus:ring-purple-200 transition-all duration-200 py-3 px-4"
                                              placeholder="Describe any complications"></textarea>
                                </div>
                            </div>
                        </div>

                        <!-- Section D: Laboratory Information -->
                        <div v-show="currentStep === 4" class="p-8">
                            <div class="mb-6">
                                <div class="flex items-center mb-4">
                                    <div class="p-3 bg-orange-100 rounded-xl mr-4">
                                        <svg class="w-7 h-7 text-orange-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19.428 15.428a2 2 0 00-1.022-.547l-2.387-.477a6 6 0 00-3.86.517l-.318.158a6 6 0 01-3.86.517L6.05 15.21a2 2 0 00-1.806.547M8 4h8l-1 1v5.172a2 2 0 00.586 1.414l5 5c1.26 1.26.367 3.414-1.415 3.414H4.828c-1.782 0-2.674-2.154-1.414-3.414l5-5A2 2 0 009 10.172V5L8 4z"/>
                                        </svg>
                                    </div>
                                    <div>
                                        <h3 class="text-2xl font-bold text-gray-900">Laboratory Information</h3>
                                        <p class="text-sm text-gray-600 mt-1">Laboratory tests and results</p>
                                    </div>
                                </div>
                                <div class="h-1 bg-gradient-to-r from-orange-500 to-orange-600 rounded-full"></div>
                            </div>

                            <div class="space-y-6">
                                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                                    <div>
                                        <label class="block text-sm font-semibold text-gray-700 mb-2">Date of Specimen Collection</label>
                                        <input v-model="form.specimen_collection_date" type="date"
                                               class="w-full rounded-xl border-gray-300 shadow-sm focus:border-orange-500 focus:ring-2 focus:ring-orange-200 transition-all duration-200 py-3 px-4" />
                                    </div>

                                    <div>
                                        <label class="block text-sm font-semibold text-gray-700 mb-2">Specimen Type</label>
                                        <select v-model="form.specimen_type"
                                                class="w-full rounded-xl border-gray-300 shadow-sm focus:border-orange-500 focus:ring-2 focus:ring-orange-200 transition-all duration-200 py-3 px-4">
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

                                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                                    <div>
                                        <label class="block text-sm font-semibold text-gray-700 mb-2">Laboratory Test Done</label>
                                        <select v-model="form.laboratory_test"
                                                class="w-full rounded-xl border-gray-300 shadow-sm focus:border-orange-500 focus:ring-2 focus:ring-orange-200 transition-all duration-200 py-3 px-4">
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
                                        <label class="block text-sm font-semibold text-gray-700 mb-2">Date of Laboratory Result</label>
                                        <input v-model="form.laboratory_result_date" type="date"
                                               class="w-full rounded-xl border-gray-300 shadow-sm focus:border-orange-500 focus:ring-2 focus:ring-orange-200 transition-all duration-200 py-3 px-4" />
                                    </div>
                                </div>

                                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                                    <div>
                                        <label class="block text-sm font-semibold text-gray-700 mb-2">Laboratory Result</label>
                                        <select v-model="form.laboratory_result"
                                                class="w-full rounded-xl border-gray-300 shadow-sm focus:border-orange-500 focus:ring-2 focus:ring-orange-200 transition-all duration-200 py-3 px-4">
                                            <option value="">Select Result</option>
                                            <option value="Positive">Positive</option>
                                            <option value="Negative">Negative</option>
                                            <option value="Pending">Pending</option>
                                        </select>
                                    </div>

                                    <div>
                                        <label class="block text-sm font-semibold text-gray-700 mb-2">Testing Laboratory Name</label>
                                        <input v-model="form.testing_laboratory" type="text"
                                               class="w-full rounded-xl border-gray-300 shadow-sm focus:border-orange-500 focus:ring-2 focus:ring-orange-200 transition-all duration-200 py-3 px-4"
                                               placeholder="Enter laboratory name" />
                                    </div>
                                </div>

                                <div>
                                    <label class="block text-sm font-semibold text-gray-700 mb-2">Upload Laboratory Result (PDF/Image, max 5MB)</label>
                                    <input @change="handleFileUpload" type="file" accept=".pdf,.jpg,.jpeg,.png"
                                           class="w-full rounded-xl border-gray-300 shadow-sm focus:border-orange-500 focus:ring-2 focus:ring-orange-200 transition-all duration-200 py-3 px-4" />
                                    <p class="text-xs text-gray-500 mt-1">Accepted formats: PDF, JPG, PNG (Max 5MB)</p>
                                </div>
                            </div>
                        </div>

                        <!-- Section E: Exposure Information -->
                        <div v-show="currentStep === 5" class="p-8">
                            <div class="mb-6">
                                <div class="flex items-center mb-4">
                                    <div class="p-3 bg-red-100 rounded-xl mr-4">
                                        <svg class="w-7 h-7 text-red-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"/>
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"/>
                                        </svg>
                                    </div>
                                    <div>
                                        <h3 class="text-2xl font-bold text-gray-900">Exposure Information</h3>
                                        <p class="text-sm text-gray-600 mt-1">Travel history and exposure details</p>
                                    </div>
                                </div>
                                <div class="h-1 bg-gradient-to-r from-red-500 to-red-600 rounded-full"></div>
                            </div>

                            <div class="space-y-6">
                                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                                    <div>
                                        <label class="block text-sm font-semibold text-gray-700 mb-2">Place of Exposure</label>
                                        <input v-model="form.place_of_exposure" type="text"
                                               class="w-full rounded-xl border-gray-300 shadow-sm focus:border-red-500 focus:ring-2 focus:ring-red-200 transition-all duration-200 py-3 px-4"
                                               placeholder="Enter place of exposure" />
                                    </div>

                                    <div>
                                        <label class="block text-sm font-semibold text-gray-700 mb-2">Date of Exposure</label>
                                        <input v-model="form.date_of_exposure" type="date"
                                               class="w-full rounded-xl border-gray-300 shadow-sm focus:border-red-500 focus:ring-2 focus:ring-red-200 transition-all duration-200 py-3 px-4" />
                                    </div>
                                </div>

                                <div>
                                    <label class="block text-sm font-semibold text-gray-700 mb-2">Travel History (14 days prior)</label>
                                    <textarea v-model="form.travel_history" rows="3"
                                              class="w-full rounded-xl border-gray-300 shadow-sm focus:border-red-500 focus:ring-2 focus:ring-red-200 transition-all duration-200 py-3 px-4"
                                              placeholder="List all travel destinations in the 14 days before onset of symptoms"></textarea>
                                </div>

                                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                                    <div>
                                        <label class="block text-sm font-semibold text-gray-700 mb-2">Date of Departure</label>
                                        <input v-model="form.travel_departure_date" type="date"
                                               class="w-full rounded-xl border-gray-300 shadow-sm focus:border-red-500 focus:ring-2 focus:ring-red-200 transition-all duration-200 py-3 px-4" />
                                    </div>

                                    <div>
                                        <label class="block text-sm font-semibold text-gray-700 mb-2">Date of Return</label>
                                        <input v-model="form.travel_return_date" type="date"
                                               class="w-full rounded-xl border-gray-300 shadow-sm focus:border-red-500 focus:ring-2 focus:ring-red-200 transition-all duration-200 py-3 px-4" />
                                    </div>
                                </div>

                                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                                    <div>
                                        <label class="block text-sm font-semibold text-gray-700 mb-2">Mode of Exposure</label>
                                        <select v-model="form.mode_of_exposure"
                                                class="w-full rounded-xl border-gray-300 shadow-sm focus:border-red-500 focus:ring-2 focus:ring-red-200 transition-all duration-200 py-3 px-4">
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
                                        <label class="block text-sm font-semibold text-gray-700 mb-2">Possible Source of Infection</label>
                                        <select v-model="form.source_of_infection"
                                                class="w-full rounded-xl border-gray-300 shadow-sm focus:border-red-500 focus:ring-2 focus:ring-red-200 transition-all duration-200 py-3 px-4">
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
                        </div>

                        <!-- Section F: Contact Information -->
                        <div v-show="currentStep === 6" class="p-8">
                            <div class="mb-6">
                                <div class="flex items-center mb-4">
                                    <div class="p-3 bg-indigo-100 rounded-xl mr-4">
                                        <svg class="w-7 h-7 text-indigo-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"/>
                                        </svg>
                                    </div>
                                    <div>
                                        <h3 class="text-2xl font-bold text-gray-900">Contact Information</h3>
                                        <p class="text-sm text-gray-600 mt-1">Contact tracing details</p>
                                    </div>
                                </div>
                                <div class="h-1 bg-gradient-to-r from-indigo-500 to-indigo-600 rounded-full"></div>
                            </div>

                            <div class="space-y-6">
                                <div>
                                    <label class="flex items-center">
                                        <input v-model="form.contacts_identified" type="checkbox" class="mr-2 rounded" />
                                        <span class="text-sm font-semibold text-gray-700">Contacts Identified</span>
                                    </label>
                                </div>

                                <div v-if="form.contacts_identified" class="space-y-6">
                                    <div>
                                        <label class="block text-sm font-semibold text-gray-700 mb-2">Number of Contacts</label>
                                        <input v-model.number="form.number_of_contacts" type="number" min="0"
                                               class="w-full rounded-xl border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-2 focus:ring-indigo-200 transition-all duration-200 py-3 px-4" />
                                    </div>

                                    <div>
                                        <label class="block text-sm font-semibold text-gray-700 mb-2">Contact Names/IDs</label>
                                        <textarea v-model="form.contact_details" rows="3"
                                                  class="w-full rounded-xl border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-2 focus:ring-indigo-200 transition-all duration-200 py-3 px-4"
                                                  placeholder="List contact names and identifiers"></textarea>
                                    </div>

                                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                                        <div>
                                            <label class="block text-sm font-semibold text-gray-700 mb-2">Relationship to Case</label>
                                            <select v-model="form.relationship_to_case"
                                                    class="w-full rounded-xl border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-2 focus:ring-indigo-200 transition-all duration-200 py-3 px-4">
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
                                            <label class="block text-sm font-semibold text-gray-700 mb-2">Date Contacted</label>
                                            <input v-model="form.date_contacted" type="date"
                                                   class="w-full rounded-xl border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-2 focus:ring-indigo-200 transition-all duration-200 py-3 px-4" />
                                        </div>
                                    </div>

                                    <div>
                                        <label class="block text-sm font-semibold text-gray-700 mb-2">Quarantine Status</label>
                                        <select v-model="form.quarantine_status"
                                                class="w-full rounded-xl border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-2 focus:ring-indigo-200 transition-all duration-200 py-3 px-4">
                                            <option value="">Select Status</option>
                                            <option value="Home">Home Quarantine</option>
                                            <option value="Facility">Facility Quarantine</option>
                                            <option value="Completed">Completed</option>
                                            <option value="None">None</option>
                                        </select>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Section G: Reporting Information -->
                        <div v-show="currentStep === 7" class="p-8">
                            <div class="mb-6">
                                <div class="flex items-center mb-4">
                                    <div class="p-3 bg-emerald-100 rounded-xl mr-4">
                                        <svg class="w-7 h-7 text-emerald-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                                        </svg>
                                    </div>
                                    <div>
                                        <h3 class="text-2xl font-bold text-gray-900">Reporting Information</h3>
                                        <p class="text-sm text-gray-600 mt-1">Health worker and reporting details</p>
                                    </div>
                                </div>
                                <div class="h-1 bg-gradient-to-r from-emerald-500 to-emerald-600 rounded-full"></div>
                            </div>

                            <div class="space-y-6">
                                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                                    <div>
                                        <label class="block text-sm font-semibold text-gray-700 mb-2">
                                            Reporting Health Worker <span class="text-red-500">*</span>
                                        </label>
                                        <input v-model="form.reporting_health_worker"
                                               type="text"
                                               :required="shouldRequireFields"
                                               :disabled="isEncoder"
                                               :class="{
                                                   'w-full rounded-xl border-gray-300 shadow-sm focus:border-emerald-500 focus:ring-2 focus:ring-emerald-200 transition-all duration-200 py-3 px-4': true,
                                                   'bg-gray-100 cursor-not-allowed': isEncoder,
                                                   'hover:bg-gray-50': !isEncoder
                                               }"
                                               placeholder="Enter health worker name" />
                                        <p v-if="isEncoder" class="mt-1 text-xs text-gray-500">
                                            Health worker cannot be changed for existing case reports
                                        </p>
                                    </div>

                                    <div>
                                        <label class="block text-sm font-semibold text-gray-700 mb-2">
                                            Designation <span class="text-red-500">*</span>
                                        </label>
                                        <input v-model="form.health_worker_designation"
                                               type="text"
                                               :required="shouldRequireFields"
                                               :disabled="isEncoder"
                                               :class="{
                                                   'w-full rounded-xl border-gray-300 shadow-sm focus:border-emerald-500 focus:ring-2 focus:ring-emerald-200 transition-all duration-200 py-3 px-4': true,
                                                   'bg-gray-100 cursor-not-allowed': isEncoder,
                                                   'hover:bg-gray-50': !isEncoder
                                               }"
                                               placeholder="Enter designation" />
                                        <p v-if="isEncoder" class="mt-1 text-xs text-gray-500">
                                            Designation cannot be changed for existing case reports
                                        </p>
                                    </div>
                                </div>

                                <div>
                                    <label class="block text-sm font-semibold text-gray-700 mb-2">Contact Information</label>
                                    <input v-model="form.health_worker_contact"
                                           type="text"
                                           :disabled="isEncoder"
                                           :class="{
                                               'w-full rounded-xl border-gray-300 shadow-sm focus:border-emerald-500 focus:ring-2 focus:ring-emerald-200 transition-all duration-200 py-3 px-4': true,
                                               'bg-gray-100 cursor-not-allowed': isEncoder,
                                               'hover:bg-gray-50': !isEncoder
                                           }"
                                           placeholder="Phone number or email" />
                                    <p v-if="isEncoder" class="mt-1 text-xs text-gray-500">
                                        Contact information cannot be changed for existing case reports
                                    </p>
                                </div>
                            </div>
                        </div>

                        <!-- Add other sections here similar to Create.vue -->

                        <!-- Navigation & Actions -->
                        <div class="px-8 py-6 bg-gray-50 border-t border-gray-200">
                            <div class="flex items-center justify-between">
                                <!-- Previous Button -->
                                <button
                                    v-if="currentStep > 1"
                                    @click.prevent="prevStep"
                                    type="button"
                                    class="group inline-flex items-center px-6 py-3 border-2 border-gray-300 rounded-xl shadow-sm text-sm font-semibold text-gray-700 bg-white hover:bg-gray-50 hover:border-gray-400 focus:outline-none focus:ring-4 focus:ring-gray-200 transition-all duration-200"
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
                                        {{ form.processing ? 'Saving...' : 'Save Changes' }}
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
                                        {{ form.processing ? 'Updating...' : 'Update Case Report' }}
                                    </button>
                                </div>
                            </div>

                            <!-- Progress Text -->
                            <div class="mt-4 text-center">
                                <p class="text-sm text-gray-500">
                                    Step {{ currentStep }} of {{ totalSteps }} • {{ Math.round((currentStep / totalSteps) * 100) }}% Complete
                                </p>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </AuthenticatedLayout>
</template>
