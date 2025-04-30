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
        width: 100%;
        display: flex;
        flex-direction: column;
        gap: 20px;
        padding: 30px 20px;
        height: fit-content;
    }
</style>
