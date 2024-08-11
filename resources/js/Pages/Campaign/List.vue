<script setup>
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import { Head } from '@inertiajs/vue3';
import { ref, onMounted, onUnmounted } from 'vue';
import axios from 'axios';
import moment from 'moment';

var arrData = ref([]),
    intIntervalId = ref(null),
    blnLoading = ref(false)

//fetch the data
const updateData = () => {
    blnLoading.value = true
    axios.get('/campaign/list')
        .then((response) => {
            if(response && response.status === 200){
                arrData.value = response.data
            }
            blnLoading.value = false
        })
        .catch(() => {
            blnLoading.value = false
            console.log('Error while fetching the records...')
        })
}

onMounted(() => {
    updateData();
    //refresh the data every 30 seconds
    intIntervalId.value = setInterval(() => updateData(), 20000);
});

onUnmounted(() => {
    clearInterval(intIntervalId.value);
});

</script>
<style>
.table-data{
    width: 100%;
}
</style>
<template>
    <Head title="Campaign" />

    <AuthenticatedLayout>

        <div class="py-6">
            <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
                <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                    <div class="p-6 text-gray-900">
                        <div v-if="blnLoading" class="text-center">
                            Laoding ...
                        </div>
                        <table v-else class="table-data">
                            <thead>
                                <tr>
                                    <th style="width: 30%;" class="text-left">Name</th>
                                    <th style="width: 20%;" class="text-center">Total Contacts</th>
                                    <th style="width: 20%;" class="text-center">Processed Contacts</th>
                                    <th style="width: 30%;" class="text-center">Created DateTime</th>
                                </tr>
                            </thead>

                            <tbody>
                                <tr v-for="(objCampaign, intKey) in arrData" :key="intKey">
                                    <td style="width: 30%;">{{ objCampaign.name }}</td>
                                    <td style="width: 20%;" class="text-center">{{ objCampaign.total_contacts }}</td>
                                    <td style="width: 20%;" class="text-center">{{ objCampaign.processed_contacts }}</td>
                                    <td style="width: 30%;" class="text-center">{{ moment(objCampaign.created_at).format('DD MMM YYYY') }}</td>
                                </tr>
                                <tr v-if="arrData.length === 0">
                                    <td colspan="4" class="py-8 text-center">No data found. Try adding the campaign.</td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>

    </AuthenticatedLayout>
</template>
