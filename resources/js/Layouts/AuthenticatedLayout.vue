<script setup lang="ts">
import { ref } from 'vue';
import ApplicationLogo from '@/Components/ApplicationLogo.vue';
import Dropdown from '@/Components/Dropdown.vue';
import DropdownLink from '@/Components/DropdownLink.vue';
import NavLink from '@/Components/NavLink.vue';
import ResponsiveNavLink from '@/Components/ResponsiveNavLink.vue';
import { Link } from '@inertiajs/vue3';

const showingNavigationDropdown = ref(false);
</script>

<template>
    <div>
        <div class="min-h-screen bg-gray-50">
            <nav
                class="bg-white border-b border-gray-200 shadow-sm"
            >
                <!-- Primary Navigation Menu -->
                <div class="px-4 mx-auto max-w-7xl sm:px-6 lg:px-8">
                    <div class="flex items-center justify-between h-16">
                        <div class="flex items-center">
                            <!-- Logo -->
                            <div class="flex items-center shrink-0">
                                <Link :href="route('dashboard')" class="flex items-center space-x-2 group">
                                    <div class="flex items-center justify-center transition-shadow bg-white border border-gray-200 rounded-lg shadow-sm w-9 h-9 group-hover:shadow-md">
                                        <ApplicationLogo
                                            class="w-7.5 h-7.5"
                                            style="width: 30px; height: 30px;"
                                        />
                                    </div>
                                    <div class="hidden lg:block">
                                        <h1 class="text-sm font-semibold text-gray-900">
                                            Disease Surveillance
                                        </h1>
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
                                    class="inline-flex items-center px-3 py-2 text-sm font-medium transition-all duration-150"
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
                            <button class="p-2 text-gray-400 transition-all duration-150 rounded-lg hover:text-gray-600 hover:bg-gray-100">
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.002 6.002 0 00-4-5.659V5a2 2 0 10-4 0v.341C7.67 6.165 6 8.388 6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9" />
                                </svg>
                            </button>

                            <!-- Settings Dropdown -->
                            <div class="relative ms-3">
                                <Dropdown align="right" width="48">
                                    <template #trigger>
                                        <span class="inline-flex rounded-lg">
                                            <button
                                                type="button"
                                                class="inline-flex items-center px-3 py-2 text-sm font-medium text-gray-700 transition-all duration-150 bg-white border border-gray-300 rounded-lg shadow-sm hover:bg-gray-50 hover:text-gray-900 hover:shadow"
                                            >
                                                <div class="flex items-center space-x-2">
                                                    <div class="hidden text-right md:block">
                                                        <p class="text-sm font-medium text-gray-800">{{ $page.props.auth.user.name }}</p>
                                                        <p class="text-xs text-gray-500">View Profile</p>
                                                    </div>
                                                    <div class="flex items-center justify-center w-8 h-8 text-sm font-medium text-white bg-blue-600 rounded-full shadow-sm">
                                                        {{ $page.props.auth.user.name.charAt(0).toUpperCase() }}
                                                    </div>
                                                    <svg
                                                        class="w-4 h-4 text-gray-400"
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
                                class="inline-flex items-center justify-center p-2 text-gray-400 transition-all duration-150 rounded-lg hover:bg-gray-100 hover:text-gray-600 focus:bg-gray-100 focus:text-gray-600 focus:outline-none"
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
                    class="border-t border-gray-200 sm:hidden bg-gray-50"
                >
                    <div class="px-4 pt-3 pb-3 space-y-1">
                        <ResponsiveNavLink
                            :href="route('dashboard')"
                            :active="route().current('dashboard')"
                            class="flex items-center px-3 py-2 text-base font-medium transition-all duration-150 rounded-md"
                        >
                            <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6" />
                            </svg>
                            Dashboard
                        </ResponsiveNavLink>
                    </div>

                    <!-- Responsive Settings Options -->
                    <div
                        class="pt-3 pb-3 bg-white border-t border-gray-200"
                    >
                        <div class="px-4 mb-3">
                            <div class="flex items-center p-3 space-x-3 border border-gray-200 rounded-lg bg-gray-50">
                                <div class="flex items-center justify-center w-10 h-10 text-sm font-medium text-white bg-blue-600 rounded-full shadow-sm">
                                    {{ $page.props.auth.user.name.charAt(0).toUpperCase() }}
                                </div>
                                <div>
                                    <div class="text-base font-medium text-gray-800">
                                        {{ $page.props.auth.user.name }}
                                    </div>
                                    <div class="text-sm text-gray-500">
                                        {{ $page.props.auth.user.email }}
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="px-4 space-y-1">
                            <ResponsiveNavLink
                                :href="route('profile.edit')"
                                class="flex items-center px-3 py-2 text-base font-medium transition-all duration-150 bg-white border border-gray-300 rounded-md hover:bg-gray-50 hover:border-gray-400"
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
                                class="flex items-center w-full px-3 py-2 text-base font-medium text-red-600 transition-all duration-150 bg-white border border-red-300 rounded-md hover:bg-red-50 hover:border-red-400"
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
                class="bg-white border-b border-gray-200"
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
