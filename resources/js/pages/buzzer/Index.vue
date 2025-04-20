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
                <input
                    type="text"
                    v-model="user.userName"
                    placeholder="Code"
                    @focus="resetJoinText"
                />
                <button @click="joinLobby">Join Lobby</button>
                <div class="input-error" v-if="joinMessage">{{ joinMessage }}</div>

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
import { initializeTempName, initializeTempUserId, setTempName } from '@/lib/utils';

    const lobbyCode = ref('');

    const user = reactive({
        userId: '',
        userName: ''
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
            console.error(error)
            if(error.response){
                createMessage.value = error.response.data.message;
            }
        }
    }

    async function joinLobby(){
        resetJoinText();
        try{
            if(lobbyCode.value != '' && user.userName != ''){
                await axios.get(`/api/buzzer/${lobbyCode.value}`, {params:user})
                setTempName(user.userName);
                window.location.href = `/buzzer/${lobbyCode.value}`;
            }else if(lobbyCode.value == ''){
                joinMessage.value = 'Please enter a code';
            }else{
                joinMessage.value = 'Please enter a username'
            }
        }catch(error){
            console.log(error)
            if(error.response){
                joinMessage.value = error.response.data.message;
            }
        }
    }

    function resetJoinText(){
        joinMessage.value = '';
    }


    onMounted(() => {
        user.userId = initializeTempUserId();
        user.userName = initializeTempName();
        lobbyData.hostId = user.userId;
    })
</script>
