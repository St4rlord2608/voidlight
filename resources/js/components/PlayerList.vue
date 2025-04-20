<script setup lang="ts">

    interface Props{
        players: Player[];
        isHost: boolean;
        userId: string;
        buzzedUserId :string | null;
        showPoints: boolean;
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
            <li :class="[isHost ? '' : 'player', userId == player.userId? 'active-player' : '', isHost && buzzedUserId === player.userId? 'has-buzzed' : '', 'player-card']" v-for="player in players" :key="player.userId">
                <button v-if="isHost" class="adjust-button" @click="emitDecreasePoints(player.userId)">-</button>
                <div class="content">
                    <span class="name">{{ player.name }}</span>
                    <div v-if="isHost" class="host-points-container">
                        <input v-bind:value="player.points" class="points">
                        <div class="point-text">Points</div>
                    </div>
                    <div v-else-if="showPoints" class="player-points">{{ player.points }} Points</div>
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
            justify-content: space-between;
            background: var(--secondary15);
            overflow: hidden;

            &.active-player{
                background-image: linear-gradient(
                to right,
                var(--secondary80) 10%,
                var(--secondary15) 50%
                );
            }

            &.has-buzzed{
                .content{
                    background-image: linear-gradient(
                        to right,
                        var(--button-success20),
                        var(--secondary15)
                    );
                }

            }

            &.player{
                padding: 10px 0;

                .content{
                    padding: 10px 20px;
                }
            }

            .adjust-button{
                background: var(--secondary30);
                border-radius: 0;

                &:hover{
                    background: var(--secondary80);
                    box-shadow: -2px 4px 73px -2px var(--secondary60);
                }
            }

            .content{
                display: flex;
                flex-direction: row;
                width: 100%;
                padding: 5px 10px;
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
