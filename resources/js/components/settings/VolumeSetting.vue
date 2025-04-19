<script setup lang="ts">
    import RangeSlider from '@/components/general/rangeSlider.vue';
    import { ref } from 'vue';

    const props = defineProps({
        buzzVolume: {
            type: Number,
            required: true
        }
    })

    const emit = defineEmits([
        'update:buzz-volume',
    ])

    function handleBuzzVolumeChange(newValue){
        emit('update:buzz-volume', newValue);
    }
</script>

<template>
<div>
    <h3>Volume</h3>
    <RangeSlider :model-value="buzzVolume" @update:model-value="handleBuzzVolumeChange" :show-value="true">
        Buzzer:
    </RangeSlider>
</div>
</template>

<style scoped>

</style>
