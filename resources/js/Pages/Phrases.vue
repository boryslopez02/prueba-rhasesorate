<script lang="ts" setup>
    import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
    import { Head } from '@inertiajs/vue3';
    import { ref, onMounted, watch } from 'vue';
    import feather from 'feather-icons';

    interface Phrase {
        text: string;
        isFavorite: boolean;
    }

    const fetchLimit = 30;
    let lastFetchTime = 0;
    let fetchCount = parseInt(localStorage.getItem('fetchCount')) || 0;
    const quoteCount = ref<number>(0);

    const kanyePhrases = ref<object[]>(JSON.parse(localStorage.getItem('kanyePhrases')) || []);

    console.log(kanyePhrases.value, "kanyePhrases")
    // * Iconos
    const heartIcon = feather.icons.heart.toSvg({ class: 'w-6 h-6' });

    // * Fetch Api
    async function fetchKanyePhrase() {
        const currentTime = Date.now();

        if (fetchCount >= fetchLimit && currentTime - lastFetchTime < 60000) {
            showNotification('You have exceeded the limit of queries per minute', 'error');
            return;
        }

        try {
            const response = await fetch('https://api.kanye.rest/');
            if (!response.ok) {
                throw new Error(`HTTP error! Status: ${response.status}`);
            }
            const data = await response.json();
            kanyePhrases.value.push({ text: data.quote, isFavorite: false });
            console.info("data fetch", data);
            lastFetchTime = currentTime;
            fetchCount++;
            localStorage.setItem('fetchCount', fetchCount.toString());
            localStorage.setItem('kanyePhrases', JSON.stringify(kanyePhrases.value));
        } catch (error) {
            showNotification('Error', 'error');
        }
    }

    const fetchFivekanyePhrase = async () => {
        if (fetchCount < 30) {
            kanyePhrases.value = [];
            for (let i = 0; i < 5; i++) {
                await fetchKanyePhrase();
            }
        } else {
            showNotification('You have exceeded the limit of queries per minute', 'error');
        }
    };

    function resetFetchCount() {
        fetchCount = 0;
        lastFetchTime = Date.now();
        localStorage.setItem('fetchCount', '0');
    }

    setInterval(resetFetchCount, 60000);

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
        console.log('Componente montado', kanyePhrases.value);
        console.log('feather.icons', feather.icons);
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
                <div class="text-center my-3">
                    <input
                        type="number"
                        min="0"
                        max="50"
                        placeholder="Enter num of quotes"
                        v-model="quoteCount"
                        class="py-2 px-4 border border-gray-300 rounded-md focus:outline-none focus:ring focus:border-blue-500 mr-3"
                    >
                    <a :href="quoteCount > 0 ? `/getphrases/${quoteCount}` : '#'" class="bg-blue-500 text-white hover:bg-blue-600 py-2 px-4 rounded-md">Show Phrases</a>
                </div>

                <div class="phrases-container">
                    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4">
                        <div v-for="(phrase, index) in kanyePhrases" :key="index" class="rounded-lg shadow-lg p-4 flex flex-col justify-between" :class="phrase.isFavorite ? 'bg-green-100' : 'bg-white'">
                            <blockquote class="text-gray-600 italic mb-4">
                                "{{ phrase.text }}"
                            </blockquote>
                            <div class="flex justify-end space-x-2">
                                <button @click="toggleFavorite(phrase)" v-if="!phrase.isFavorite" class="text-red-500 hover:text-red-700">
                                    <span v-html="heartIcon"></span>
                                </button>
                                <!-- <button @click="deletePhrase(phrase)" v-else class="text-gray-500 hover:text-gray-700">
                                    <span v-html="trashIcon"></span>
                                </button> -->
                            </div>
                        </div>
                    </div>
                </div>
                <div class="text-center my-5">
                    <button @click="fetchFivekanyePhrase" class="bg-blue-500 text-white hover:bg-blue-600 py-2 px-4 rounded-md">
                        Update Phrases
                    </button>
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
