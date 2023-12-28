<script lang="ts" setup>
    import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
    import { Head } from '@inertiajs/vue3';
    import { ref, onMounted, watch } from 'vue';
    import feather from 'feather-icons';

    const props = defineProps({
        phrases: Array,
    });

    const kanyePhrases = ref<object[]>([]);

    interface Phrase {
        text: string;
        isFavorite: boolean;
    }
    const token = document.querySelector('meta[name="csrf-token"]').getAttribute('content');

    // * Iconos
    const trashIcon = feather.icons.trash.toSvg({ class: 'w-6 h-6' });

    // * Fetch Api
    const getMyPhrases = async () => {
        try {
            const response = await fetch(`/getmyphrases`, {
                method: 'GET',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': token,
                },
            });

            if (!response.ok) {
                throw new Error(`HTTP error! Status: ${response.status}`);
            }

            let result = await response.json();
            kanyePhrases.value = result.myphrases;
        } catch (error) {
            console.error('Error al agregar frase a favoritos:', error);

            showNotification('Error', 'error');
        }
    };

    // * Actions
    const deletePhrase = async (phrase: Phrase) => {
        const response = await fetch(`/deletephrase`, {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': token,
            },
            body: JSON.stringify(phrase),
        });

        if (!response.ok) {
            throw new Error(`HTTP error! Status: ${response.status}`);
        }

        const indexToDelete = kanyePhrases.value.findIndex((item) => item.id === phrase.id);

        // Si se encontró el índice, elimina el elemento del array
        if (indexToDelete !== -1) {
            kanyePhrases.value.splice(indexToDelete, 1);
        }

        showNotification('Phrase removed from favorites', 'error');
    };

    const show = ref(false);
    const message = ref('');
    const type = ref('info');

    watch(show, (newVal) => {
        if (newVal) {
            let timeout = setTimeout(() => {
                show.value = false;
                clearTimeout(timeout);
            }, 3000);
        }
    });

    const showNotification = (msg: string, notificationType: string) => {
        message.value = msg;
        type.value = notificationType;
        show.value = true;
    };

    // Ejemplo de cómo se podría usar el hook onMounted,
    // aunque para este caso no es necesario si solo muestras datos.
    onMounted(async () => {
        console.log('Componente montado', kanyePhrases);
        console.log('feather.icons', feather.icons);
        getMyPhrases();
    });
</script>

<template>
    <AuthenticatedLayout>
        <Head title="My Phrases" />

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

        <div class="text-gray-800 min-h-screen p-4">
            <div class="container mx-auto my-5">
                <div class="text-end">
                    <a :href="route('phrases')" class="bg-blue-500 text-white hover:bg-blue-600 py-2 px-4 rounded-md">Phrases</a>
                </div>
                <h1 class="text-2xl font-bold text-center mb-6">Kanye Quotes</h1>
                <div class="phrases-container">
                    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4">
                        <div v-for="(phrase, index) in kanyePhrases" :key="index" class="bg-white rounded-lg shadow-lg p-4 flex flex-col justify-between">
                            <blockquote class="text-gray-600 italic mb-4">
                                "{{ phrase.text }}"
                            </blockquote>
                            <div class="flex justify-end space-x-2">
                                <button @click="deletePhrase(phrase)" class="text-gray-500 hover:text-gray-700">
                                    <span v-html="trashIcon"></span>
                                </button>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </AuthenticatedLayout>

</template>

<style>
    body {
        background-color: #e6e6e6; /* Esto es un gris claro */
    }
    .phrases-container {
        min-height: 250px;
    }
</style>
