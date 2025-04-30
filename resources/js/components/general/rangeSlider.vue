<script setup lang="ts">
import { ref, computed, watch, onMounted, onUnmounted } from 'vue';

const props = defineProps({
    modelValue: { // For v-model
        type: Number,
        required: true,
    },
    min: {
        type: Number,
        default: 0,
    },
    max: {
        type: Number,
        default: 100,
    },
    step: {
        type: Number,
        default: 1,
    },
    disabled: {
        type: Boolean,
        default: false,
    },
    showValue: { // Option to display the value next to the slider
        type: Boolean,
        default: false,
    },
    knobSize: { // To calculate offset accurately
        type: Number,
        default: 16, // Should match CSS --knob-size
    }
});

const emit = defineEmits(['update:modelValue']);

const trackRef = ref<HTMLDivElement | null>(null); // Ref for the track element
const isDragging = ref(false);

// Calculate the percentage position of the knob
const valuePercentage = computed(() => {
    const range = props.max - props.min;
    if (range === 0) return 0; // Avoid division by zero
    const correctedValue = Math.min(props.max, Math.max(props.min, props.modelValue));
    return ((correctedValue - props.min) / range) * 100;
});

// Calculate knob offset to center it visually over the value position
const knobOffset = computed(() => {
    // Offset is half the knob size multiplied by its relative position (0 to 1)
    return (props.knobSize / 2) * (valuePercentage.value / 100) * 2;
    // Simplified: return props.knobSize * (valuePercentage.value / 100); if centering isn't perfect
    // Better Simplified: return props.knobSize / 2; if always centering needed
});


// --- Drag Logic ---

function calculateValue(clientX: number): number {
    if (!trackRef.value || props.disabled) return props.modelValue;

    const trackRect = trackRef.value.getBoundingClientRect();
    const trackWidth = trackRect.width;

    // Calculate position relative to the track start
    let position = clientX - trackRect.left;

    // Clamp position within the track bounds
    position = Math.max(0, Math.min(trackWidth, position));

    // Calculate percentage and raw value
    const percentage = position / trackWidth;
    let newValue = percentage * (props.max - props.min) + props.min;

    // Apply step
    if (props.step > 0) {
        newValue = Math.round(newValue / props.step) * props.step;
    }

    // Clamp final value within min/max
    newValue = Math.max(props.min, Math.min(props.max, newValue));

    // Format based on step decimals if necessary (e.g., for step=0.1)
    const stepString = props.step.toString();
    const decimalPlaces = stepString.includes('.') ? stepString.split('.')[1].length : 0;
    return Number(newValue.toFixed(decimalPlaces));
}

function handleTrackOrKnobInteraction(event: MouseEvent | TouchEvent) {
    if (props.disabled) return;
    isDragging.value = true;

    const clientX = 'touches' in event ? event.touches[0].clientX : event.clientX;
    const newValue = calculateValue(clientX);
    emit('update:modelValue', newValue); // Emit immediately on click/touch

    // Add listeners to window for move and up/end
    window.addEventListener('mousemove', handleDragMove);
    window.addEventListener('touchmove', handleDragMove);
    window.addEventListener('mouseup', handleDragEnd);
    window.addEventListener('touchend', handleDragEnd);
}

function handleDragMove(event: MouseEvent | TouchEvent) {
    if (!isDragging.value || props.disabled) return;

    // Prevent page scroll on touch devices
    if ('touches' in event) {
        event.preventDefault();
    }

    const clientX = 'touches' in event ? event.touches[0].clientX : event.clientX;
    const newValue = calculateValue(clientX);

    // Only emit if value actually changes to avoid excessive updates
    if (newValue !== props.modelValue) {
        emit('update:modelValue', newValue);
    }
}

function handleDragEnd() {
    if (!isDragging.value) return;
    isDragging.value = false;

    // Remove window listeners
    window.removeEventListener('mousemove', handleDragMove);
    window.removeEventListener('touchmove', handleDragMove);
    window.removeEventListener('mouseup', handleDragEnd);
    window.removeEventListener('touchend', handleDragEnd);
}

