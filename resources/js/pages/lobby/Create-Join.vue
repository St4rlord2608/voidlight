<script setup lang="ts">
import { onMounted, ref } from 'vue';
import axios from 'axios';
import { SubLobby } from '@/types/subLobby';
import Heading from '@/components/general/Heading.vue';

interface Props{
    subLobbies: SubLobby[]
}

const props = withDefaults(defineProps<Props>(), {})

const lobbyCode = ref('');

const joinMessage = ref('');
const createMessage = ref('');

async function createLobby(subLobby: SubLobby){
    createMessage.value = '';
    try{
        const response = await axios.post(`/${subLobby.lobby_type}`);
        const lobbyCode = response.data?.lobby.lobby_code;
        window.location.href = `/${subLobby.lobby_type}/${lobbyCode}`;
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
            const response = await axios.get(`/lobbies/${lobbyCode.value}`)
            window.location.href = `/${response.data.lobby_type}/${lobbyCode.value}`;
        }else if(lobbyCode.value == ''){
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

})
</script>

<template>
    <section>
        <heading back-link="/" home-link="/">Play</heading>
        <div class="join-create-grid">
            <div class="join-container">
                <h2>Join Lobby</h2>
                <div class="join-form">
                    <input
                        type="text"
                        v-model="lobbyCode"
                        placeholder="Code"
                        @focus="resetJoinText"
                    />
                    <button @click="joinLobby">Join Lobby</button>
                    <div class="input-error" v-if="joinMessage">{{ joinMessage }}</div>
                </div>

            </div>
            <div class="create-container">
                <h2>Create Lobby</h2>
                <ul class="lobby-type-container">
                    <li class="lobby-type-card" :key="subLobby.lobby_type" v-for="subLobby in subLobbies"
                    @click="createLobby(subLobby)">
                        <div>{{ subLobby.label }}</div>
                    </li>
                </ul>
                <div class="input-error" v-if="createMessage">{{ createMessage }}</div>
            </div>
        </div>
    </section>
</template>

<style scoped>
.join-create-grid{
    width: 100%;
    margin-top: 100px;
    display: grid;
    grid-template-columns: repeat(2, 1fr);

    .join-container, .create-container{
        display: flex;
        flex-direction: column;
        width: 100%;
    }

    .join-container{
        gap: 20px;

        .join-form{
            padding: 10px 20px;
            border-radius: 20px;
            display: flex;
            flex-direction: column;
            gap: 10px;
            max-height: 500px;
            overflow: auto;
        }

        input{
            background: var(--secondary20);
            border: 1px solid var(--accent-secondary50);
            margin-left: auto;
            margin-right: auto;
            border-radius: 10px;
            padding: 10px;
            outline: none;

            &:focus{
                border: 1px solid var(--accent-secondary100);
            }
        }

        button{
            width: fit-content;
            margin-left: auto;
            margin-right: auto;
            background: var(--secondary40);
            border: 1px solid var(--accent-secondary30);
            padding: 10px 20px;
            border-radius: 10px;

            &:hover{
                background: var(--secondary100);
                cursor: pointer;
            }
        }
    }

    .create-container{
        gap: 20px;

        .lobby-type-container{
            padding: 10px 20px;
            border-radius: 20px;
            display: flex;
            flex-direction: column;
            gap: 10px;
            max-height: 500px;
            overflow: auto;
            .lobby-type-card{
                border: 1px solid var(--accent-primary50);
                border-radius: 20px;
                display: flex;
                flex-direction: row;
                background: var(--primary20);
                overflow: hidden;
                padding: 10px 20px;
                justify-content: center;
                min-height: fit-content;

                &:hover{
                    background: var(--primary100);
                    cursor: pointer;
                }
            }
        }
    }
}

</style>
