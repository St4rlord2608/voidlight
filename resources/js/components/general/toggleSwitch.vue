<script setup lang="ts">
import { defineProps, defineEmits } from 'vue';

const props = defineProps({
    // Use modelValue for v-model compatibility
    modelValue: {
        type: Boolean,
        required: true,
    },
    disabled: {
        type: Boolean,
        default: false,
    },
    inputId: {
        type: String,
        required: true
    }
});

const emit = defineEmits(['update:modelValue']);

function handleChange(event: Event) {
    if (!props.disabled) {
        const target = event.target as HTMLInputElement;
        emit('update:modelValue', target.checked);
    }
}
</script>

<template>
    <label class="toggle-switch-wrapper">
        <input
            :id="inputId"
            :name="inputId"
            type="checkbox"
            class="visually-hidden"
            :checked="modelValue"
            @change="handleChange"
            :disabled="disabled"
        />
        <span
            class="toggle-switch"
            :class="{ 'active': modelValue, 'disabled': disabled }"
            aria-hidden="true"
        >
      <span class="toggle-knob"></span>
    </span>
    </label>
</template>

<style scoped>
    .toggle-switch-wrapper {
        display: inline-flex;
        align-items: center;
        cursor: pointer;
        gap: 8px;
        user-select: none;
        width: fit-content;
        margin-left: auto;
        margin-right: auto;
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

    .toggle-switch {
        position: relative;
        display: inline-block;
        width: 40px;
        height: 20px;
        background-color: var(--button-error30);
        border-radius: 20px;
        transition: background-color 0.3s ease;
        vertical-align: middle;
    }

    .toggle-knob {
        position: absolute;
        top: 2px;
        left: 2px;
        width: 16px;
        height: 16px;
        background-color: white;
        border-radius: 50%;
        transition: transform 0.3s ease;
        box-shadow: 0 1px 3px var(--secondary100);
    }

    /* Active (Checked) State */
    .toggle-switch.active {
        background-color: var(--button-success30);
    }

    .toggle-switch.active .toggle-knob {
        transform: translateX(20px);
    }

    .toggle-switch.disabled {
        background-color: #e0e0e0;
        cursor: not-allowed;
        opacity: 0.6;
    }
    .toggle-switch.disabled .toggle-knob {
        background-color: #f5f5f5;
    }
    .toggle-switch-wrapper:has(.visually-hidden:disabled) {
        cursor: not-allowed;
    }
</style>
