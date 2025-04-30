<script setup lang="ts">
import { onMounted, onUnmounted } from 'vue';

    const props = defineProps({
        visible: {
            type: Boolean,
            required: true
        }
    })

    const emit = defineEmits(['update:visible']);

    function closePopup(){
        emit('update:visible', false);
    }

    function handleEscapeKey(event: KeyboardEvent){
        if(event.key === 'Escape' && props.visible){
            closePopup();
        }
    }

    onMounted(() => {
        window.addEventListener('keydown', handleEscapeKey);
    })

    onUnmounted(() => {
        window.removeEventListener('keydown', handleEscapeKey);
    })
</script>

<template>
    <div v-if="visible" class="modal-container">
        <div class="modal-overlay" @click="closePopup"></div>
        <div class="modal-content card">
            <slot></slot>
            <button class="close-btn" @click="closePopup">x</button>
        </div>
    </div>
</template>

<style scoped>
    .modal-container{
        position: fixed;
        top: 0;
        bottom: 0;
        left: 0;
        right: 0;
        z-index: 1000;
        display: flex;
        justify-content: center;
        align-items: center;

        .modal-overlay{
            background: black;
            opacity: 0.6;
            cursor: pointer;
            width: 100%;
            height: 100%;
            position: absolute;
        }

        .modal-content{
            text-align: center;
            align-content: center;
            width: fit-content;
            height: fit-content;
            min-width: 300px;
            min-height: 300px;
            max-width: 80%;
            max-height: 80%;
            position: relative;
            background: var(--secondary40);

            .close-btn{
                position: absolute;
                top: 5px;
                right: 5px;
                width: 30px;
                height: 30px;
                background: var(--button-error30);
                line-height: 0;
                border-radius: 50%;

                &:hover{
                    background: var(--button-error60);
                }
            }
        }
    }
</style>
