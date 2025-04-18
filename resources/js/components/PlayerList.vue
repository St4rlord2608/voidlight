<script setup lang="ts">

    interface Props{
        players: Player[];
        isHost: boolean;
    }

    const props = withDefaults(defineProps<Props>(), {})

    const emit = defineEmits([
        'decrease-points',
        'increase-points'
    ])

    function emitDecreasePoints(userId:string){
        emit('decrease-points', userId);
    }

    function emitIncreasePoints(userId:string){
        emit('increase-points', userId);
    }
</script>

<template>
    <div class="player-list-container card">
        <h2 class="player-list-heading">Players</h2>
        <ul class="player-list">
            <li :class="[isHost ? '' : 'player', 'player-card']" v-for="player in players" :key="player.userId">
                <button v-if="isHost" class="adjust-button" @click="emitDecreasePoints(player.userId)">-</button>
                <div class="content">
                    <span class="name">{{ player.name }}</span>
                    <div v-if="isHost" class="host-points-container">
                        <input v-bind:value="player.points" class="points">
                        <div class="point-text">Points</div>
                    </div>
                    <div v-else class="player-points">{{ player.points }} Points</div>
                </div>
                <button v-if="isHost" class="adjust-button" @click="emitIncreasePoints(player.userId)">+</button>
            </li>
        </ul>
    </div>
</template>

<style scoped>
.player-list-container{
    width: 100%;
    border-radius: 10px;
    padding: 30px 20px;
    height: fit-content;

    .player-list{
        display: flex;
        flex-direction: column;
        gap: 15px;

        .player-card{
            border: 1px solid var(--accent-secondary50);
            border-radius: 20px;
            display: flex;
            flex-direction: row;
            gap: 10px;
            justify-content: space-between;
            background: var(--secondary15);
            overflow: hidden;

            &.player{
                padding: 10px 20px;
            }

            .adjust-button{
                background: var(--secondary30);
                border-radius: 0;
            }

            .content{
                display: flex;
                flex-direction: row;
                width: 100%;
                padding: 5px 0;
                gap: 10px;

                .name{
                    width: 100%;
                    align-content: center;
                }
            }

            .host-points-container{
                display: flex;
                gap: 5px;
                min-width: fit-content;

                .points{
                    width: 4ch;
                    padding: 0 5px;
                    text-align: end;

                    &:focus{
                        outline: none;
                    }
                }

                .point-text{
                    align-content: center;
                }
            }

            .player-points{
                min-width: fit-content;
                align-content: center;
            }
        }
    }
}

@media screen and (max-width: 1400px){

}
</style>
