<template>
    <section>
        <h1>Play Buzz</h1>
        <div class="buzzer-index-container">
            <div class="join-container">
                <input
                    type="text"
                    v-model="lobbyCode"
                    placeholder="Code"
                    @focus="resetJoinText"
                />
                <div class="input-error" v-if="joinMessage">{{ joinMessage }}</div>
                <button @click="joinLobby">Join Lobby</button>

            </div>
            <div class="create-container">
                <button @click="createLobby">Create Lobby</button>
                <div class="input-error" v-if="createMessage">{{ createMessage }}</div>
            </div>
        </div>
    </section>
</template>

<script setup lang="ts">
import { onMounted, reactive, ref } from 'vue';
import axios from 'axios';
import { initializeTempUserId } from '@/lib/utils'

    const lobbyCode = ref('');

    const user = reactive({
        id: ''
    })

    const joinMessage = ref('');
    const createMessage = ref('');

    const lobbyData = reactive({
        hostId: '',
        lobbyCode: ''
    });

    const buzzerLobbyData = reactive({
        id: '',
        lobbyId: ''
    })


    async function createLobby(){
        createMessage.value = '';
        try{
            const response = await axios.post('/api/buzzer', lobbyData);
            const lobbyCode = response.data?.lobby.lobby_code;
            window.location.href = `/buzzer/${lobbyCode}`;
        }catch (error){
            if(error.response){
                createMessage.value = error.response.data.message;
            }
        }
    }

    async function joinLobby(){
        resetJoinText();
        try{
            if(lobbyCode.value != ''){
                await axios.get(`/api/buzzer/${lobbyCode.value}`)
                window.location.href = `/buzzer/${lobbyCode.value}`;
            }else{
                joinMessage.value = 'Please enter a code';
            }
        }catch(error){
            if(error.response){
                joinMessage.value = error.response.data.message;
            }
        }
    }

    function resetJoinText(){
        joinMessage.value = '';
    }


    onMounted(() => {
        user.id = initializeTempUserId();
        lobbyData.hostId = user.id;
    })
</script>
