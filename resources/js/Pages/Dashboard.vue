<script lang="ts" setup>
    import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
    import { Head, usePage } from '@inertiajs/vue3';
    import { ref, onMounted, watch } from 'vue';
    import feather from 'feather-icons';

    const token = document.querySelector('meta[name="csrf-token"]').getAttribute('content');

    // * Iconos
    const phrasesList = ref<object[]>([]);

    const { props } = usePage();
    const user = props.user;
    console.log(user,"user")

    const getPhrasesAll = async () => {
        phrasesList.value = [];
        try {
            const response = await fetch('/phrasesAll');
            if (!response.ok) {
                throw new Error(`HTTP error! Status: ${response.status}`);
            }
            const data = await response.json();
            phrasesList.value = data.phrases;
            console.info("data fetch", data);
        } catch (error) {
            showNotification('Error', 'error');
        }
    }

    const show = ref(false);
    const message = ref('');
    const type = ref('info');

    const showNotification = (msg: string, notificationType: string) => {
        message.value = msg;
        type.value = notificationType;
        show.value = true;
    };

    const allowUser = async (userId: number) => {
        try {
            const response = await fetch(`/allow-user/${userId}`, {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': token,
                },
            });
            console.log(response, "res")
            if (response.ok) {
                showNotification('User successfully enabled', 'success');
            } else {
                showNotification('Error', 'error');
            }
            getPhrasesAll();
        } catch (error) {
            showNotification('Error', 'error');
            console.error('Error en la solicitud Fetch:', error);
        }
    };

    const disableUser = async (userId: number) => {
        try {
            const response = await fetch(`/disable-user/${userId}`, {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': token,
                },
            });
            console.log(response, "res")
            if (response.ok) {
                showNotification('User disable successfully', 'info');
            } else {
                showNotification('Error', 'error');
            }
            getPhrasesAll();
        } catch (error) {
            showNotification('Error', 'error');
            console.error('Error en la solicitud Fetch:', error);
        }
    };

    watch(show, (newVal) => {
        if (newVal) {
            let timeout = setTimeout(() => {
                show.value = false;
                clearTimeout(timeout);
            }, 3000);
        }
    });

    onMounted(async () => {
        console.log(props, "asfa")
        console.log('feather.icons', feather.icons);
        if (user.role === "admin") {
            getPhrasesAll();
        }
    });
</script>

<template>
    <Head title="Dashboard" />

    <AuthenticatedLayout>
        <template #header>
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">Dashboard</h2>
        </template>

        <div class="py-12">
            <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
                <div
                    v-if="show"
                    class="fixed top-4 right-4 z-50"
                    :class="{
                        'bg-green-500': type === 'success',
                        'bg-blue-500': type === 'info',
                        'bg-red-500': type === 'error',
                        'bg-yellow-500': type === 'warning',
                    }"
                    >
                    <div class="max-w-sm mx-auto py-2 px-4 rounded shadow-lg text-white">
                        {{ message }}
                    </div>
                </div>
                <div v-if="user.role === 'admin'">
                    <table class="min-w-full divide-y divide-gray-200">
                        <thead>
                            <tr>
                                <th class="px-6 py-3 bg-gray-50 text-left text-xs leading-4 font-medium text-gray-500 uppercase tracking-wider">
                                    #
                                </th>
                                <th class="px-6 py-3 bg-gray-50 text-left text-xs leading-4 font-medium text-gray-500 uppercase tracking-wider">
                                    Quote
                                </th>
                                <th class="px-6 py-3 bg-gray-50 text-left text-xs leading-4 font-medium text-gray-500 uppercase tracking-wider">
                                    User
                                </th>
                                <th class="px-6 py-3 bg-gray-50 text-left text-xs leading-4 font-medium text-gray-500 uppercase tracking-wider">
                                    Actions
                                </th>
                            </tr>
                        </thead>
                        <tbody class="bg-gray-50 divide-y divide-gray-200">
                            <tr v-for="(phrase, index) in phrasesList" :key="phrase.id">
                                <td class="px-6 py-4 whitespace-no-wrap text-sm leading-5 text-gray-900">
                                    {{ index + 1 }}
                                </td>
                                <td class="px-6 py-4 whitespace-no-wrap text-sm leading-5 text-gray-900">
                                    {{ phrase.text }}
                                </td>
                                <td class="px-6 py-4 whitespace-no-wrap text-sm leading-5 text-gray-900">
                                    {{ phrase.user.name }}
                                    <br>
                                    <small>{{ phrase.user.email }}</small>
                                </td>
                                <td class="px-6 py-4 whitespace-no-wrap text-right text-sm leading-5 font-medium">
                                    <template v-if="phrase.user.role !== 'admin'">
                                        <template v-if="phrase.user.is_active == 1">
                                            <button
                                                class="flex items-center text-red-600 hover:text-red-900 ml-2 bg-red-100 bg-opacity-70 hover:bg-opacity-100 py-2 px-4 rounded-md"
                                                @click="disableUser(phrase.user_id)"
                                            >
                                                <span class="mr-2">Ban user</span>
                                                <svg
                                                    fill="none"
                                                    stroke="currentColor"
                                                    stroke-linecap="round"
                                                    stroke-linejoin="round"
                                                    stroke-width="2"
                                                    class="w-5 h-5"
                                                    viewBox="0 0 24 24"
                                                >
                                                    <path d="M6 18L18 6M6 6l12 12"></path>
                                                </svg>
                                            </button>
                                        </template>
                                        <template v-else>
                                            <button
                                                class="flex items-center text-green-600 hover:text-green-900 ml-2 bg-green-100 bg-opacity-70 hover:bg-opacity-100 py-2 px-4 rounded-md"
                                                @click="allowUser(phrase.user_id)"
                                            >
                                                <span class="mr-2">Allow user</span>
                                                <svg
                                                    fill="none"
                                                    stroke="currentColor"
                                                    stroke-linecap="round"
                                                    stroke-linejoin="round"
                                                    stroke-width="2"
                                                    class="w-5 h-5"
                                                    viewBox="0 0 24 24"
                                                >
                                                    <path d="M6 18L18 6M6 6l12 12"></path>
                                                </svg>
                                            </button>
                                        </template>
                                    </template>
                                    <template v-else>
                                        <button
                                            class="flex items-center text-blue-600 hover:text-blue-900 ml-2 bg-blue-100 bg-opacity-70 hover:bg-opacity-100 py-2 px-4 rounded-md"
                                        >
                                            <span>Administrador</span>
                                        </button>
                                    </template>

                                </td>

                            </tr>
                        </tbody>
                    </table>
                </div>
                <div v-else>
                    <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                        <div class="p-6 text-gray-900">You're logged in!</div>
                    </div>
                </div>
            </div>
        </div>
    </AuthenticatedLayout>
</template>
