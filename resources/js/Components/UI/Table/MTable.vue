<script setup>
import MThead from "./MThead.vue";
import MTBody from "./MTBody.vue";
import MTFooter from "./MTFooter.vue";
import MTPagination from "./MTPagination.vue";
import {computed, provide, useSlots, ref, onMounted, watch} from 'vue';

const props = defineProps({
    value: {
        type: Array,
        required: false,
        default: () => {
            return [];
        },
    },
    paginationLinks: {
        type: Array,
        required: false,
        default: () => {
            return [];
        },
    },
    isInModal: {
        type: Boolean,
        required: false,
        default: () => {
            return false;
        },
    },
    resourceUrl: {
        type: String,
        required: false,
        default: () => {
            return null;
        },
    },
});

const initialized = ref(false);
const resources = ref([]);
const resourcePaginationLinks = ref([]);
const mResourceUrl = ref(props.resourceUrl);

const fetchResources = (url) => {
    fetch(url).then(async response => {
        response = await response.json();
        resources.value = response.data;
        resourcePaginationLinks.value = response.links;
        mResourceUrl.value = url;
        initialized.value = true;
    });
}
const setData = () => {
    if (props.resourceUrl) {
        fetchResources(props.resourceUrl);
    } else {
        resources.value = props.value;
        resourcePaginationLinks.value = props.paginationLinks;
        initialized.value = true;
    }
}

setData();

watch(() => props.value, () => {
    console.log('123123');
    setData();
});


provide('resourceUrl', mResourceUrl);

const slots = useSlots();

const columns = computed(() => {
    let result = [];

    if (slots.default) {
        slots.default().forEach(item => {
            result.push(item);
        })
    }

    return result;
});

const emit = defineEmits(['clickRow'])

const onClickRow = (e) => {
    emit('clickRow', e)
}

</script>

<template>
    <div class="relative overflow-x-auto shadow-md sm:rounded-lg">
        <slot></slot>
        <table class="w-full text-sm text-left text-gray-500 dark:text-gray-400" v-if="initialized">
            <MThead
                :columns="columns"
                @reloadData="url => fetchResources(url)"
            />
            <MTBody
                :columns="columns"
                :resources="resources"
                @clickRow="onClickRow"
            />
            <MTFooter
                :columns="columns"
            />
        </table>
    </div>
    <MTPagination
        v-if="initialized && resourcePaginationLinks"
        class="MTPagination mt-6"
        :class="{'m-6 ': isInModal}"
        :links="resourcePaginationLinks"
        @reloadData="url => fetchResources(url)"
    />
</template>


<style scoped>

</style>
