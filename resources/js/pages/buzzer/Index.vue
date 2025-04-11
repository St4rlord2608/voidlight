<template>
    <div>
        <button @click="createLobby">Create Lobby</button>
    </div>
    <div>
        <input/>
        <button>Join Lobby</button>
    </div>
</template>

<script setup lang="ts">
import { onMounted, reactive, ref } from 'vue';
import axios from 'axios';

const lobbyData = reactive({
    HostId: '123'
});
const count = ref(0);
doSomething();
function doSomething() {
    count.value++;
    console.log(count.value);
}

    async function createLobby(){
        try{
            const response = await axios.post('/api/lobby', lobbyData);
            console.log(response.data)
        }catch (error){
            console.error(error)
        }
    }

    onMounted(() => {
        console.log("mounted")
        doSomething();
    })
</script>
