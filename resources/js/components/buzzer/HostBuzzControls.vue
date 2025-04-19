<script setup lang="ts">
    import Lock from '@/components/icons/lock.vue';
    import Unlock from '@/components/icons/unlock.vue';

    const props = defineProps({
        buzzerLocked: {
            type: Boolean,
            required: true
        },
        buzzedPlayerName: {
            type: String,
            required: true
        },
        userId: {
            type: String,
            required: true
        }
    })
    const emit = defineEmits([
        'update:buzzer-locked',
        'update:buzzed-player-name',
        'correct-buzzer',
        'false-buzzer'
    ])

    function handleBuzzerChange(newState: boolean){
        emit('update:buzzer-locked', newState)
    }

    function handleCorrectBuzz(){
        if(props.buzzedPlayerName == '') return;
        emit('correct-buzzer');
    }

    function handleFalseBuzz(){
        if(props.buzzedPlayerName == '') return;
        emit('false-buzzer');
    }
</script>

<template>
    <div class="buzz-controls">
        <h2 class="heading">Buzzer</h2>
        <div class="resolve-buzzer-container">
            <button @click="handleCorrectBuzz" v-bind:disabled="buzzedPlayerName == ''" class="correct-button success">✓</button>
            <button
                @click="handleBuzzerChange(!buzzerLocked)"
                :class="[buzzerLocked? 'error' : 'success', 'buzzer-state has-tooltip']"
                :data-tooltip="buzzerLocked? 'unlock buzzer' : 'lock buzzer'">
                <lock v-if="buzzerLocked"/>
                <unlock v-else/>
            </button>
            <button @click="handleFalseBuzz" v-bind:disabled="buzzedPlayerName == ''" class="false-button error">✗</button>
        </div>
        <div class="buzzed-player">{{ buzzedPlayerName }}</div>

    </div>
</template>

<style scoped>
.buzz-controls{
    grid-column: span 3;
    display: flex;
    flex-direction: column;
    align-items: center;
    gap: 10px;

    .buzzer-state{
        width: 50px;
        height: 50px;
        border-radius: 50%;

        &.success{
            .locked-item{
                display: none;
            }
        }

        &.error{
            .unlocked-item{
                display: none;
            }
        }
    }

    .resolve-buzzer-container{
        display: flex;
        flex-direction: row;
        gap: 10px;
    }

    .resolve-buzzer-container{
        .correct-button, .false-button{
            width: 44px;
        }
    }
}
</style>
