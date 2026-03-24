<script setup>
import { onMounted, ref } from 'vue';
import { fetchAllCards, fetchSetCodes } from '../services/cardService';

const cards = ref([]);
const setCodes = ref([]);
const selectedSetCode = ref('');
const loadingCards = ref(true);

async function loadCards() {
    loadingCards.value = true;
    cards.value = await fetchAllCards(selectedSetCode.value);
    loadingCards.value = false;
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
        <select id="setSelect" v-model="selectedSetCode" @change="loadCards">
            <option value="">Toutes les éditions</option>
            <option v-for="set in setCodes" :key="set" :value="set">{{ set }}</option>
        </select>
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
