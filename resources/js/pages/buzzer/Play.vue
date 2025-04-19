<script setup lang="ts">
import { computed, onMounted, onUnmounted, reactive, ref, Ref } from 'vue';
import { initializeTempName, initializeTempUserId, playBuzzSound, playCorrectSound, playFalseSound } from '@/lib/utils';
import axios from 'axios';
import PlayerList from '@/components/PlayerList.vue';
import SettingList from '@/components/SettingList.vue';
import PointSetting from '@/components/settings/PointSetting.vue';
import Buzzer from '@/components/buzzer/Buzzer.vue';
import PlayerText from '@/components/input/PlayerText.vue';
import Echo, { Channel } from 'laravel-echo';
import HostBuzzControls from '@/components/buzzer/HostBuzzControls.vue';
import HostText from '@/components/input/HostText.vue';
import PlayerTextList from '@/components/input/PlayerTextList.vue';
import VolumeSetting from '@/components/settings/VolumeSetting.vue';

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
        loaded: false,
        showPoints: true,
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
        loaded: false,
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
        buzzedPlayerId: buzzerLobby.buzzedPlayerId,
        showPoints: buzzerLobby.showPoints
    }
})

    const players: Ref<Player[]> = ref([]);
    const lobbyExists: Ref<boolean> = ref(false);
    const correctBuzzPoints: Ref<number> = ref(5);
    const falseBuzzPoints: Ref<number> = ref(1);
    const manualChangePoints: Ref<number> = ref(1);
    const hostText: Ref<string> = ref('');
    const buzzVolume : Ref<number> = ref(10);

    const buzzerLobbyChannelName = computed(() => lobby.lobbyCode ? `buzzer.${lobby.lobbyCode}` : null);
    const buzzerPlayersChannelName = computed(() => lobby.lobbyCode ? `buzzer.${lobby.lobbyCode}.playerChange` : null);
    const playerTextsChannelName = computed(() => lobby.lobbyCode ? `buzzer.${lobby.lobbyCode}.playerTextChange` : null);
    const buzzerChannelName = computed(() => lobby.lobbyCode? `buzzer.${lobby.lobbyCode}.buzzerChange` : null);
    let buzzerLobbyEchoChannel: Channel | null = null;
    let buzzerPlayersEchoChannel: Channel | null = null;
    let playerTextsEchoChannel: Channel | null = null;
    let buzzerEchoChannel: Channel | null = null;

    function initializeLobbyData(){
        if(props.propBuzzerLobby && props.propLobby ){
            buzzerLobby.id = props.propBuzzerLobby.id;
            buzzerLobby.buzzerLocked = props.propBuzzerLobby.buzzer_locked;
            buzzerLobby.buzzedPlayerId = props.propBuzzerLobby.buzzed_player_id;
            if(props.propBuzzerLobby.settings.show_points != null){
                buzzerLobby.showPoints = props.propBuzzerLobby.settings.show_points
            }else{
                buzzerLobby.showPoints = true;
            }

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
        if(buzzerLobbyEchoChannel != null){
            buzzerLobbyEchoChannel.listen('Buzzer\\LobbyChanged', (event: any) => {
                if(owningPlayer.userId == event.userId){
                    return;
                }
                buzzerLobby.buzzerLocked = event.buzzerLocked;
                buzzerLobby.buzzedPlayerId = event.buzzedPlayerId;
                buzzerLobby.showPoints = event.showPoints;

            }).error((error: unknown) => {
                console.error(error);
            })
        }
        buzzerPlayersEchoChannel = echoInstance.channel(buzzerPlayersChannelName.value);
        if(buzzerPlayersEchoChannel != null){
            buzzerPlayersEchoChannel.listen('Buzzer\\PlayerChanged', (event: any) => {
                if(owningPlayer.userId == event.userId){
                    return;
                }
                event.buzzerLobby?.buzzer_players.forEach(changedPlayer => {
                    const existingPlayer = getPlayer(changedPlayer.user_id);
                    if(existingPlayer != null){
                        existingPlayer.points = changedPlayer.points;
                        existingPlayer.textLocked = changedPlayer.text_locked;
                    }else{
                        addPlayer(changedPlayer.user_id, changedPlayer.name, changedPlayer.id, changedPlayer.points, changedPlayer.text_locked);
                    }
                    if(changedPlayer.user_id == owningPlayer.userId){
                        owningPlayer.id = changedPlayer.id;
                        owningPlayer.points = changedPlayer.points;
                        owningPlayer.textLocked = changedPlayer.text_locked;
                    }
                })
            })
        }
        playerTextsEchoChannel = echoInstance.channel(playerTextsChannelName.value);
        if(playerTextsEchoChannel != null){
            playerTextsEchoChannel.listen('Player\\TextChanged', (event: any) => {
                if(owningPlayer.userId == event.userId)return;
                if(lobby.hostId == event.userId){
                    hostText.value = event.text;
                    return;
                }
                const player = getPlayer(event.userId);
                if(player != null){
                    player.text = event.text
                }
            })
        }
        buzzerEchoChannel = echoInstance.channel(buzzerChannelName.value);
        if(buzzerEchoChannel != null){
            buzzerEchoChannel.listen('Buzzer\\BuzzerChanged', (event: any) => {
                if(event.buzzerCanceled || event.userId == owningPlayer.userId) return;
                if(event.wasBuzzerLock){
                    playBuzzSound(buzzVolume.value);
                }else if(event.buzzerResult){
                    playCorrectSound(buzzVolume.value);
                }else{
                    playFalseSound(buzzVolume.value);
                }
            })
        }
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
        if(buzzerEchoChannel != null){
            echoInstance.leave(buzzerChannelName.value);
            buzzerEchoChannel = null;
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

    async function handleBuzzerSound(wasBuzzerLock: boolean, buzzerResult: boolean = false, buzzerCanceled: boolean = false){
        if(wasBuzzerLock){
            playBuzzSound(buzzVolume.value);
        }else if(buzzerResult){
            playCorrectSound(buzzVolume.value);
        }else{
            playFalseSound(buzzVolume.value);
        }
        const buzzerAPIPayload = {
            wasBuzzerLock: wasBuzzerLock,
            buzzerResult: buzzerResult,
            buzzerCanceled: buzzerCanceled,
            userId: owningPlayer.userId
        }
        await axios.patch(`/api/broadcast/buzzer/${lobby.lobbyCode}`, buzzerAPIPayload);
    }

    function getPlayer(userId: string){
        const playerIndex = players.value.findIndex(player => player.userId === userId);
        if(playerIndex != -1){
            return players.value[playerIndex];
        }
        return null;
    }

    async function changeBuzzerLobbyData(buzzerLocked: boolean, buzzedPlayerId: string, showPoints: boolean = buzzerLobby.showPoints){
        buzzerLobby.buzzerLocked = buzzerLocked;
        buzzerLobby.buzzedPlayerId = buzzedPlayerId;
        buzzerLobby.showPoints = showPoints
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
        handleBuzzerSound(true)
    }

    function handleBuzzChange(newState: boolean){
        changeBuzzerLobbyData(newState, owningPlayer.userId);
    }

    async function handleCorrectBuzz(){
        const player = getPlayer(buzzerLobby.buzzedPlayerId);
        if(player != null){
            await changePlayerData(player.userId, player.points + correctBuzzPoints.value, player.textLocked)
        }
        await handleBuzzerSound(false, true);
        await changeBuzzerLobbyData(false, owningPlayer.userId)
        await unlockAllTexts();
    }

    async function handleFalseBuzz(){
        const changePlayers = players.value.filter(p => p.userId != buzzerLobby.buzzedPlayerId);
        if(changePlayers.length !== 0){
            changePlayers.forEach(player => {
                player.points += falseBuzzPoints.value;
            });
            await bulkUpdatePlayers(players.value);
        }
        await handleBuzzerSound(false,false);
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

async function lockAllTexts(){
    if(players.value.length === 0)return;
    players.value.forEach(player => {
        player.textLocked = true;
    });
    await bulkUpdatePlayers(players.value);
}

    async function bulkUpdatePlayers(changePlayers: Player[]){
        const payload = {
            players: changePlayers,
            requestingUserId: owningPlayer.userId
        };
        try{
            await axios.patch(`/api/lobby/${lobby.lobbyCode}/users/bulk-update`, payload);
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

    function handlePlayerTextLockChange(payload: any){
        const player = getPlayer(payload.userId);
        if(player != null){
            changePlayerData(player.userId, player.points, payload.newState)
        }
    }

    function handleShowPointsChange(newState: boolean){
        changeBuzzerLobbyData(buzzerLobby.buzzerLocked, buzzerLobby.buzzedPlayerId, newState)
    }

    function handleBuzzThroughSpacebar(event){
        if(event.key === ' ' || event.keyCode === 32){
            const activeElement = document.activeElement;
            const isInputFocused = activeElement && (activeElement.tagName === 'INPUT' ||
            activeElement.tagName === 'TEXTAREA' || activeElement.isContentEditable);
            if(!isInputFocused && !buzzerLobby.buzzerLocked){
                event.preventDefault();
                handleBuzz();
            }
        }
    }

    onMounted(() => {
        owningPlayer.userId = initializeTempUserId();
        owningPlayer.name = initializeTempName();
        initializeLobbyData();
        if(lobbyExists.value){
            initializePropPlayers(props.propBuzzerLobby?.buzzer_players);
            initializePlayer();
            initializeBroadcastListeners();
            if(!owningPlayer.isHost){
                window.addEventListener('keydown', handleBuzzThroughSpacebar)
            }
        }

    })

    onUnmounted(() => {
        if(!owningPlayer.isHost){
            window.removeEventListener('keydown', handleBuzzThroughSpacebar)
        }
        leaveBroadcastListeners();
    })
</script>

<template>
    <section class="buzzer-play-section">
        <h1 class="heading">Buzz</h1>
        <div v-if="lobbyExists" class="buzzer-play-container">
            <PlayerList class="player-list"
                        :players="players"
                        :is-host="owningPlayer.isHost"
                        :show-points="buzzerLobby.showPoints"
                        @decrease-points="handleDecreasePoints"
                        @increase-points="handleIncreasePoints"/>
            <setting-list class="settings-list">
                <point-setting
                    :is-host="owningPlayer.isHost"
                    v-model:correct-points="correctBuzzPoints"
                    v-model:false-points="falseBuzzPoints"
                    v-model:manual-points="manualChangePoints"
                    v-model:show-points="buzzerLobby.showPoints"
                    @update:show-points="handleShowPointsChange"/>
                <volume-setting v-model:buzz-volume="buzzVolume"/>
            </setting-list>

            <div class="main-section">
                <Buzzer v-if="!owningPlayer.isHost" :buzzer-locked="buzzerLobby.buzzerLocked" :buzzed-player-name="buzzedPlayerName" @buzz="handleBuzz"/>
                <HostText class="card" v-if="!owningPlayer.isHost" v-model:host-text="hostText" :is-host="owningPlayer.isHost"/>
                <PlayerText v-if="!owningPlayer.isHost"
                            :text="owningPlayer.text"
                            :text-locked="owningPlayer.textLocked"
                            @update:text="handlePlayerTextChange"
                            @update:text-locked="handlePlayerTextLock"/>
                <div v-else class="command-container card">

                    <HostBuzzControls
                        :buzzer-locked="buzzerLobby.buzzerLocked"
                        :buzzed-player-name="buzzedPlayerName"
                        :user-id="owningPlayer.userId"
                        @update:buzzer-locked="handleBuzzChange"
                        @correct-buzzer="handleCorrectBuzz"
                        @false-buzzer="handleFalseBuzz"/>
                    <HostText v-model:host-text="hostText" :is-host="owningPlayer.isHost" @update:host-text="changePlayerText"/>
                    <PlayerTextList :players="players"
                                    @change-player-text-lock="handlePlayerTextLockChange"
                                    @lock-all="lockAllTexts"
                                    @unlock-all="unlockAllTexts"/>
                </div>
            </div>

        </div>
        <div v-else>
            Lobby does not exist
        </div>
    </section>
</template>

<style scoped>
    .buzzer-play-section{
        .buzzer-play-container{
            display: grid;
            gap: 40px;
            grid-template-columns: 400px 1fr;

            .player-list{
                grid-row: 1;
                grid-column: 1;
            }

            .settings-list{
                grid-row: 2;
                grid-column: 1;
            }

            .main-section{
                grid-row: 1 / span 2;
                grid-column: 2;
                display: flex;
                flex-direction: column;
                gap: 20px;
            }

            .command-container{
                display: grid;
                grid-template-columns: repeat(2, 1fr);
                flex-direction: column;
                justify-content: center;
                gap: 20px;
                padding: 30px 20px;
            }
        }
    }

    @media screen and (max-width: 1400px){

    }

    @media screen and (max-width: 980px){
        .buzzer-play-section{
            .buzzer-play-container{
                grid-template-columns: 1fr;

                .player-list{
                    grid-row: 1;
                    grid-column: 1;
                }

                .settings-list{
                    grid-row: 3;
                    grid-column: 1;
                }

                .main-section{
                    grid-row: 2;
                    grid-column: 1;
                }
            }
        }
    }
</style>
