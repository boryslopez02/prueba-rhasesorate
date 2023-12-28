<script lang="ts" setup>
    import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
    import { Head } from '@inertiajs/vue3';
    import { ref, onMounted, watch } from 'vue';
    import feather from 'feather-icons';

    const props = defineProps({
        count: Number
    });

    interface Phrase {
        text: string;
        isFavorite: boolean;
    }

    const kanyePhrases = ref<object[]>([]);
    const count = props.count;

    // * Iconos
    const heartIcon = feather.icons.heart.toSvg({ class: 'w-6 h-6' });

    // * Fetch Api
    async function fetchKanyePhrase() {
        try {
            const response = await fetch('https://api.kanye.rest/');
            if (!response.ok) {
                throw new Error(`HTTP error! Status: ${response.status}`);
            }
            const data = await response.json();
            kanyePhrases.value.push({ text: data.quote, isFavorite: false });
            console.info("data fetch", data);
        } catch (error) {
            showNotification('Error', 'error');
        }
    }

    const fetchFivekanyePhrase = async () => {
        kanyePhrases.value = [];
        for (let i = 0; i < count; i++) {
            await fetchKanyePhrase();
        }
    };

    // * Actions
    const toggleFavorite = async (phrase: Phrase) => {
        try {
            const token = document.querySelector('meta[name="csrf-token"]').getAttribute('content');

            const response = await fetch(`/phrases/favorite`, {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': token,
                },
                body: JSON.stringify(phrase),
            });
            console.log(response, "res")
            if (!response.ok) {
                throw new Error(`HTTP error! Status: ${response.status}`);
                return
            }

            phrase.isFavorite = true;
            showNotification('Phrase added to favorites', 'success');
        } catch (error) {
            showNotification('Error', 'error');
        }
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

    onMounted(async () => {
        console.log(props, "asfa")

        fetchFivekanyePhrase();
    });
</script>

<template>
    <AuthenticatedLayout>
        <Head title="Phrases" />

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
                    <a :href="route('myphrases')" class="bg-blue-500 text-white hover:bg-blue-600 py-2 px-4 rounded-md">My Phrases</a>
                </div>
                <h1 class="text-2xl font-bold text-center mb-6">Kanye Quotes</h1>
                <div class="phrases-container">
                    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4">
                        <div v-for="(phrase, index) in kanyePhrases" :key="index" class="bg-white rounded-lg shadow-lg p-4 flex flex-col justify-between">
                            <blockquote class="text-gray-600 italic mb-4">
                                "{{ phrase.text }}"
                            </blockquote>
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
