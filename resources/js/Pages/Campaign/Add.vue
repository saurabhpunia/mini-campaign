<script setup>
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import InputLabel from '@/Components/InputLabel.vue';
import PrimaryButton from '@/Components/PrimaryButton.vue';
import TextInput from '@/Components/TextInput.vue';
import InputError from '@/Components/InputError.vue';
import { Head, useForm } from '@inertiajs/vue3';

defineProps({
    //array of error while uploading the csv file
    arrError: {
        type: Array,
        default: []
    },
});

const objForm = useForm({
    name: '',
    file: null,
});

const submit = () => {
    objForm.post(route('campaign.add'));
};

//handle the csv file upload
const handleFileUpload = ($event) => {
    const target = $event.target;
    if (target && target.files) {
        objForm.file = target.files[0];
    }
}

</script>

<template>
    <Head title="Campaign" />

    <AuthenticatedLayout>

        <div class="py-6">
            <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
                <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                    <div class="p-6 text-gray-900">
                        <form @submit.prevent="submit">
                            <div>
                                <InputLabel for="name" value="Name" />

                                <TextInput
                                    id="name"
                                    class="mt-1 block w-full"
                                    v-model="objForm.name"
                                    autofocus
                                    autocomplete="false"
                                />

                                <InputError class="mt-2" :message="objForm.errors.name" />
                            </div>

                            <div class="py-6">
                                <InputLabel for="file" value="File" />

                                <input
                                    id="file"
                                    type="file"
                                    class="mt-1 block w-full"
                                    @change="handleFileUpload($event)"
                                />

                                <InputError class="mt-2" :message="objForm.errors.file" />
                            </div>
                            <div class="py-1" v-if="arrError.length">
                                <InputError class="mt-2" v-for="error in arrError" :key="error" :message="error" />
                            </div>
                            <div class="flex items-center justify-end mt-4">
                                <PrimaryButton class="ms-4" :class="{ 'opacity-25': objForm.processing }" :disabled="objForm.processing">
                                    Add
                                </PrimaryButton>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>

    </AuthenticatedLayout>
</template>
