<script setup lang="ts">
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import { Head, Link, router, usePage } from '@inertiajs/vue3';
import { computed, ref } from 'vue';

interface CaseReport {
    id: number;
    case_id: string;
    disease: { id: number; name: string };
    disease_other?: string;
    date_reported: string;
    case_classification: string;
    outcome: string;

    // Patient Information
    last_name: string;
    first_name: string;
    middle_name?: string;
    sex: string;
    date_of_birth: string;
    civil_status: string;
    occupation?: string;
    address: string;
    barangay: { id: number; name: string };
    municipality: { id: number; name: string };
    contact_number?: string;
    pregnancy_status?: string;
    philhealth_number?: string;
    nationality: string;

    // Clinical Information
    date_of_onset: string;
    date_of_consultation: string;
    admitting_facility?: { id: number; name: string; type: string };
    signs_symptoms?: string[];
    signs_symptoms_other?: string;
    clinical_classification?: string;
    complications?: string;
    clinical_outcome: string;
    date_of_death?: string;
    final_diagnosis?: string;
    clinical_remarks?: string;

    // Laboratory
    specimen_collection_date?: string;
    specimen_type?: string;
    laboratory_test?: string;
    laboratory_result_date?: string;
    laboratory_result?: string;
    testing_laboratory?: string;
    laboratory_file_path?: string;

    // Exposure & Travel
    place_of_exposure?: string;
    date_of_exposure?: string;
    travel_history?: string;
    travel_departure_date?: string;
    travel_return_date?: string;
    mode_of_exposure?: string;
    source_of_infection?: string;

    // Contact Tracing
    contacts_identified: boolean;
    number_of_contacts?: number;
    contact_details?: string;
    relationship_to_case?: string;
    date_contacted?: string;
    quarantine_status?: string;

    // Reporting
    reporting_facility?: { id: number; name: string; type: string };
    reporting_health_worker: string;
    health_worker_designation: string;
    health_worker_contact?: string;
    reporter?: { id: number; name: string; email: string };

    // Status
    status: string;
    validator_feedback?: string;
    returned_by?: number;
    returned_at?: string;
    created_at: string;
    updated_at: string;
}

interface Props {
    caseReport: CaseReport;
}

const props = defineProps<Props>();

const getStatusColor = (status: string) => {
    const colors: Record<string, string> = {
        draft: 'bg-gray-100 text-gray-800',
        submitted: 'bg-blue-100 text-blue-800',
        validated: 'bg-green-100 text-green-800',
        approved: 'bg-purple-100 text-purple-800',
        returned: 'bg-yellow-100 text-yellow-800',
    };
    return colors[status] || 'bg-gray-100 text-gray-800';
};

const getClassificationColor = (classification: string) => {
    const colors: Record<string, string> = {
        Suspect: 'bg-yellow-100 text-yellow-800',
        Confirmed: 'bg-red-100 text-red-800',
    };
    return colors[classification] || 'bg-gray-100 text-gray-800';
};

const calculateAge = (dob: string) => {
    const birthDate = new Date(dob);
    const today = new Date();
    let age = today.getFullYear() - birthDate.getFullYear();
    const monthDiff = today.getMonth() - birthDate.getMonth();
    if (monthDiff < 0 || (monthDiff === 0 && today.getDate() < birthDate.getDate())) {
        age--;
    }
    return age;
};

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

const printReport = () => {
    window.print();
};

// Get user roles from page props
const page = usePage();
const userRoles = computed(() => (page.props.auth as any).user?.roles || []);
const isValidator = computed(() => userRoles.value.includes('validator'));
const isPesuAdmin = computed(() => userRoles.value.includes('pesu_admin'));

const validateReport = () => {
    let confirmMessage = 'Are you sure you want to validate this case report? It will be sent to PESU for final approval.';

    // Add special message for Suspect cases
    if (props.caseReport.case_classification === 'Suspect') {
        confirmMessage += '\n\nNote: Since this is a Suspect case, validating it will automatically change the classification to Confirmed.';
    }

    if (confirm(confirmMessage)) {
        router.post(route('case-reports.validate', props.caseReport.id), {}, {
            preserveScroll: true,
        });
    }
};

const approveReport = () => {
    if (confirm('Are you sure you want to approve this case report?')) {
        router.post(route('case-reports.approve', props.caseReport.id), {}, {
            preserveScroll: true,
        });
    }
};

