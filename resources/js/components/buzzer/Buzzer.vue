<script setup lang="ts">
    const props = defineProps({
        buzzerLocked: {
            type: Boolean,
            required: true
        },
        buzzedPlayerName: {
            type: String
        }
    })

    const emit = defineEmits([
        'buzz'
    ])

    function handleBuzz(){
        emit('buzz')
    }
</script>

<template>
    <div class="buzz-container card">
        <button @click="handleBuzz" v-if="!buzzerLocked" class="buzzer success">Buzz</button>
        <div v-else class="buzzer disabled">Buzz</div>
        <div class="play-buzzed-user">{{ buzzedPlayerName }}</div>
    </div>
</template>

<style scoped>
.buzz-container{
    display: grid;
    grid-template-columns: repeat(2, 1fr);
    flex-direction: column;
    justify-content: center;
    gap: 20px;
    padding: 30px 20px;

    .buzzer{
        width: 200px;
        height: 200px;
        border-radius: 50%;
        margin-left: auto;
        margin-right: auto;
        grid-column: 1 / span 3;

        &.disabled{
            background: var(--button-warning30);
            align-content: center;
            text-align: center;
            cursor: not-allowed;
        }
    }

    .play-buzzed-user{
        text-align: center;
        grid-column: 1 / span 3;
    }
}
</style>