// Update based on hidden input changes (e.g., accessibility tools)
function updateValueFromInput(event: Event) {
    const target = event.target as HTMLInputElement;
    emit('update:modelValue', Number(target.value));
}

// Cleanup listeners when component is unmounted
onUnmounted(() => {
    // Ensure listeners are removed if component is destroyed while dragging
    handleDragEnd();
});
</script>

<template>
    <label class="range-slider-wrapper" :class="{ disabled: disabled }">
        <span v-if="$slots.default" class="range-label">
      <slot></slot>
    </span>
        <input
            type="range"
            class="visually-hidden"
            :value="modelValue"
            :min="min"
            :max="max"
            :step="step"
            :disabled="disabled"
            @input="updateValueFromInput"
            aria-label="Range Slider"
        />
        <div
            ref="trackRef"
            class="slider-track"
            @mousedown.prevent="handleTrackOrKnobInteraction"
            @touchstart.prevent="handleTrackOrKnobInteraction"
        >
            <div class="slider-filled-track" :style="{ width: valuePercentage + '%' }"></div>

            <div
                class="slider-knob"
                :style="{ left: `calc(${valuePercentage}% - ${knobOffset}px)` }"
                @mousedown.prevent="handleTrackOrKnobInteraction"
                @touchstart.prevent="handleTrackOrKnobInteraction"
            ></div>
        </div>

        <span class="range-value-display" v-if="showValue">{{ modelValue }}</span>
    </label>
</template>

<style scoped>
.range-slider-wrapper {
    /* Define CSS variables for easy customization */
    --knob-size: 16px;
    --track-height: 6px;
    --track-bg: var(--secondary30);
    --track-fill-bg: var(--accent-secondary70);
    --knob-bg: white;
    --knob-shadow: 0 1px 3px rgba(0, 0, 0, 0.3);
    --track-border-radius: 3px; /* half of track-height */
    --disabled-opacity: 0.6;
}

.range-slider-wrapper {
    display: inline-flex;
    align-items: center;
    gap: 10px;
    cursor: pointer;
    padding: 5px 0; /* Add some padding for easier clicking */
    user-select: none;
    width: 100%; /* Example: make wrapper take full width */
}
.range-slider-wrapper.disabled {
    cursor: not-allowed;
    opacity: var(--disabled-opacity);
}

.visually-hidden {
    position: absolute;
    width: 1px;
    height: 1px;
    padding: 0;
    margin: -1px;
    overflow: hidden;
    clip: rect(0, 0, 0, 0);
    white-space: nowrap;
    border-width: 0;
}

.slider-track {
    position: relative;
    width: 100%; /* Take available space within wrapper */
    height: var(--track-height);
    background-color: var(--track-bg);
    border-radius: var(--track-border-radius);
    cursor: pointer;
    flex-grow: 1; /* Allow track to grow */
}
.range-slider-wrapper.disabled .slider-track {
    cursor: not-allowed;
}

.slider-filled-track {
    position: absolute;
    left: 0;
    top: 0;
    height: 100%;
    background-color: var(--track-fill-bg);
    border-radius: var(--track-border-radius);
    pointer-events: none; /* Don't interfere with track clicks */
}

.slider-knob {
    position: absolute;
    top: 50%; /* Center vertically */
    transform: translateY(-50%); /* Adjust vertical centering */
    width: var(--knob-size);
    height: var(--knob-size);
    background-color: var(--knob-bg);
    border-radius: 50%;
    box-shadow: var(--knob-shadow);
    cursor: grab;
    z-index: 1; /* Ensure knob is above filled track */
}
.slider-knob:active {
    cursor: grabbing;
}
.range-slider-wrapper.disabled .slider-knob {
    cursor: not-allowed;
}

.range-label, .range-value-display {
    white-space: nowrap; /* Prevent label/value wrapping */
}

/* Optional: Style for value display */
.range-value-display {
    min-width: 30px; /* Ensure space for value */
    text-align: right;
    font-variant-numeric: tabular-nums; /* Keep number width consistent */
}
</style>
