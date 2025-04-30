<script setup lang="ts">
import { onMounted, reactive, ref } from 'vue';
import axios from 'axios';
import { initializeTempName, initializeTempUserId, setTempName } from '@/lib/utils';
import { SubLobby } from '@/types/subLobby';
import Heading from '@/components/general/Heading.vue';

interface Props{
    subLobbies: SubLobby[]
}

const props = withDefaults(defineProps<Props>(), {})

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


async function createLobby(subLobby: SubLobby){
    createMessage.value = '';
    try{
        const response = await axios.post(`/api/${subLobby.lobby_type}`, lobbyData);
        const lobbyCode = response.data?.lobby.lobby_code;
        window.location.href = `/${subLobby.lobby_type}/${lobbyCode}`;
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
            const response = await axios.get(`/api/lobby/${lobbyCode.value}`, {params:user})
            console.log(response)
            setTempName(user.userName);
            window.location.href = `/${response.data.lobby.sub_lobby.lobby_type}/${lobbyCode.value}`;
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
    console.log(props)
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
                    <input
                        type="text"
                        v-model="user.userName"
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
