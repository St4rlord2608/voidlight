<script setup lang="ts">
     import { onMounted } from 'vue';

     const props = defineProps({
         text: {
             type: String,
             required: true
         },

         textLocked:{
             type: Boolean,
             required: true
         }
     })

     const emit = defineEmits([
         'update:text',
         'update:text-locked'
     ])

     function handleLockText(){
        emit('update:text-locked', true)
     }

     function handleTextInput(event: any){
        emit('update:text', event.target.value);
     }

     onMounted(() => {
         console.log(props.textLocked)
     })

</script>

<template>
    <div class="text-container">
        <label class="text-label">Text</label>
        <div class="text-lock-container">
            <textarea v-bind:readonly="textLocked" @input="handleTextInput" class="text"/>
            <button v-if="!textLocked" @click="handleLockText" class="lock-text">
                <span class="unlocked-item">🔓</span>
                <span class="locked-item">🔒</span>
            </button>
            <div v-else class="locked-text">🔒</div>
        </div>

    </div>
</template>

<style scoped>
.text-container{
    display: flex;
    flex-direction: column;
    gap: 10px;

    .text-label{
        text-align: center;
    }
    .text-lock-container{
        display: flex;

        .text{
            max-height: 300px;
            border-bottom-right-radius: 0;
            border-top-right-radius: 0;
        }

        .lock-text, .locked-text{
            align-content: center;
            text-align: center;
            border-radius: 0 10px 10px 0;
            padding: 20px;
        }

        .lock-text{
            background: var(--button-success60);

            .locked-item{
                display: none;
            }

            &:hover{
                background: var(--button-success100);
                box-shadow: -2px 4px 73px -2px var(--button-success60);
                .unlocked-item{
                    display: none;
                }
                .locked-item{
                    display: block;
                }
            }
        }

        .locked-text{
            background: var(--button-error20);
            cursor: not-allowed;
        }
    }

}
</style>
