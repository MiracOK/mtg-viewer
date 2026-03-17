<script setup>
// TODO: La page de recheche de cartes.

import { onMounted, ref } from 'vue';
import { useRoute } from 'vue-router';
import { fetchSearchCard } from '../services/cardService';

const route = useRoute();
const cards = ref({});
const loadingCard = ref(true);

async function loadCard(event) {
    loadingCard.value = true;
    cards.value = await fetchSearchCard(event.target.value);
    loadingCard.value = false;
    console.log(card.value)
}

onMounted(() => {
    loadCard(route.params.name);
});

</script>

<template>
    <div>
        <h1>Rechercher une Carte</h1>
    </div>
    <div class="card-list">
        <div>
            <input type="text" @keyup.enter="loadCard($event)"/>
        </div>
        <div v-if="loadingCards">Loading...</div>
        <div v-else>
            <div class="card" v-for="card in cards" :key="card.id">
                <router-link :to="{ name: 'get-card', params: { uuid: card.uuid } }"> {{ card.name }} - {{ card.uuid }}
                </router-link>
            </div>
        </div>
    </div>
</template>
