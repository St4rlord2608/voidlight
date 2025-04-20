<script setup lang="ts">

    import { ref } from 'vue';

    const props = defineProps({
        value:{
            type: String,
            required: true
        }
    })

    const isCopied = ref(false);
    let copyTimeoutId = null;
    function handleCopyLink(){
        if(isCopied.value) return;
        console.log(props.value);
        navigator.clipboard.writeText(props.value)
        isCopied.value = true;

        if(copyTimeoutId) clearTimeout(copyTimeoutId);
        copyTimeoutId = setTimeout(() => {
            isCopied.value = false;
            copyTimeoutId = null;
        }, 2000)
    }
</script>

<template>
<span class="copy-container">
    <span class="copyable-text" @click="handleCopyLink" v-if="!isCopied">{{ value }}</span>
    <span v-else>Link Copied!</span>
</span>
</template>

<style scoped>
    .copy-container{
        .copyable-text{
            cursor: pointer;
            &:hover{
                text-decoration: underline;
            }
        }
    }
</style>
