<script setup lang="ts">
    const props = defineProps({
        hostText: {
            type: String,
            required: true
        },

        isHost: {
            type: Boolean,
            required: true
        }
    })

    const emit = defineEmits([
        'update:host-text'
    ])

    function handleTextChange(event: any){
        emit('update:host-text', event.target.value);
    }
</script>

<template>
    <div class="host-text-container">
        <div class="host-text">
            <h2 class="heading">{{ isHost ? 'Text for users' : 'Text from host'}}</h2>
            <textarea v-bind:readonly="!isHost" @input="handleTextChange" v-bind:value="hostText"/>
        </div>
    </div>
</template>

<style scoped>
    .host-text-container{
        grid-column: span 3;

        textarea{
            max-height: 300px;
        }
    }
</style>
