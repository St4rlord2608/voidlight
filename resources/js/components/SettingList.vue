<script setup lang="ts">

</script>

<template>
    <div class="settings-container card">
        <h2>Settings</h2>
        <slot></slot>
    </div>
</template>

<style scoped>
    .settings-container {
        min-width: 400px;
        width: 400px;
        display: flex;
        flex-direction: column;
        gap: 20px;
        padding: 30px 20px;
        height: fit-content;
    }
</style>
