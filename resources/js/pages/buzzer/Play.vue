<script setup lang="ts">
import { computed, onMounted, reactive, ref, Ref } from 'vue';
import { initializeTempName, initializeTempUserId } from '@/lib/utils';
import axios from 'axios';

interface Player {
    id: number;
    userId: string;
    name: string;
    points: number;
    textLocked: boolean;
    text: string;
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

    const buzzedPlayerName = computed(() => {
        const targetId = buzzerLobby.buzzedPlayerId;

        if(!targetId){
            return '';
        }
        const player = players.value.find(player => player.userId == targetId);

        if(player){
            return player.name;
        }
        return '';
    })

    const lobby = reactive({
        id: -1,
        hostId: '',
        lobbyCode: '',
        loaded: false
    })

    const owningPlayer = reactive({
        id: -1,
        userId: '',
        name: '',
        points: 0,
        textLocked: false,
        isHost: false
    })

    const players: Ref<Player[]> = ref([]);
    const lobbyExists: Ref<boolean> = ref(false);
    const correctBuzzPoints: Ref<number> = ref(5);
    const falseBuzzPoints: Ref<number> = ref(1);
    const manualChangePoints: Ref<number> = ref(1);
    const hostText: Ref<string> = ref('');

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
            name: player.name,
            points: player.points,
            textLocked: player.text_locked,
            text: ''
        }))
    }

    async function initializePlayer(){
        if(owningPlayer.userId == lobby.hostId){
            owningPlayer.isHost = true;
            return;
        }
        if(checkIfPlayerExists()){
            const playerIndex = players.value.findIndex(existingPlayer => existingPlayer.userId === owningPlayer.userId);
            const existingPlayer = players.value[playerIndex];
            owningPlayer.id = existingPlayer.id;
            owningPlayer.points = existingPlayer.points;
            owningPlayer.textLocked = existingPlayer.textLocked;
        }else{
            try{
                const response = await axios.post(`/api/buzzer/${lobby.lobbyCode}/${owningPlayer.userId}`, owningPlayer);
                const dataBasePlayer = response.data;
                owningPlayer.id = dataBasePlayer.id;
                addPlayer(owningPlayer.userId, owningPlayer.name, owningPlayer.id);
            }catch(error){

            }
        }
    }

    function checkIfPlayerExists(){
        return players.value.some(existingPlayer => existingPlayer.userId === owningPlayer.userId);
    }

    async function changePlayerData(userId: string, points: number, textLocked: boolean){
        const playerIndex = players.value.findIndex(player => player.userId === userId);
        if(playerIndex != -1){
            const player = players.value[playerIndex];
            player.points = points;
            player.textLocked = textLocked;
            await axios.patch(`/api/buzzer/${lobby.lobbyCode}/${player.userId}`, player)
        }
    }

    function getPlayer(userId: string){
        const playerIndex = players.value.findIndex(player => player.userId === userId);
        if(playerIndex != -1){
            return players.value[playerIndex];
        }
        return null;
    }

    async function changeBuzzerLobbyData(buzzerLocked: boolean, buzzedPlayerId: string){
        buzzerLobby.buzzerLocked = buzzerLocked;
        buzzerLobby.buzzedPlayerId = buzzedPlayerId;
        await axios.patch(`/api/buzzer/${lobby.lobbyCode}`, buzzerLobby);
    }

    function addPlayer(userId: string, name: string, id: number = -1, points: number = 0, textLocked: boolean = false){
        if(userId == ''){
            return;
        }
        const newPlayer = {
            id: id,
            userId: userId,
            name: name,
            points: points,
            textLocked: textLocked,
            text: ''
        };
        players.value.push(newPlayer);
    }

    onMounted(() => {
        owningPlayer.userId = initializeTempUserId();
        owningPlayer.name = initializeTempName();
        initializeLobbyData();
        if(lobbyExists.value){
            initializePropPlayers(props.propBuzzerLobby?.buzzer_players);
            initializePlayer();
        }
    })
</script>

