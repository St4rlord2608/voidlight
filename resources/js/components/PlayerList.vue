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
            <li class="player-card" v-for="player in players" :key="player.userId">
                <span class="name">{{ player.name }}</span>
                <div v-if="isHost" class="host-points-container">
                    <input v-bind:value="player.points" class="points">
                    <div class="point-text">Points</div>
                    <div class="points-adjust-container">
                        <button @click="emitDecreasePoints(player.userId)">-</button>
                        <button @click="emitIncreasePoints(player.userId)">+</button>
                    </div>
                </div>
                <div v-else>
                    <div>{{ player.points }} Points</div>
                </div>
            </li>
        </ul>
    </div>
</template>

<style scoped>
.player-list-container{
    min-width: 400px;
    width: 400px;
    border-radius: 10px;
    padding: 30px 20px;
    height: fit-content;

    .player-list{
        display: flex;
        flex-direction: column;
        gap: 15px;

        .player-card{
            border: 1px solid var(--accent-secondary50);
            padding: 10px 20px;
            border-radius: 20px;
            display: flex;
            flex-direction: row;
            gap: 20px;
            justify-content: space-between;
            background: var(--secondary15);

            .host-points-container{
                display: flex;
                gap: 5px;

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

                .points-adjust-container{
                    display: flex;
                    flex-direction: row;
                    align-items: center;
                    button{
                        padding: 0;
                        width: 30px;
                        height: 30px;
                        border-radius: 50%;
                        margin: 0 5px;
                        background: var(--secondary30);
                    }
                }
            }
        }
    }
}
</style>
