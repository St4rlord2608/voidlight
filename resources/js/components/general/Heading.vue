<script setup lang="ts">
    import BackIcon from '@/components/icons/backIcon.vue';
    import HomeIcon from '@/components/icons/homeIcon.vue';

    const props = defineProps({
        backLink: {
            type: String,
            required: false
        },
        homeLink: {
            type: String,
            required: false
        }
    })
</script>

<template>
<div class="heading-container">
    <a v-if="backLink" class="back-icon" :href="backLink"><back-icon size="50px"/></a>
    <h1 class="heading"><slot></slot></h1>
    <a v-if="homeLink" class="home-icon" :href="homeLink"><HomeIcon size="50px"/></a>
</div>
</template>

<style scoped>
    .heading-container{
        display: grid;
        grid-template-columns: 50px 1fr 50px;
        align-items: center;

        .back-icon{
            grid-column: 1;
        }

        .heading{
            grid-column: 2;
        }

        .home-icon{
            grid-column: 3;
        }
    }
</style>
