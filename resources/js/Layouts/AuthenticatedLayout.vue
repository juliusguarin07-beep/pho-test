<script setup lang="ts">
import { ref } from 'vue';
import ApplicationLogo from '@/Components/ApplicationLogo.vue';
import Dropdown from '@/Components/Dropdown.vue';
import DropdownLink from '@/Components/DropdownLink.vue';
import NavLink from '@/Components/NavLink.vue';
import ResponsiveNavLink from '@/Components/ResponsiveNavLink.vue';
import NotificationDropdown from '@/Components/NotificationDropdown.vue';
import { Link } from '@inertiajs/vue3';

const showingNavigationDropdown = ref(false);
</script>

<template>
    <div>
        <div class="min-h-screen bg-gray-50">
            <nav
                class="bg-gradient-to-r from-blue-600 via-blue-700 to-indigo-800 shadow-lg border-b border-blue-800/20"
            >
                <!-- Primary Navigation Menu -->
                <div class="px-4 mx-auto max-w-7xl sm:px-6 lg:px-8">
                    <div class="flex items-center justify-between h-16">
                        <div class="flex items-center">
                            <!-- Logo -->
                            <div class="flex items-center shrink-0">
                                <Link :href="route('dashboard')" class="flex items-center space-x-3 group p-2 rounded-lg transition-all duration-200 hover:bg-white/10">
                                    <div class="relative">
                                        <div class="absolute inset-0 bg-white/20 rounded-lg blur-sm"></div>
                                        <div class="relative flex items-center justify-center transition-all bg-white/10 border border-white/20 rounded-lg shadow-lg backdrop-blur-sm w-10 h-10 group-hover:shadow-xl group-hover:bg-white/20">
                                            <ApplicationLogo
                                                class="w-7 h-7 text-white"
                                                style="width: 28px; height: 28px;"
                                            />
                                        </div>
                                    </div>
                                    <div class="hidden lg:block">
                                        <h1 class="text-lg font-bold text-white group-hover:text-blue-100 transition-colors duration-200">
                                            Disease Surveillance
                                        </h1>
                                        <p class="text-xs text-blue-200 group-hover:text-blue-100 transition-colors duration-200">
                                            Health Information System
                                        </p>
                                    </div>
                                </Link>
                            </div>

                            <!-- Navigation Links -->
                            <div
                                class="hidden space-x-2 sm:-my-px sm:ms-10 sm:flex"
                            >
                                <NavLink
                                    :href="route('dashboard')"
                                    :active="route().current('dashboard')"
                                >
                                    <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6" />
                                    </svg>
                                    Dashboard
                                </NavLink>
                            </div>
                        </div>

                        <div class="hidden space-x-3 sm:ms-6 sm:flex sm:items-center">
                            <!-- Notification Bell -->
                            <NotificationDropdown />

                            <!-- Settings Dropdown -->
                            <div class="relative ms-3">
                                <Dropdown align="right" width="48">
                                    <template #trigger>
                                        <span class="inline-flex rounded-lg">
                                            <button
                                                type="button"
                                                class="inline-flex items-center px-3 py-2 text-sm font-medium text-white transition-all duration-200 bg-white/10 border border-white/20 rounded-lg shadow-lg backdrop-blur-sm hover:bg-white/20 hover:shadow-xl"
                                            >
                                                <div class="flex items-center space-x-2">
                                                    <div class="hidden text-right md:block">
                                                        <p class="text-sm font-medium text-white">{{ $page.props.auth.user.name }}</p>
                                                        <p class="text-xs text-blue-200">View Profile</p>
                                                    </div>
                                                    <div class="flex items-center justify-center w-8 h-8 text-sm font-medium text-blue-600 bg-white rounded-full shadow-lg">
                                                        {{ $page.props.auth.user.name.charAt(0).toUpperCase() }}
                                                    </div>
                                                    <svg
                                                        class="w-4 h-4 text-white/70"
                                                        xmlns="http://www.w3.org/2000/svg"
                                                        viewBox="0 0 20 20"
                                                        fill="currentColor"
                                                    >
                                                        <path
                                                            fill-rule="evenodd"
                                                            d="M5.293 7.293a1 1 0 011.414 0L10 10.586l3.293-3.293a1 1 0 111.414 1.414l-4 4a1 1 0 01-1.414 0l-4-4a1 1 0 010-1.414z"
                                                            clip-rule="evenodd"
                                                        />
                                                    </svg>
                                                </div>
                                            </button>
                                        </span>
                                    </template>

                                    <template #content>
                                        <div class="p-1">
                                            <DropdownLink
                                                :href="route('profile.edit')"
                                                class="flex items-center px-3 py-2 text-gray-700 transition-all duration-150 rounded-md hover:bg-gray-100 hover:text-gray-900"
                                            >
                                                <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z" />
                                                </svg>
                                                Profile Settings
                                            </DropdownLink>
                                            <div class="my-1 border-t border-gray-200"></div>
                                            <DropdownLink
                                                :href="route('logout')"
                                                method="post"
                                                as="button"
                                                class="flex items-center w-full px-3 py-2 text-gray-700 transition-all duration-150 rounded-md hover:bg-red-50 hover:text-red-700"
                                            >
                                                <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1" />
                                                </svg>
                                                Log Out
                                            </DropdownLink>
                                        </div>
                                    </template>
                                </Dropdown>
                            </div>
                        </div>

                        <!-- Hamburger -->
                        <div class="flex items-center -me-2 sm:hidden">
                            <button
                                @click="
                                    showingNavigationDropdown =
                                        !showingNavigationDropdown
                                "
                                class="inline-flex items-center justify-center p-2 text-white/80 transition-all duration-200 rounded-lg hover:bg-white/10 hover:text-white focus:bg-white/10 focus:text-white focus:outline-none backdrop-blur-sm"
                            >
                                <svg
                                    class="w-5 h-5"
                                    stroke="currentColor"
                                    fill="none"
                                    viewBox="0 0 24 24"
                                    stroke-width="2"
                                >
                                    <path
                                        :class="{
                                            hidden: showingNavigationDropdown,
                                            'inline-flex':
                                                !showingNavigationDropdown,
                                        }"
                                        stroke-linecap="round"
                                        stroke-linejoin="round"
                                        d="M4 6h16M4 12h16M4 18h16"
                                    />
                                    <path
                                        :class="{
                                            hidden: !showingNavigationDropdown,
                                            'inline-flex':
                                                showingNavigationDropdown,
                                        }"
                                        stroke-linecap="round"
                                        stroke-linejoin="round"
                                        d="M6 18L18 6M6 6l12 12"
                                    />
                                </svg>
                            </button>
                        </div>
                    </div>
                </div>

                <!-- Responsive Navigation Menu -->
                <div
                    :class="{
                        block: showingNavigationDropdown,
                        hidden: !showingNavigationDropdown,
                    }"
                    class="border-t border-blue-800/20 sm:hidden bg-gradient-to-b from-blue-700 to-blue-800"
                >
                    <div class="px-4 pt-3 pb-3 space-y-1">
                        <ResponsiveNavLink
                            :href="route('dashboard')"
                            :active="route().current('dashboard')"
                        >
                            <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6" />
                            </svg>
                            Dashboard
                        </ResponsiveNavLink>
                    </div>

                    <!-- Responsive Settings Options -->
                    <div
                        class="pt-3 pb-3 bg-gradient-to-b from-blue-800 to-indigo-800 border-t border-blue-700/30"
                    >
                        <div class="px-4 mb-3">
                            <div class="flex items-center p-3 space-x-3 border border-white/20 rounded-lg bg-white/10 backdrop-blur-sm">
                                <div class="flex items-center justify-center w-10 h-10 text-sm font-medium text-blue-600 bg-white rounded-full shadow-lg">
                                    {{ $page.props.auth.user.name.charAt(0).toUpperCase() }}
                                </div>
                                <div>
                                    <div class="text-base font-medium text-white">
                                        {{ $page.props.auth.user.name }}
                                    </div>
                                    <div class="text-sm text-blue-200">
                                        {{ $page.props.auth.user.email }}
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="px-4 space-y-1">
                            <ResponsiveNavLink
                                :href="route('profile.edit')"
                                class="flex items-center px-3 py-2 text-base font-medium transition-all duration-200 bg-white/10 border border-white/20 rounded-md hover:bg-white/20 hover:border-white/30 text-white backdrop-blur-sm"
                            >
                                <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z" />
                                </svg>
                                Profile Settings
                            </ResponsiveNavLink>
                            <ResponsiveNavLink
                                :href="route('logout')"
                                method="post"
                                as="button"
                                class="flex items-center w-full px-3 py-2 text-base font-medium text-red-300 transition-all duration-200 bg-red-500/10 border border-red-400/30 rounded-md hover:bg-red-500/20 hover:border-red-400/50 hover:text-red-200 backdrop-blur-sm"
                            >
                                <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1" />
                                </svg>
                                Log Out
                            </ResponsiveNavLink>
                        </div>
                    </div>
                </div>
            </nav>

            <!-- Page Heading -->
            <header
                class="bg-gradient-to-r from-white via-blue-50/50 to-indigo-50/30 border-b border-blue-100/50 shadow-sm"
                v-if="$slots.header"
            >
                <div class="px-4 py-4 mx-auto max-w-7xl sm:px-6 lg:px-8">
                    <slot name="header" />
                </div>
            </header>

            <!-- Page Content -->
            <main>
                <slot />
            </main>
        </div>
    </div>
</template>