<template>
    <section class="buzzer-play-section">
        <h1 class="heading">Buzz</h1>
        <div v-if="lobbyExists" class="buzzer-play-container">
            <div class="player-list-container card">
                <h2 class="player-list-heading">Players</h2>
                <ul class="player-list">
                    <li class="player-card" v-for="player in players" :key="player.userId">
                        <span class="name">{{ player.name }}</span>
                        <div v-if="owningPlayer.isHost" class="host-points-container">
                            <input v-bind:value="player.points" class="points">
                            <div class="point-text">Points</div>
                            <div class="points-adjust-container">
                                <button>-</button>
                                <button>+</button>
                            </div>
                        </div>
                        <div v-else>
                            <div>{{ player.points }} Points</div>
                        </div>
                    </li>
                </ul>
            </div>
            <div class="secondary-container">
                <div class="play-container">
                    <div v-if="!owningPlayer.isHost" class="buzz-container card">
                        <button @click="changeBuzzerLobbyData(true, owningPlayer.userId)" v-if="!buzzerLobby.buzzerLocked" class="buzzer success">Buzz</button>
                        <div v-else class="buzzer disabled">Buzz</div>
                        <div class="play-buzzed-user">{{ buzzedPlayerName }} Buzzed</div>
                    </div>
                    <div v-if="!owningPlayer.isHost" class="text-container">
                        <label class="text-label">Text</label>
                        <textarea class="text"/>
                    </div>
                    <div v-else>
                        <div class="command-container card">

                            <div class="buzz-controls">
                                <h2 class="heading">Buzzer</h2>
                                <div class="locked-buzzer-indicator" v-if="buzzerLobby.buzzerLocked">Buzzer is Locked</div>
                                <div class="unlocked-buzzer-indicator" v-else>Users can buzzer</div>
                                <div class="buzzed-player">{{ buzzedPlayerName }}</div>
                                <div class="buzzer-button-container">
                                    <div class="resolve-buzzer-container">
                                        <button v-bind:disabled="buzzedPlayerName == ''" class="correct-button success">✓</button>
                                        <button v-bind:disabled="buzzedPlayerName == ''" class="false-button error">✗</button>
                                    </div>
                                    <div class="button-state-container">
                                        <button
                                            @click="changeBuzzerLobbyData(true, owningPlayer.userId)" v-bind:disabled="buzzerLobby.buzzerLocked"
                                            class="primary">Lock Buzzer</button>
                                        <button
                                            @click="changeBuzzerLobbyData(false, '')" v-bind:disabled="!buzzerLobby.buzzerLocked"
                                            class="secondary">Reset Buzzer</button>
                                    </div>
                                </div>

                            </div>
                            <div class="host-text-container">
                                <div class="host-text">
                                    <h2 class="heading">Text for users</h2>
                                    <textarea v-bind:value="hostText"/>
                                </div>
                            </div>
                            <div class="player-text-container">
                                <h2 class="heading">Texts from Players</h2>
                                <div class="player-text-lock-button-container">
                                    <button class="warning">Lock all Texts</button>
                                    <button class="secondary">Unlock all Texts</button>
                                </div>
                                <div class="text-container">
                                    <div class="player-text" v-for="player in players" :key="player.userId">
                                        <div class="name-container">
                                            <label class="name">{{ player.name }}</label>
                                            <button @click="changePlayerData(player.userId, player.points, false)" v-if="player.textLocked" class="has-tooltip" data-tooltip="unlock text">🔒</button>
                                            <button @click="changePlayerData(player.userId, player.points, true)" v-if="!player.textLocked" class="has-tooltip" data-tooltip="lock text">🔓</button>
                                        </div>
                                        <textarea v-bind:value="player.text"/>
                                    </div>
                                </div>
                            </div>
                            <div>

                            </div>
                        </div>
                    </div>
                </div>
                <div v-if="owningPlayer.isHost" class="settings-container card">
                    <h2>Settings</h2>
                    <div class="points-controls">
                        <h3 class="heading">Points</h3>
                        <div class="input-container">
                            <div class="input-block">
                                <label class="has-tooltip" data-tooltip="Points for correct buzzer">Correct</label>
                                <input min="0" name="correctBuzz" type="number" v-bind:value="correctBuzzPoints">
                            </div>
                            <div class="input-block">
                                <label class="has-tooltip" data-tooltip="Points for false buzzer">False</label>
                                <input min="0" type="number" v-bind:value="falseBuzzPoints">
                            </div>
                            <div class="input-block">
                                <label class="has-tooltip" data-tooltip="Points for manual point change">Manual</label>
                                <input min="0" type="number" v-bind:value="manualChangePoints">
                            </div>
                        </div>
                    </div>
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