// Return report functionality
const showReturnDialog = ref(false);
const returnFeedback = ref('');
const returnError = ref('');

const openReturnDialog = () => {
    showReturnDialog.value = true;
    returnFeedback.value = '';
};

const closeReturnDialog = () => {
    showReturnDialog.value = false;
    returnFeedback.value = '';
    returnError.value = '';
};

const returnReport = () => {
    console.log('returnReport called');
    console.log('Feedback:', returnFeedback.value);

    // Clear any previous errors
    returnError.value = '';

    if (!returnFeedback.value.trim()) {
        returnError.value = 'Please provide feedback for returning this report.';
        return;
    }

    if (returnFeedback.value.trim().length < 10) {
        returnError.value = 'Feedback must be at least 10 characters long.';
        return;
    }

    if (confirm('Are you sure you want to return this case report to the encoder?')) {
        console.log('Sending return request...');
        router.post(route('case-reports.return', props.caseReport.id), {
            feedback: returnFeedback.value.trim()
        }, {
            preserveScroll: true,
            onSuccess: (page) => {
                console.log('Return successful:', page);
                closeReturnDialog();
            },
            onError: (errors) => {
                console.error('Return failed:', errors);
                if (errors.feedback) {
                    returnError.value = errors.feedback[0];
                } else {
                    returnError.value = 'Failed to return report: ' + Object.values(errors).join(', ');
                }
            }
        });
    }
};
</script>

