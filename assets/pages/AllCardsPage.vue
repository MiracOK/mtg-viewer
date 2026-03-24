<script setup>
import { onMounted, ref, computed } from 'vue';
import { fetchAllCards, fetchSetCodes } from '../services/cardService';

const cards = ref([]);
const setCodes = ref([]);
const selectedSetCode = ref('');
const loadingCards = ref(true);
const currentPage = ref(1);
const totalItems = ref(0);
const limit = ref(100);

const totalPages = computed(() => Math.ceil(totalItems.value / limit.value));

async function loadCards() {
    loadingCards.value = true;
    const result = await fetchAllCards(selectedSetCode.value, currentPage.value);
    cards.value = result.data || [];
    totalItems.value = result.total || 0;
    limit.value = result.limit || 100;
    loadingCards.value = false;
}

async function onSetCodeChange() {
    currentPage.value = 1;
    await loadCards();
}

async function nextPage() {
    if (currentPage.value < totalPages.value) {
        currentPage.value += 1;
        await loadCards();
    }
}

async function prevPage() {
    if (currentPage.value > 1) {
        currentPage.value -= 1;
        await loadCards();
    }
}

onMounted(async () => {
    setCodes.value = await fetchSetCodes();
    loadCards();
});

</script>

<template>
    <div>
        <h1>Toutes les cartes</h1>
    </div>

    <div class="filters">
        <label for="setSelect">Filtrer par édition :</label>
        <select id="setSelect" v-model="selectedSetCode" @change="onSetCodeChange">
            <option value="">Toutes les éditions</option>
            <option v-for="set in setCodes" :key="set" :value="set">{{ set }}</option>
        </select>
    </div>

    <div class="pagination" v-if="!loadingCards && totalItems > 0" style="margin: 20px 0;">
        <button type="button" :disabled="currentPage === 1" @click="prevPage">Précédent</button>
        <span style="margin: 0 15px;">Page {{ currentPage }} sur {{ totalPages }} ({{ totalItems }} cartes)</span>
        <button type="button" :disabled="currentPage === totalPages" @click="nextPage">Suivant</button>
    </div>

    <div class="card-list">
        <div v-if="loadingCards">Loading...</div>
        <div v-else>
            <div class="card-result" v-for="card in cards" :key="card.id">
                <router-link :to="{ name: 'get-card', params: { uuid: card.uuid } }">
                    {{ card.name }} <span>({{ card.uuid }})</span>
                </router-link>
            </div>
        </div>
    </div>
</template>
