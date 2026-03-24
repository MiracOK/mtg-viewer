<script setup>
import { onMounted, ref } from 'vue';
import { fetchSearchCard, fetchSetCodes } from '../services/cardService';

const searchInput = ref('');
const cards = ref([]);
const setCodes = ref([]);
const selectedSetCode = ref('');
const loadingCards = ref(false);

async function handleSearch() {
    if (searchInput.value.length >= 3) {
        loadingCards.value = true;
        try {
            const results = await fetchSearchCard(searchInput.value, selectedSetCode.value);
            cards.value = results || [];
        } catch (e) {
            cards.value = [];
        }
        loadingCards.value = false;
    } else {
        cards.value = [];
    }
}

onMounted(async () => {
    setCodes.value = await fetchSetCodes();
});
</script>

<template>
    <div>
        <h1>Rechercher une Carte</h1>
    </div>

    <div class="filters">
        <label for="setSelect">Filtrer par édition :</label>
        <select id="setSelect" v-model="selectedSetCode" @change="handleSearch">
            <option value="">Toutes les éditions</option>
            <option v-for="set in setCodes" :key="set" :value="set">{{ set }}</option>
        </select>
    </div>

    <div class="card-list">
        <div>
            <label for="searchInput">Nom de la carte</label>
            <input
                id="searchInput"
                v-model="searchInput"
                type="text"
                placeholder="Entrez au moins 3 caractères..."
                @input="handleSearch"
            />
        </div>
        <div v-if="loadingCards">Recherche en cours...</div>
        <div v-else-if="cards.length > 0">
            <div class="card-result" v-for="card in cards" :key="card.uuid">
                <router-link :to="{ name: 'get-card', params: { uuid: card.uuid } }">
                    {{ card.name }} <span>({{ card.uuid }})</span>
                </router-link>
            </div>
        </div>
        <div v-else-if="searchInput.length >= 3 && !loadingCards">
            Aucune carte trouvée pour "{{ searchInput }}"
        </div>
    </div>
</template>
