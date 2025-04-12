<script setup lang="ts">
import { onMounted, reactive, ref, Ref } from 'vue';
import { initializeTempUserId } from '@/lib/utils';
import axios from 'axios';

interface Player {
    id: number;
    userId: string;
    points: number;
    textLocked: boolean;
}

    const props = defineProps({
        propBuzzerLobby:{
            type: Object,
            required: false
        },
        propLobby: {
            type: Object,
            required: false
        },
        propErrorMessage: {
            type: String,
            required: false
        }
    })

    const buzzerLobby = reactive({
        id: -1,
        buzzerLocked: false,
        buzzedPlayerId: '',
        loaded: false
    })

    const lobby = reactive({
        id: -1,
        hostId: '',
        lobbyCode: '',
        loaded: false
    })

    const player = reactive({
        id: -1,
        userId: '',
        points: 0,
        textLocked: false,
        isHost: false
    })

    const players: Ref<Player[]> = ref([]);
    const lobbyExists: Ref<boolean> = ref(false);

    function initializeLobbyData(){
        if(props.propBuzzerLobby && props.propLobby ){
            buzzerLobby.id = props.propBuzzerLobby.id;
            buzzerLobby.buzzerLocked = props.propBuzzerLobby.buzzer_locked;
            buzzerLobby.buzzedPlayerId = props.propBuzzerLobby.buzzed_player_id;

            lobby.id = props.propLobby.id;
            lobby.hostId = props.propLobby.host_id;
            lobby.lobbyCode = props.propLobby.lobby_code

            lobbyExists.value = true;
        }
    }

    function initializePropPlayers(playersData: Ref<Player[]>){
        if(!Array.isArray(playersData)){
            players.value = [];
            return;
        }
        players.value = playersData.map(player => ({
            id: player.id,
            userId: player.user_id,
            points: player.points,
            textLocked: player.text_locked
        }))
    }

    async function initializePlayer(){
        if(player.userId == lobby.hostId){
            player.isHost = true;
            return;
        }
        if(checkIfPlayerExists()){
            const playerIndex = players.value.findIndex(existingPlayer => existingPlayer.userId === player.userId);
            const existingPlayer = players.value[playerIndex];
            player.id = existingPlayer.id;
            player.points = existingPlayer.points;
            player.textLocked = existingPlayer.textLocked;
        }else{
            try{
                const response = await axios.post(`/api/buzzer/${lobby.lobbyCode}/${player.userId}`, player);
            }catch(error){

            }
        }
    }

    function checkIfPlayerExists(){
        return players.value.some(existingPlayer => existingPlayer.userId === player.userId);
    }

    function changePlayerData(userId: string, points: number, textLocked: boolean){
        const playerIndex = players.value.findIndex(player => player.userId === userId);
        if(playerIndex != -1){
            const player = players.value[playerIndex];
            player.points = points;
            player.textLocked = textLocked;
        }
    }

    async function changeBuzzerLobbyData(buzzerLocked: boolean, buzzedPlayerId: string){
        buzzerLobby.buzzerLocked = buzzerLocked;
        buzzerLobby.buzzedPlayerId = buzzedPlayerId;
        await axios.patch(`/api/buzzer/${lobby.lobbyCode}`, buzzerLobby);
    }

    function addPlayer(userId: string, points: number = 0, textLocked: boolean = false){
        if(userId == ''){
            return;
        }
        const newPlayer = {
            id: -1,
            userId: userId,
            points: points,
            textLocked: textLocked
        };
        players.value.push(newPlayer);
    }

    onMounted(() => {
        player.userId = initializeTempUserId();
        player.userId = 'test'
        initializeLobbyData();
        if(lobbyExists.value){
            initializePropPlayers(props.propBuzzerLobby?.buzzer_players);
            initializePlayer();
        }
    })
</script>

<template>
    <section>
        <div v-if="lobbyExists">
            <div>
                <div>
                    <span v-if="player.isHost">You are the host</span>
                    <span v-else> You are a player</span>
                </div>
                <div>General Lobby Data</div>
                <div>BuzzerLobby id: {{ buzzerLobby.id }}</div>
                <div>BuzzerLocked: {{ buzzerLobby.buzzerLocked }}</div>
                <div>buzzedPlayerId: {{ buzzerLobby.buzzedPlayerId }}</div>
                <div>lobbyId: {{ lobby.id }}</div>
                <div>lobby host: {{ lobby.hostId }}</div>
                <div>lobbyCode: {{ lobby.lobbyCode }}</div>
                <button @click="changeBuzzerLobbyData(!buzzerLobby.buzzerLocked, player.userId)">Change buzzer state</button>
                <div v-if="buzzerLobby.buzzerLocked">Buzzer locked</div>
                <div v-else>Buzzer is not locked</div>
            </div>
            <div>
                <div>Player Data:</div>
                <ol v-if="players.length > 0">
                    <li v-for="player in players" :key="player.userId">
                        <span>DB id: {{ player.id }}, </span>
                        <span>user Id: {{ player.userId }}, </span>
                        <span>Points: {{ player.points }}, </span>
                        <span>TextLocked: {{ player.textLocked }}</span>
                    </li>
                </ol>
                <div v-else>
                    There are no players here
                </div>
            </div>
        </div>
        <div v-else>
            Lobby does not exist
        </div>
    </section>
</template>

<style scoped>

</style>
