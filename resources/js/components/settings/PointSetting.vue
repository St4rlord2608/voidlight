<script setup lang="ts">
    const props = defineProps({
        isHost: {
            type: Boolean,
            required: true
        },

        correctPoints: {
            type: Number,
            default: 5
        },

        falsePoints: {
            type: Number,
            default: 1
        },

        manualPoints: {
            type: Number,
            default: 1
        }
    })

    const emit = defineEmits([
        'update:correctPoints',
        'update:falsePoints',
        'update:manualPoints'
    ])

    function emitCorrectPoints(event){
        const numericValue = getNumericValue(event);

        emit('update:correctPoints', numericValue)
    }

    function emitFalsePoints(event){
        const numericValue = getNumericValue(event);

        emit('update:falsePoints', numericValue)
    }

    function emitManualPoints(event){
        const numericValue = getNumericValue(event);

        emit('update:manualPoints', numericValue)
    }

    function getNumericValue(event){
        const rawValue = event.target.value;
        return rawValue === '' ? null : Number(rawValue);
    }
</script>

<template>
    <div v-if="isHost" class="points-controls">
        <h3 class="heading">Points</h3>
        <div class="input-container">
            <div class="input-block">
                <label class="has-tooltip" data-tooltip="Points for correct buzzer">Correct</label>
                <input min="0" name="correctBuzz" type="number" @input="emitCorrectPoints" v-bind:value="correctPoints" />
            </div>
            <div class="input-block">
                <label class="has-tooltip" data-tooltip="Points for false buzzer">False</label>
                <input min="0" type="number" @input="emitFalsePoints" v-bind:value="falsePoints" />
            </div>
            <div class="input-block">
                <label class="has-tooltip" data-tooltip="Points for manual point change">Manual</label>
                <input min="0" type="number" @input="emitManualPoints" v-bind:value="manualPoints" />
            </div>
        </div>
    </div>
</template>

<style scoped>
.points-controls{
    border: 1px solid var(--accent-secondary60);
    padding: 10px 20px;
    border-radius: 20px;
    background: var(--secondary15);
    .input-container{
        display: flex;
        flex-direction: column;
        justify-content: space-around;

        .input-block{
            display: grid;
            grid-template-columns: repeat(2, 1fr);
            gap: 10px;

            input{
                width: 5ch;
                text-align: center;
                margin-left: auto;
                margin-right: auto;
            }
        }
    }
}
</style>
