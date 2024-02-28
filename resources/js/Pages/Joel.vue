<script>
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import { Head } from '@inertiajs/vue3';

// Options API
export default {

    components: {
        AuthenticatedLayout,
        Head
    },

    methods: {
        doClick() {
            alert('clicked');

        }
    },

    props: {
        message: {
            required: true,
            type: String,
        }
    }
}

</script>

<template>
    <Head title="This is the title in the browser tab" />

    <AuthenticatedLayout>
        <template #header>
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">Joel is the man!</h2>
        </template>

        <div class="py-12">
            <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
                <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                    <div class="p-6 text-gray-900">
                        You are here!
                    </div>

                    <h3>This is a heading: {{ message }}</h3>

                    <button @click="doClick">Click me!</button>
                </div>
            </div>
        </div>
    </AuthenticatedLayout>
</template>