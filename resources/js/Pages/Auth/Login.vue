<script setup lang="ts">
import { Head, useForm } from '@inertiajs/vue3';
import { ref } from 'vue';

defineProps<{
    canResetPassword?: boolean;
    status?: string;
}>();

const form = useForm({
    email: '',
    password: '',
    remember: false,
});

const showPassword = ref(false);

const submit = () => {
    form.post(route('login'), {
        onFinish: () => {
            form.reset('password');
        },
    });
};

// Quick login presets
const quickLogin = (email: string) => {
    form.email = email;
    form.password = 'password';
};
</script>

<template>
    <Head title="Login - Pangasinan PHO Disease Reporting System" />

    <div class="flex min-h-screen">
        <!-- Left Side - Branding & Info -->
        <div class="relative flex-col justify-between hidden p-12 overflow-hidden lg:flex lg:w-1/2 bg-gradient-to-br from-blue-600 via-blue-700 to-blue-900">
            <!-- Background Pattern -->
            <div class="absolute inset-0 opacity-10">
                <div class="absolute top-0 left-0 w-64 h-64 -translate-x-1/2 -translate-y-1/2 bg-white rounded-full"></div>
                <div class="absolute bottom-0 right-0 bg-white rounded-full w-96 h-96 translate-x-1/3 translate-y-1/3"></div>
            </div>

            <div class="relative z-10 text-center">
                <!-- Logo & Header -->
                <div class="flex justify-center mb-6">
                    <div class="flex items-center justify-center w-24 h-24 p-3 bg-white rounded-full">
                        <!-- Replace 'pangasinan-logo.png' with your actual logo filename -->
                        <img
                            src="/images/pangasinan-logo.png"
                            alt="Pangasinan PHO Logo"
                            class="object-contain w-20 h-20"
                            onerror="this.style.display='none'; this.nextElementSibling.style.display='block';"
                        />
                        <!-- Fallback SVG icon if image fails to load -->
                        <svg class="hidden w-16 h-16 text-blue-600" fill="currentColor" viewBox="0 0 20 20">
                            <path d="M9 6a3 3 0 11-6 0 3 3 0 616 0zM17 6a3 3 0 11-6 0 3 3 0 616 0zM12.93 17c.046-.327.07-.66.07-1a6.97 6.97 0 00-1.5-4.33A5 5 0 0119 16v1h-6.07zM6 11a5 5 0 715 5v1H1v-1a5 5 0 715-5z"/>
                        </svg>
                    </div>
                </div>

                <!-- Main Info -->
                <div>
                    <h2 class="mb-4 text-4xl font-bold text-center text-white">
                        Welcome to Pangasinan Disease Reporting and Outbreak Alert System
                    </h2>
                    <p class="max-w-2xl mx-auto mb-8 text-lg text-center text-blue-100">
                        A centralized system designed to streamline disease reporting, monitoring, and outbreak alert management across the province.
                    </p>
                </div>
            </div>

            <!-- Footer -->
            <div class="relative z-10 text-sm text-blue-200">
                <p>© 2025 Provincial Health Office of Pangasinan</p>
            </div>
        </div>

        <!-- Right Side - Login Form -->
        <div class="flex items-center justify-center w-full p-8 lg:w-1/2 bg-gray-50">
            <div class="w-full max-w-md">
                <!-- Mobile Logo -->
                <div class="mb-8 text-center lg:hidden">
                    <div class="inline-flex items-center justify-center w-20 h-20 p-3 mb-4 bg-blue-600 rounded-full">
                        <!-- Replace 'pangasinan-logo.png' with your actual logo filename -->
                        <img
                            src="/images/pangasinan-logo.png"
                            alt="Pangasinan PHO Logo"
                            class="object-contain w-16 h-16"
                            onerror="this.style.display='none'; this.nextElementSibling.style.display='block';"
                        />
                        <!-- Fallback SVG icon if image fails to load -->
                        <svg class="hidden w-12 h-12 text-white" fill="currentColor" viewBox="0 0 20 20">
                            <path d="M9 6a3 3 0 11-6 0 3 3 0 016 0zM17 6a3 3 0 11-6 0 3 3 0 016 0zM12.93 17c.046-.327.07-.66.07-1a6.97 6.97 0 00-1.5-4.33A5 5 0 0119 16v1h-6.07zM6 11a5 5 0 015 5v1H1v-1a5 5 0 015-5z"/>
                        </svg>
                    </div>
                </div>

                <!-- Login Card -->
                <div class="p-8 bg-white shadow-xl rounded-2xl">
                    <div class="mb-8">
                        <h2 class="mb-2 text-3xl font-bold text-gray-900">Welcome Back</h2>
                        <p class="text-gray-600">Sign in to access the disease reporting system</p>
                    </div>

                    <!-- Status Message -->
                    <div v-if="status" class="p-4 mb-6 border border-green-200 rounded-lg bg-green-50">
                        <p class="text-sm font-medium text-green-800">{{ status }}</p>
                    </div>

                    <!-- Login Form -->
                    <form @submit.prevent="submit" class="space-y-6">
                        <!-- Email -->
                        <div>
                            <label for="email" class="block mb-2 text-sm font-semibold text-gray-700">
                                Email Address
                            </label>
                            <div class="relative">
                                <div class="absolute inset-y-0 left-0 flex items-center pl-3 pointer-events-none">
                                    <svg class="w-5 h-5 text-gray-400" fill="currentColor" viewBox="0 0 20 20">
                                        <path d="M2.003 5.884L10 9.882l7.997-3.998A2 2 0 0016 4H4a2 2 0 00-1.997 1.884z"/>
                                        <path d="M18 8.118l-8 4-8-4V14a2 2 0 002 2h12a2 2 0 002-2V8.118z"/>
                                    </svg>
                                </div>
                                <input
                                    id="email"
                                    type="email"
                                    v-model="form.email"
                                    required
                                    autofocus
                                    autocomplete="username"
                                    class="block w-full py-3 pl-10 pr-3 transition-colors border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500"
                                    placeholder="your.email@example.com"
                                />
                            </div>
                            <p v-if="form.errors.email" class="mt-2 text-sm text-red-600">{{ form.errors.email }}</p>
                        </div>

                        <!-- Password -->
                        <div>
                            <label for="password" class="block mb-2 text-sm font-semibold text-gray-700">
                                Password
                            </label>
                            <div class="relative">
                                <div class="absolute inset-y-0 left-0 flex items-center pl-3 pointer-events-none">
                                    <svg class="w-5 h-5 text-gray-400" fill="currentColor" viewBox="0 0 20 20">
                                        <path fill-rule="evenodd" d="M5 9V7a5 5 0 0110 0v2a2 2 0 012 2v5a2 2 0 01-2 2H5a2 2 0 01-2-2v-5a2 2 0 012-2zm8-2v2H7V7a3 3 0 016 0z" clip-rule="evenodd"/>
                                    </svg>
                                </div>
                                <input
                                    id="password"
                                    :type="showPassword ? 'text' : 'password'"
                                    v-model="form.password"
                                    required
                                    autocomplete="current-password"
                                    class="block w-full py-3 pl-10 pr-10 transition-colors border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500"
                                    placeholder="Enter your password"
                                />
                                <button
                                    type="button"
                                    @click="showPassword = !showPassword"
                                    class="absolute inset-y-0 right-0 flex items-center pr-3"
                                >
                                    <svg v-if="!showPassword" class="w-5 h-5 text-gray-400 hover:text-gray-600" fill="currentColor" viewBox="0 0 20 20">
                                        <path d="M10 12a2 2 0 100-4 2 2 0 000 4z"/>
                                        <path fill-rule="evenodd" d="M.458 10C1.732 5.943 5.522 3 10 3s8.268 2.943 9.542 7c-1.274 4.057-5.064 7-9.542 7S1.732 14.057.458 10zM14 10a4 4 0 11-8 0 4 4 0 018 0z" clip-rule="evenodd"/>
                                    </svg>
                                    <svg v-else class="w-5 h-5 text-gray-400 hover:text-gray-600" fill="currentColor" viewBox="0 0 20 20">
                                        <path fill-rule="evenodd" d="M3.707 2.293a1 1 0 00-1.414 1.414l14 14a1 1 0 001.414-1.414l-1.473-1.473A10.014 10.014 0 0019.542 10C18.268 5.943 14.478 3 10 3a9.958 9.958 0 00-4.512 1.074l-1.78-1.781zm4.261 4.26l1.514 1.515a2.003 2.003 0 012.45 2.45l1.514 1.514a4 4 0 00-5.478-5.478z" clip-rule="evenodd"/>
                                        <path d="M12.454 16.697L9.75 13.992a4 4 0 01-3.742-3.741L2.335 6.578A9.98 9.98 0 00.458 10c1.274 4.057 5.065 7 9.542 7 .847 0 1.669-.105 2.454-.303z"/>
                                    </svg>
                                </button>
                            </div>
                            <p v-if="form.errors.password" class="mt-2 text-sm text-red-600">{{ form.errors.password }}</p>
                        </div>

                        <!-- Remember & Forgot -->
                        <div class="flex items-center justify-between">
                            <label class="flex items-center">
                                <input
                                    type="checkbox"
                                    v-model="form.remember"
                                    class="w-4 h-4 text-blue-600 border-gray-300 rounded focus:ring-blue-500"
                                />
                                <span class="ml-2 text-sm text-gray-700">Remember me</span>
                            </label>
                            <a
                                v-if="canResetPassword"
                                :href="route('password.request')"
                                class="text-sm font-medium text-blue-600 hover:text-blue-500"
                            >
                                Forgot password?
                            </a>
                        </div>

                        <!-- Submit Button -->
                        <button
                            type="submit"
                            :disabled="form.processing"
                            class="w-full bg-gradient-to-r from-blue-600 to-blue-700 text-white py-3 px-4 rounded-lg font-semibold hover:from-blue-700 hover:to-blue-800 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:ring-offset-2 disabled:opacity-50 disabled:cursor-not-allowed transition-all duration-200 transform hover:scale-[1.02]"
                        >
                            <span v-if="form.processing" class="flex items-center justify-center">
                                <svg class="w-5 h-5 mr-3 -ml-1 text-white animate-spin" fill="none" viewBox="0 0 24 24">
                                    <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                                    <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                                </svg>
                                Signing in...
                            </span>
                            <span v-else>Sign In</span>
                        </button>
                    </form>

                    <!-- Quick Login for Testing -->
                    <div class="pt-6 mt-8 border-t border-gray-200">
                        <p class="mb-3 text-xs text-center text-gray-500">Quick Login (Testing)</p>
                        <div class="grid grid-cols-3 gap-2">
                            <button
                                @click="quickLogin('encoder@dcho.gov.ph')"
                                type="button"
                                class="px-3 py-2 text-xs font-medium text-blue-700 transition-colors border border-blue-200 rounded-lg bg-blue-50 hover:bg-blue-100"
                            >
                                Encoder
                            </button>
                            <button
                                @click="quickLogin('validator@rimc.gov.ph')"
                                type="button"
                                class="px-3 py-2 text-xs font-medium text-green-700 transition-colors border border-green-200 rounded-lg bg-green-50 hover:bg-green-100"
                            >
                                Validator
                            </button>
                            <button
                                @click="quickLogin('pesu@pangasinan.gov.ph')"
                                type="button"
                                class="px-3 py-2 text-xs font-medium text-purple-700 transition-colors border border-purple-200 rounded-lg bg-purple-50 hover:bg-purple-100"
                            >
                                PESU Admin
                            </button>
                        </div>
                    </div>
                </div>

                <!-- User Roles Info -->
                <div class="mt-6 text-sm text-center text-gray-600">
                    <details class="cursor-pointer">
                        <summary class="font-medium text-gray-700 hover:text-gray-900">User Role Information</summary>
                        <div class="p-4 mt-4 space-y-3 text-left bg-white rounded-lg shadow">
                            <div class="pl-3 border-l-4 border-blue-500">
                                <h4 class="font-semibold text-gray-900">Encoder (Disease Surveillance Officer)</h4>
                                <p class="mt-1 text-xs text-gray-600">Create and edit case reports • View own submissions</p>
                            </div>
                            <div class="pl-3 border-l-4 border-green-500">
                                <h4 class="font-semibold text-gray-900">Validator (District Hospital)</h4>
                                <p class="mt-1 text-xs text-gray-600">Validate case reports • Export data • View facility cases</p>
                            </div>
                            <div class="pl-3 border-l-4 border-purple-500">
                                <h4 class="font-semibold text-gray-900">PESU Admin (Super Admin)</h4>
                                <p class="mt-1 text-xs text-gray-600">Full system access • Manage users • Create outbreak alerts • View all reports</p>
                            </div>
                        </div>
                    </details>
                </div>
            </div>
        </div>
    </div>
</template>
