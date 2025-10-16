<script setup lang="ts">
import { computed } from 'vue';
import { Link } from '@inertiajs/vue3';

const props = defineProps<{
    href: string;
    active?: boolean;
}>();

const classes = computed(() =>
    props.active
        ? 'inline-flex items-center px-4 py-2 text-sm font-medium text-white bg-white/20 border border-white/30 rounded-lg shadow-lg backdrop-blur-sm focus:outline-none focus:bg-white/30 transition-all duration-200'
        : 'inline-flex items-center px-4 py-2 text-sm font-medium text-white/90 hover:text-white hover:bg-white/10 rounded-lg border border-transparent hover:border-white/20 focus:outline-none focus:text-white focus:bg-white/10 transition-all duration-200',
);
</script>

<template>
    <Link :href="href" :class="classes">
        <slot />
    </Link>
</template>
