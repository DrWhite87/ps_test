<script setup>
import {ref, computed, inject} from "vue";
import { Link } from "@inertiajs/inertia-vue3";

const props = defineProps({
    label: {
        type: String,
        default: "",
    },
    attribute: {
        type: String,
        default: "",
    },
});

const resourceUrl = inject('resourceUrl');

const downFill = ref("lightgray");
const upFill = ref("lightgray");
const sortLink = computed(() => {
    let url = new URL(resourceUrl.value || document.location, document.location.origin);
    let sortValue = url.searchParams.get("sort");
    console.log('url', url.href, 'sortValue', sortValue, 'props.attribute', props.attribute);
    if (sortValue === props.attribute) {
        url.searchParams.set("sort", "-" + props.attribute);
        upFill.value = "black";
        downFill.value = "lightgray";
    } else if (sortValue === "-" + props.attribute) {
        url.searchParams.delete("sort");
        downFill.value = "black";
        upFill.value = "lightgray";
    } else {
        url.searchParams.set("sort", props.attribute);
        downFill.value = "lightgray";
        upFill.value = "lightgray";
    }
    return url.href;
});
</script>
<template>
    <div class="flex items-center gap-4">
        <Link v-if="!resourceUrl"
            :href="sortLink"
        >{{ label }}</Link>
        <a v-else :href="sortLink" @click.prevent="$emit('reloadData', sortLink)">{{ label }}</a>
        <div class="flex flex-col">
            <svg
                class="inline-block w-2 h-2"
                xmlns="http://www.w3.org/2000/svg"
                viewBox="0 0 15 15"
                fill="none"
            >
                <path d="M7.5 3L15 11H0L7.5 3Z" :fill="upFill" />
            </svg>
            <svg
                class="inline-block w-2 h-2"
                xmlns="http://www.w3.org/2000/svg"
                viewBox="0 0 15 15"
                fill="none"
            >
                <path
                    d="M7.49988 12L-0.00012207 4L14.9999 4L7.49988 12Z"
                    :fill="downFill"
                />
            </svg>
        </div>
    </div>
</template>
