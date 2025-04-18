<script setup lang="ts">
import { computed, onMounted, onUnmounted, reactive, ref, Ref } from 'vue';
import { initializeTempName, initializeTempUserId } from '@/lib/utils';
import axios from 'axios';
import PlayerList from '@/components/PlayerList.vue';
import SettingList from '@/components/SettingList.vue';
import PointSetting from '@/components/settings/PointSetting.vue';
import Buzzer from '@/components/buzzer/Buzzer.vue';
import PlayerText from '@/components/input/PlayerText.vue';
import Echo, { Channel } from 'laravel-echo';
import HostBuzzControls from '@/components/buzzer/HostBuzzControls.vue';

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

    const echoInstance = window.Echo as Echo;

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
        isHost: false,
        text: ''
    })

const buzzerLobbyAPIPayload = computed(() => {
    return {
        id: buzzerLobby.id,
        userId: owningPlayer.userId,
        buzzerLocked: buzzerLobby.buzzerLocked,
        buzzedPlayerId: buzzerLobby.buzzedPlayerId
    }
})

    const players: Ref<Player[]> = ref([]);
    const lobbyExists: Ref<boolean> = ref(false);
    const correctBuzzPoints: Ref<number> = ref(5);
    const falseBuzzPoints: Ref<number> = ref(1);
    const manualChangePoints: Ref<number> = ref(1);
    const hostText: Ref<string> = ref('');

    const buzzerLobbyChannelName = computed(() => lobby.lobbyCode ? `buzzer.${lobby.lobbyCode}` : null);
    const buzzerPlayersChannelName = computed(() => lobby.lobbyCode ? `buzzer.${lobby.lobbyCode}.playerChange` : null);
    const playerTextsChannelName = computed(() => lobby.lobbyCode ? `buzzer.${lobby.lobbyCode}.playerTextChange` : null);
    let buzzerLobbyEchoChannel: Channel | null = null;
    let buzzerPlayersEchoChannel: Channel | null = null;
    let playerTextsEchoChannel: Channel | null = null;

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
        }));
    }

    function initializeBroadcastListeners(){
        buzzerLobbyEchoChannel = echoInstance.channel(buzzerLobbyChannelName.value);
        buzzerLobbyEchoChannel.listen('Buzzer\\LobbyChanged', (event: unknown) => {
            if(owningPlayer.userId == event.userId){
                return;
            }
            buzzerLobby.buzzerLocked = event.buzzerLocked;
            buzzerLobby.buzzedPlayerId = event.buzzedPlayerId;

        }).error((error: unknown) => {
            console.log(error);
        })

        buzzerPlayersEchoChannel = echoInstance.channel(buzzerPlayersChannelName.value);
        buzzerPlayersEchoChannel.listen('Buzzer\\PlayerChanged', (event: unknown) => {
            console.log("Player change")
            console.log(event)
            console.log(owningPlayer)
            if(owningPlayer.userId == event.userId){
                return;
            }
            console.log(event)
            initializePropPlayers(event.buzzerLobby?.buzzer_players);
            initializePlayer();
        })

        playerTextsEchoChannel = echoInstance.channel(playerTextsChannelName.value);
        playerTextsEchoChannel.listen('Player\\TextChanged', (event: unknown) => {
            const player = getPlayer(event.userId);
            if(player != null){
                player.text = event.text
            }
        })
    }

    function leaveBroadcastListeners(){
        if(buzzerLobbyEchoChannel != null){
            echoInstance.leave(buzzerLobbyChannelName.value);
            buzzerLobbyEchoChannel = null;
        }
        if(buzzerPlayersEchoChannel != null){
            echoInstance.leave(buzzerPlayersChannelName.value);
            buzzerPlayersEchoChannel = null;
        }
        if(playerTextsEchoChannel != null){
            echoInstance.leave(playerTextsChannelName.value);
            playerTextsEchoChannel = null;
        }
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
            }catch(error: unknown){
                console.error(error);
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
            const playerAPIPayload = {
                id: player.id,
                requestingUserId: owningPlayer.userId,
                name: player.name,
                points: player.points,
                textLocked: player.textLocked,
            }
            await axios.patch(`/api/buzzer/${lobby.lobbyCode}/${player.userId}`, playerAPIPayload)
        }
    }

    async function changePlayerText(text: string){
        const playerTextAPIPayload = {
            text: text
        }
        await axios.patch(`/api/broadcast/playerText/${lobby.lobbyCode}/${owningPlayer.userId}`, playerTextAPIPayload)
    }

    function getPlayer(userId: string){
        const playerIndex = players.value.findIndex(player => player.userId === userId);
        if(playerIndex != -1){
            return players.value[playerIndex];
        }
        return null;
    }

    async function changeBuzzerLobbyData(buzzerLocked: boolean, buzzedPlayerId: string){
        console.log("was here");
        buzzerLobby.buzzerLocked = buzzerLocked;
        buzzerLobby.buzzedPlayerId = buzzedPlayerId;
        await axios.patch(`/api/buzzer/${lobby.lobbyCode}`, buzzerLobbyAPIPayload.value);
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

    function handleIncreasePoints(userId:string){
        const player = getPlayer(userId);
        if(player != null){
            changePlayerData(userId, player?.points + manualChangePoints.value, player?.textLocked);
        }
    }

    function handleDecreasePoints(userId:string){
        const player = getPlayer(userId);
        if(player != null){
            changePlayerData(userId, player?.points - manualChangePoints.value, player?.textLocked);
        }
    }

    function handleBuzz(){
        changeBuzzerLobbyData(true, owningPlayer.userId);
    }

    function handleBuzzChange(newState: boolean){
        changeBuzzerLobbyData(newState, owningPlayer.userId);
    }

    async function handleCorrectBuzz(){
        const player = getPlayer(buzzerLobby.buzzedPlayerId);
        if(player != null){
            await changePlayerData(player.userId, player.points + correctBuzzPoints.value, player.textLocked)
        }
        await changeBuzzerLobbyData(false, owningPlayer.userId)
        await unlockAllTexts();
    }

    async function handleFalseBuzz(){
        const changePlayers = players.value.filter(p => p.userId != buzzerLobby.buzzedPlayerId);

        if(changePlayers.length === 0){
            return;
        }

        changePlayers.forEach(player => {
            player.points += falseBuzzPoints.value;
        });
        await changeBuzzerLobbyData(false, owningPlayer.userId)
        await unlockAllTexts();
    }

    async function unlockAllTexts(){
        if(players.value.length === 0)return;
        players.value.forEach(player => {
            player.textLocked = false;
        });
        await bulkUpdatePlayers(players.value);
    }

    async function bulkUpdatePlayers(changePlayers: Player[]){
        const payload = {
            players: changePlayers,
            requestingUserId: owningPlayer.userId
        };
        try{
            const response = await axios.patch(`/api/lobby/${lobby.lobbyCode}/users/bulk-update`, payload);
            console.log(response)
        }catch(error: any){
            console.error(error);
        }
    }

    function handlePlayerTextChange(text: string){
        changePlayerText(text);
    }

    function handlePlayerTextLock(){
        owningPlayer.textLocked = true;
        changePlayerData(owningPlayer.userId, owningPlayer.points, true)
    }

    onMounted(() => {
        owningPlayer.userId = initializeTempUserId();
        owningPlayer.name = initializeTempName();
        initializeLobbyData();
        if(lobbyExists.value){
            initializePropPlayers(props.propBuzzerLobby?.buzzer_players);
            initializePlayer();
            initializeBroadcastListeners();
        }

    })

    onUnmounted(() => {
        leaveBroadcastListeners();
    })
</script>

<template>
    <section class="buzzer-play-section">
        <h1 class="heading">Buzz</h1>
        <div v-if="lobbyExists" class="buzzer-play-container">
            <PlayerList
            :players="players"
            :is-host="owningPlayer.isHost"
            @decrease-points="handleDecreasePoints"
            @increase-points="handleIncreasePoints"/>

            <div class="secondary-container">
                <div class="play-container">
                <Buzzer v-if="!owningPlayer.isHost" :buzzer-locked="buzzerLobby.buzzerLocked" :buzzed-player-name="buzzedPlayerName" @buzz="handleBuzz"/>
                <PlayerText v-if="!owningPlayer.isHost"
                            :text="owningPlayer.text"
                            :text-locked="owningPlayer.textLocked"
                            @update:text="handlePlayerTextChange"
                            @update:text-locked="handlePlayerTextLock"/>
                <div v-else>
                    <div class="command-container card">

                        <HostBuzzControls
                            :buzzer-locked="buzzerLobby.buzzerLocked"
                            :buzzed-player-name="buzzedPlayerName"
                            :user-id="owningPlayer.userId"
                        @update:buzzer-locked="handleBuzzChange"
                        @correct-buzzer="handleCorrectBuzz"
                        @false-buzzer="handleFalseBuzz"/>
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
                <setting-list>
                    <point-setting
                        :is-host="owningPlayer.isHost"
                        v-model:correct-points="correctBuzzPoints"
                        v-model:false-points="falseBuzzPoints"
                        v-model:manual-points="manualChangePoints"/>
                </setting-list>
            </div>
        </div>
        <div v-else>
            Lobby does not exist
        </div>
    </section>
</template>

<style scoped>

</style>
