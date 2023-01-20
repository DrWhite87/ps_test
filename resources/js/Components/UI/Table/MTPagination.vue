<script setup>
import { Link } from '@inertiajs/inertia-vue3'
import {inject} from 'vue';

defineProps({
    links: {
        type: Array,
        required: true,
        default: () => {
            return [];
        },
    },
});

const resourceUrl = inject('resourceUrl');
console.log('pag resourceUrl', resourceUrl);
</script>

<template>
    <div v-if="links.length > 3">
        <div class="flex flex-wrap -mb-1">
            <template v-for="(link, key) in links">
                <div v-if="link.url === null" :key="key" class="mb-1 mr-1 px-4 py-3 text-gray-400 text-sm leading-4 border rounded" v-html="link.label" />
                <a v-else-if="resourceUrl" :key="`link-${key}`" class="mb-1 mr-1 px-4 py-3 focus:text-indigo-500 text-sm leading-4 hover:bg-white border focus:border-indigo-500 rounded" :class="{ 'bg-amber-50': link.active }" href @click.prevent="$emit('reloadData', link.url)" v-html="link.label" />
                <Link v-else :key="`link-${key}`" class="mb-1 mr-1 px-4 py-3 focus:text-indigo-500 text-sm leading-4 hover:bg-white border focus:border-indigo-500 rounded" :class="{ 'bg-amber-50': link.active }" :href="link.url" v-html="link.label" />
            </template>
        </div>
    </div>
</template>