<template>
    <Head :title="`Case Report - ${caseReport.case_id}`" />

    <AuthenticatedLayout>
        <template #header>
            <div class="flex items-center justify-between">
                <div>
                    <h2 class="text-xl font-semibold leading-tight text-gray-800">
                        Case Report: {{ caseReport.case_id }}
                    </h2>
                    <p class="mt-1 text-sm text-gray-600">
                        {{ caseReport.disease.name }} - {{ caseReport.first_name }} {{ caseReport.last_name }}
                    </p>
                </div>
                <Link
                    :href="route('case-reports.index')"
                    class="flex items-center px-4 py-2 text-white transition bg-gray-600 rounded-lg hover:bg-gray-700"
                >
                    <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18" />
                    </svg>
                    Back to List
                </Link>
            </div>
        </template>

        <div class="py-12">
            <div class="mx-auto space-y-6 max-w-7xl sm:px-6 lg:px-8">

                <!-- Status Banner -->
                <div class="overflow-hidden bg-white shadow-sm sm:rounded-lg">
                    <div class="flex items-center justify-between p-6">
                        <div>
                            <span :class="['px-3 py-1 rounded-full text-sm font-medium', getStatusColor(caseReport.status)]">
                                {{ caseReport.status.charAt(0).toUpperCase() + caseReport.status.slice(1) }}
                            </span>
                            <span :class="['ml-2 px-3 py-1 rounded-full text-sm font-medium', getClassificationColor(caseReport.case_classification)]">
                                {{ caseReport.case_classification }}
                            </span>
                        </div>
                        <div class="text-sm text-gray-600">
                            Reported: {{ new Date(caseReport.date_reported).toLocaleDateString() }}
                        </div>
                    </div>
                </div>

                <!-- Validation Notice for Suspect Cases -->
                <div v-if="isValidator && caseReport.status === 'submitted' && caseReport.case_classification === 'Suspect'"
                     class="overflow-hidden border border-blue-200 shadow-sm bg-blue-50 sm:rounded-lg">
                    <div class="p-4">
                        <div class="flex items-start">
                            <div class="flex-shrink-0">
                                <svg class="w-5 h-5 text-blue-600 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                                </svg>
                            </div>
                            <div class="ml-3">
                                <h4 class="text-sm font-medium text-blue-900">Validation Notice</h4>
                                <p class="mt-1 text-sm text-blue-700">
                                    This case is currently classified as <strong>Suspect</strong>. When you validate this report,
                                    the classification will automatically be updated to <strong>Confirmed</strong>.
                                </p>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Validator Feedback (when returned) -->
                <div v-if="caseReport.status === 'returned' && caseReport.validator_feedback"
                     class="overflow-hidden border border-red-200 shadow-sm bg-red-50 sm:rounded-lg">
                    <div class="p-6">
                        <div class="flex items-start">
                            <div class="flex-shrink-0">
                                <svg class="w-6 h-6 text-red-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-2.5L13.732 4c-.77-.833-1.964-.833-2.732 0L3.732 16c-.77.833.192 2.5 1.732 2.5z" />
                                </svg>
                            </div>
                            <div class="flex-1 ml-3">
                                <h3 class="text-lg font-medium text-red-900">
                                    Report Returned for Corrections
                                </h3>
                                <div class="mt-2 text-sm text-red-700">
                                    <p class="mb-2">This report has been returned by the validator with the following feedback:</p>
                                    <div class="p-3 bg-white border border-red-200 rounded-md">
                                        <p class="whitespace-pre-wrap">{{ caseReport.validator_feedback }}</p>
                                    </div>
                                    <p v-if="caseReport.returned_at" class="mt-2 text-xs text-red-600">
                                        Returned on: {{ formatReturnedDateTime(caseReport.returned_at) }}
                                    </p>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Section A: Case Identification -->
                <div class="overflow-hidden bg-white shadow-sm sm:rounded-lg">
                    <div class="p-6">
                        <h3 class="pb-2 mb-4 text-lg font-semibold text-gray-900 border-b">Section A: Case Identification</h3>
                        <div class="grid grid-cols-2 gap-6">
                            <div>
                                <label class="text-sm font-medium text-gray-500">Case ID</label>
                                <p class="mt-1 text-gray-900">{{ caseReport.case_id }}</p>
                            </div>
                            <div>
                                <label class="text-sm font-medium text-gray-500">Disease</label>
                                <p class="mt-1 text-gray-900">{{ caseReport.disease.name }}</p>
                                <p v-if="caseReport.disease_other" class="text-sm text-gray-600">({{ caseReport.disease_other }})</p>
                            </div>
                            <div>
                                <label class="text-sm font-medium text-gray-500">Date Reported</label>
                                <p class="mt-1 text-gray-900">{{ new Date(caseReport.date_reported).toLocaleDateString() }}</p>
                            </div>
                            <div>
                                <label class="text-sm font-medium text-gray-500">Case Classification</label>
                                <p class="mt-1">
                                    <span :class="['px-2 py-1 rounded text-sm', getClassificationColor(caseReport.case_classification)]">
                                        {{ caseReport.case_classification }}
                                    </span>
                                </p>
                            </div>
                            <div>
                                <label class="text-sm font-medium text-gray-500">Outcome</label>
                                <p class="mt-1 text-gray-900">{{ caseReport.outcome }}</p>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Section B: Patient Information -->
                <div class="overflow-hidden bg-white shadow-sm sm:rounded-lg">
                    <div class="p-6">
                        <h3 class="pb-2 mb-4 text-lg font-semibold text-gray-900 border-b">Section B: Patient Information</h3>
                        <div class="grid grid-cols-2 gap-6">
                            <div>
                                <label class="text-sm font-medium text-gray-500">Full Name</label>
                                <p class="mt-1 font-medium text-gray-900">
                                    {{ caseReport.first_name }} {{ caseReport.middle_name ? caseReport.middle_name + ' ' : '' }}{{ caseReport.last_name }}
                                </p>
                            </div>
                            <div>
                                <label class="text-sm font-medium text-gray-500">Sex</label>
                                <p class="mt-1 text-gray-900">{{ caseReport.sex }}</p>
                            </div>
                            <div>
                                <label class="text-sm font-medium text-gray-500">Date of Birth / Age</label>
                                <p class="mt-1 text-gray-900">
                                    {{ new Date(caseReport.date_of_birth).toLocaleDateString() }} ({{ calculateAge(caseReport.date_of_birth) }} years old)
                                </p>
                            </div>
                            <div>
                                <label class="text-sm font-medium text-gray-500">Civil Status</label>
                                <p class="mt-1 text-gray-900">{{ caseReport.civil_status }}</p>
                            </div>
                            <div>
                                <label class="text-sm font-medium text-gray-500">Occupation</label>
                                <p class="mt-1 text-gray-900">{{ caseReport.occupation || 'N/A' }}</p>
                            </div>
                            <div>
                                <label class="text-sm font-medium text-gray-500">Nationality</label>
                                <p class="mt-1 text-gray-900">{{ caseReport.nationality }}</p>
                            </div>
                            <div class="col-span-2">
                                <label class="text-sm font-medium text-gray-500">Address</label>
                                <p class="mt-1 text-gray-900">{{ caseReport.address }}</p>
                            </div>
                            <div>
                                <label class="text-sm font-medium text-gray-500">Barangay</label>
                                <p class="mt-1 text-gray-900">{{ caseReport.barangay.name }}</p>
                            </div>
                            <div>
                                <label class="text-sm font-medium text-gray-500">Municipality</label>
                                <p class="mt-1 text-gray-900">{{ caseReport.municipality.name }}</p>
                            </div>
                            <div>
                                <label class="text-sm font-medium text-gray-500">Contact Number</label>
                                <p class="mt-1 text-gray-900">{{ caseReport.contact_number || 'N/A' }}</p>
                            </div>
                            <div>
                                <label class="text-sm font-medium text-gray-500">PhilHealth Number</label>
                                <p class="mt-1 text-gray-900">{{ caseReport.philhealth_number || 'N/A' }}</p>
                            </div>
                            <div v-if="caseReport.sex === 'Female'">
                                <label class="text-sm font-medium text-gray-500">Pregnancy Status</label>
                                <p class="mt-1 text-gray-900">{{ caseReport.pregnancy_status || 'N/A' }}</p>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Section C: Clinical Information -->
                <div class="overflow-hidden bg-white shadow-sm sm:rounded-lg">
                    <div class="p-6">
                        <h3 class="pb-2 mb-4 text-lg font-semibold text-gray-900 border-b">Section C: Clinical Information</h3>
                        <div class="grid grid-cols-2 gap-6">
                            <div>
                                <label class="text-sm font-medium text-gray-500">Date of Onset</label>
                                <p class="mt-1 text-gray-900">{{ new Date(caseReport.date_of_onset).toLocaleDateString() }}</p>
                            </div>
                            <div>
                                <label class="text-sm font-medium text-gray-500">Date of Consultation</label>
                                <p class="mt-1 text-gray-900">{{ new Date(caseReport.date_of_consultation).toLocaleDateString() }}</p>
                            </div>
                            <div class="col-span-2">
                                <label class="text-sm font-medium text-gray-500">Admitting Facility</label>
                                <p class="mt-1 text-gray-900">
                                    {{ caseReport.admitting_facility ? `${caseReport.admitting_facility.name} (${caseReport.admitting_facility.type})` : 'N/A' }}
                                </p>
                            </div>
                            <div class="col-span-2">
                                <label class="text-sm font-medium text-gray-500">Signs & Symptoms</label>
                                <div class="flex flex-wrap gap-2 mt-2">
                                    <span
                                        v-for="symptom in caseReport.signs_symptoms"
                                        :key="symptom"
                                        class="px-3 py-1 text-sm text-blue-700 rounded-full bg-blue-50"
                                    >
                                        {{ symptom }}
                                    </span>
                                    <span v-if="!caseReport.signs_symptoms || caseReport.signs_symptoms.length === 0" class="text-gray-500">
                                        None reported
                                    </span>
                                </div>
                                <p v-if="caseReport.signs_symptoms_other" class="mt-2 text-gray-900">
                                    Other: {{ caseReport.signs_symptoms_other }}
                                </p>
                            </div>
                            <div>
                                <label class="text-sm font-medium text-gray-500">Clinical Classification</label>
                                <p class="mt-1 text-gray-900">{{ caseReport.clinical_classification || 'N/A' }}</p>
                            </div>
                            <div>
                                <label class="text-sm font-medium text-gray-500">Clinical Outcome</label>
                                <p class="mt-1 text-gray-900">{{ caseReport.clinical_outcome }}</p>
                            </div>
                            <div v-if="caseReport.date_of_death">
                                <label class="text-sm font-medium text-gray-500">Date of Death</label>
                                <p class="mt-1 text-gray-900">{{ new Date(caseReport.date_of_death).toLocaleDateString() }}</p>
                            </div>
                            <div class="col-span-2" v-if="caseReport.complications">
                                <label class="text-sm font-medium text-gray-500">Complications</label>
                                <p class="mt-1 text-gray-900">{{ caseReport.complications }}</p>
                            </div>
                            <div class="col-span-2" v-if="caseReport.final_diagnosis">
                                <label class="text-sm font-medium text-gray-500">Final Diagnosis</label>
                                <p class="mt-1 text-gray-900">{{ caseReport.final_diagnosis }}</p>
                            </div>
                            <div class="col-span-2" v-if="caseReport.clinical_remarks">
                                <label class="text-sm font-medium text-gray-500">Clinical Remarks</label>
                                <p class="mt-1 text-gray-900">{{ caseReport.clinical_remarks }}</p>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Section D: Laboratory Information -->
                <div class="overflow-hidden bg-white shadow-sm sm:rounded-lg">
                    <div class="p-6">
                        <h3 class="pb-2 mb-4 text-lg font-semibold text-gray-900 border-b">Section D: Laboratory Information</h3>
                        <div class="grid grid-cols-2 gap-6">
                            <div>
                                <label class="text-sm font-medium text-gray-500">Specimen Collection Date</label>
                                <p class="mt-1 text-gray-900">
                                    {{ caseReport.specimen_collection_date ? new Date(caseReport.specimen_collection_date).toLocaleDateString() : 'N/A' }}
                                </p>
                            </div>
                            <div>
                                <label class="text-sm font-medium text-gray-500">Specimen Type</label>
                                <p class="mt-1 text-gray-900">{{ caseReport.specimen_type || 'N/A' }}</p>
                            </div>
                            <div>
                                <label class="text-sm font-medium text-gray-500">Laboratory Test</label>
                                <p class="mt-1 text-gray-900">{{ caseReport.laboratory_test || 'N/A' }}</p>
                            </div>
                            <div>
                                <label class="text-sm font-medium text-gray-500">Testing Laboratory</label>
                                <p class="mt-1 text-gray-900">{{ caseReport.testing_laboratory || 'N/A' }}</p>
                            </div>
                            <div>
                                <label class="text-sm font-medium text-gray-500">Result Date</label>
                                <p class="mt-1 text-gray-900">
                                    {{ caseReport.laboratory_result_date ? new Date(caseReport.laboratory_result_date).toLocaleDateString() : 'N/A' }}
                                </p>
                            </div>
                            <div>
                                <label class="text-sm font-medium text-gray-500">Result</label>
                                <p class="mt-1 text-gray-900">{{ caseReport.laboratory_result || 'N/A' }}</p>
                            </div>
                            <div v-if="caseReport.laboratory_file_path" class="col-span-2">
                                <label class="text-sm font-medium text-gray-500">Laboratory File</label>
                                <div class="mt-1">
                                    <a :href="`/storage/${caseReport.laboratory_file_path}`" target="_blank" class="text-blue-600 hover:text-blue-800">
                                        View Laboratory Results
                                    </a>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Section E: Exposure & Travel History -->
                <div class="overflow-hidden bg-white shadow-sm sm:rounded-lg">
                    <div class="p-6">
                        <h3 class="pb-2 mb-4 text-lg font-semibold text-gray-900 border-b">Section E: Exposure & Travel History</h3>
                        <div class="grid grid-cols-2 gap-6">
                            <div>
                                <label class="text-sm font-medium text-gray-500">Place of Exposure</label>
                                <p class="mt-1 text-gray-900">{{ caseReport.place_of_exposure || 'N/A' }}</p>
                            </div>
                            <div>
                                <label class="text-sm font-medium text-gray-500">Date of Exposure</label>
                                <p class="mt-1 text-gray-900">
                                    {{ caseReport.date_of_exposure ? new Date(caseReport.date_of_exposure).toLocaleDateString() : 'N/A' }}
                                </p>
                            </div>
                            <div class="col-span-2">
                                <label class="text-sm font-medium text-gray-500">Travel History</label>
                                <p class="mt-1 text-gray-900">{{ caseReport.travel_history || 'N/A' }}</p>
                            </div>
                            <div>
                                <label class="text-sm font-medium text-gray-500">Travel Departure Date</label>
                                <p class="mt-1 text-gray-900">
                                    {{ caseReport.travel_departure_date ? new Date(caseReport.travel_departure_date).toLocaleDateString() : 'N/A' }}
                                </p>
                            </div>
                            <div>
                                <label class="text-sm font-medium text-gray-500">Travel Return Date</label>
                                <p class="mt-1 text-gray-900">
                                    {{ caseReport.travel_return_date ? new Date(caseReport.travel_return_date).toLocaleDateString() : 'N/A' }}
                                </p>
                            </div>
                            <div>
                                <label class="text-sm font-medium text-gray-500">Mode of Exposure</label>
                                <p class="mt-1 text-gray-900">{{ caseReport.mode_of_exposure || 'N/A' }}</p>
                            </div>
                            <div>
                                <label class="text-sm font-medium text-gray-500">Source of Infection</label>
                                <p class="mt-1 text-gray-900">{{ caseReport.source_of_infection || 'N/A' }}</p>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Section F: Contact Tracing -->
                <div class="overflow-hidden bg-white shadow-sm sm:rounded-lg">
                    <div class="p-6">
                        <h3 class="pb-2 mb-4 text-lg font-semibold text-gray-900 border-b">Section F: Contact Tracing</h3>
                        <div class="grid grid-cols-2 gap-6">
                            <div>
                                <label class="text-sm font-medium text-gray-500">Contacts Identified</label>
                                <p class="mt-1 text-gray-900">{{ caseReport.contacts_identified ? 'Yes' : 'No' }}</p>
                            </div>
                            <div>
                                <label class="text-sm font-medium text-gray-500">Number of Contacts</label>
                                <p class="mt-1 text-gray-900">{{ caseReport.number_of_contacts || 0 }}</p>
                            </div>
                            <div class="col-span-2" v-if="caseReport.contact_details">
                                <label class="text-sm font-medium text-gray-500">Contact Details</label>
                                <p class="mt-1 text-gray-900">{{ caseReport.contact_details }}</p>
                            </div>
                            <div v-if="caseReport.relationship_to_case">
                                <label class="text-sm font-medium text-gray-500">Relationship to Case</label>
                                <p class="mt-1 text-gray-900">{{ caseReport.relationship_to_case }}</p>
                            </div>
                            <div v-if="caseReport.date_contacted">
                                <label class="text-sm font-medium text-gray-500">Date Contacted</label>
                                <p class="mt-1 text-gray-900">{{ new Date(caseReport.date_contacted).toLocaleDateString() }}</p>
                            </div>
                            <div v-if="caseReport.quarantine_status">
                                <label class="text-sm font-medium text-gray-500">Quarantine Status</label>
                                <p class="mt-1 text-gray-900">{{ caseReport.quarantine_status }}</p>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Section G: Reporting Information -->
                <div class="overflow-hidden bg-white shadow-sm sm:rounded-lg">
                    <div class="p-6">
                        <h3 class="pb-2 mb-4 text-lg font-semibold text-gray-900 border-b">Section G: Reporting Information</h3>
                        <div class="grid grid-cols-2 gap-6">
                            <div>
                                <label class="text-sm font-medium text-gray-500">Reporting Health Worker</label>
                                <p class="mt-1 text-gray-900">{{ caseReport.reporting_health_worker }}</p>
                            </div>
                            <div>
                                <label class="text-sm font-medium text-gray-500">Designation</label>
                                <p class="mt-1 text-gray-900">{{ caseReport.health_worker_designation }}</p>
                            </div>
                            <div>
                                <label class="text-sm font-medium text-gray-500">Contact</label>
                                <p class="mt-1 text-gray-900">{{ caseReport.health_worker_contact || 'N/A' }}</p>
                            </div>
                            <div v-if="caseReport.reporting_facility">
                                <label class="text-sm font-medium text-gray-500">Reporting Facility</label>
                                <p class="mt-1 text-gray-900">{{ caseReport.reporting_facility.name }}</p>
                            </div>
                            <div v-if="caseReport.reporter">
                                <label class="text-sm font-medium text-gray-500">Reported By (System User)</label>
                                <p class="mt-1 text-gray-900">{{ caseReport.reporter.name }} ({{ caseReport.reporter.email }})</p>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Action Buttons -->
                <div class="overflow-hidden bg-white shadow-sm sm:rounded-lg">
                    <div class="flex items-center justify-between p-6">
                        <div class="flex space-x-3">
                            <!-- Validator Actions -->
                            <button
                                v-if="isValidator && caseReport.status === 'submitted'"
                                @click="validateReport"
                                class="flex items-center px-4 py-2 text-white transition bg-green-600 rounded-lg hover:bg-green-700"
                            >
                                <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                                </svg>
                                Validate Report
                            </button>

                            <button
                                v-if="isValidator && caseReport.status === 'submitted'"
                                @click="openReturnDialog"
                                class="flex items-center px-4 py-2 text-white transition bg-orange-600 rounded-lg hover:bg-orange-700"
                            >
                                <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 10h10a8 8 0 018 8v2M3 10l6 6m-6-6l6-6" />
                                </svg>
                                Return Report
                            </button>

                            <!-- PESU Admin Actions -->
                            <button
                                v-if="isPesuAdmin && caseReport.status === 'validated'"
                                @click="approveReport"
                                class="flex items-center px-4 py-2 text-white transition bg-purple-600 rounded-lg hover:bg-purple-700"
                            >
                                <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
                                </svg>
                                Approve Report
                            </button>
                        </div>

                        <div class="flex space-x-3">
                            <Link
                                v-if="caseReport.status === 'draft' || caseReport.status === 'returned'"
                                :href="`/case-reports/${caseReport.id}/edit`"
                                class="px-4 py-2 text-white transition bg-blue-600 rounded-lg hover:bg-blue-700"
                            >
                                Edit Case Report
                            </Link>
                            <button
                                class="px-4 py-2 text-white transition bg-gray-600 rounded-lg hover:bg-gray-700"
                                @click="printReport"
                            >
                                Print Report
                            </button>
                        </div>
                    </div>
                </div>

            </div>
        </div>

        <!-- Return Report Modal -->
        <div v-if="showReturnDialog" class="fixed inset-0 z-50 overflow-y-auto">
            <div class="flex items-center justify-center min-h-screen px-4 pt-4 pb-20 text-center sm:block sm:p-0">
                <!-- Background overlay -->
                <div class="fixed inset-0 transition-opacity bg-gray-500 bg-opacity-75" @click="closeReturnDialog"></div>

                <!-- Modal content -->
                <div class="inline-block overflow-hidden text-left align-bottom transition-all transform bg-white rounded-lg shadow-xl sm:my-8 sm:align-middle sm:max-w-lg sm:w-full">
                    <div class="px-4 pt-5 pb-4 bg-white sm:p-6 sm:pb-4">
                        <div class="sm:flex sm:items-start">
                            <div class="flex items-center justify-center flex-shrink-0 w-12 h-12 mx-auto bg-orange-100 rounded-full sm:mx-0 sm:h-10 sm:w-10">
                                <svg class="w-6 h-6 text-orange-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 10h10a8 8 0 018 8v2M3 10l6 6m-6-6l6-6" />
                                </svg>
                            </div>
                            <div class="w-full mt-3 text-center sm:mt-0 sm:ml-4 sm:text-left">
                                <h3 class="mb-4 text-lg font-medium leading-6 text-gray-900">
                                    Return Case Report
                                </h3>
                                <div class="mt-2">
                                    <p class="mb-4 text-sm text-gray-500">
                                        Please provide feedback explaining why this case report is being returned to the encoder for correction.
                                    </p>
                                    <div class="mb-4">
                                        <label class="block mb-2 text-sm font-medium text-gray-700">
                                            Feedback *
                                            <span class="text-xs text-gray-500">(minimum 10 characters)</span>
                                        </label>
                                        <textarea
                                            v-model="returnFeedback"
                                            rows="4"
                                            :class="[
                                                'w-full border rounded-md shadow-sm px-3 py-2 focus:outline-none focus:ring-2 focus:ring-orange-500 focus:border-orange-500',
                                                returnError ? 'border-red-300' : 'border-gray-300'
                                            ]"
                                            placeholder="Explain what needs to be corrected or improved..."
                                        ></textarea>
                                        <div class="flex items-center justify-between mt-1">
                                            <div v-if="returnError" class="text-sm text-red-600">
                                                {{ returnError }}
                                            </div>
                                            <div class="ml-auto text-xs text-gray-500">
                                                {{ returnFeedback.length }}/1000 characters
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="px-4 py-3 bg-gray-50 sm:px-6 sm:flex sm:flex-row-reverse">
                        <button
                            @click="returnReport"
                            type="button"
                            class="inline-flex justify-center w-full px-4 py-2 text-base font-medium text-white bg-orange-600 border border-transparent rounded-md shadow-sm hover:bg-orange-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-orange-500 sm:ml-3 sm:w-auto sm:text-sm"
                        >
                            Return Report
                        </button>
                        <button
                            @click="closeReturnDialog"
                            type="button"
                            class="inline-flex justify-center w-full px-4 py-2 mt-3 text-base font-medium text-gray-700 bg-white border border-gray-300 rounded-md shadow-sm hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500 sm:mt-0 sm:ml-3 sm:w-auto sm:text-sm"
                        >
                            Cancel
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </AuthenticatedLayout>
</template>
