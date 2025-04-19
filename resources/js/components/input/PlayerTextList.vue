<script setup lang="ts">
    import Unlock from '@/components/icons/unlock.vue';
    import Lock from '@/components/icons/lock.vue';

    interface Props{
        players: Player[];
    }

    const props = withDefaults(defineProps<Props>(), {})

    const emit = defineEmits([
        'change-player-text-lock',
        'unlock-all',
        'lock-all'
    ])

    function handlePlayerTextLockChange(userId: string, newState: boolean){
        emit('change-player-text-lock', {userId: userId, newState: newState});
    }

    function handleLockAll(){
        emit('lock-all');
    }

    function handleUnlockAll(){
        emit('unlock-all')
    }
</script>

<template>
    <div class="player-text-container">
        <div class="heading-container">
            <button @click="handleUnlockAll" class="success has-tooltip" data-tooltip="unlock all texts"><unlock/></button>
            <h2 class="heading">Texts from Players</h2>
            <button @click="handleLockAll" class="error has-tooltip" data-tooltip="lock all texts"><lock/></button>
        </div>

        <div class="text-container">
            <div class="player-text" v-for="player in players" :key="player.userId">
                <div class="name-container">
                    <label class="name">{{ player.name }}</label>
                    <button @click="handlePlayerTextLockChange(player.userId, !player.textLocked)" class="lock-button has-tooltip"
                            :data-tooltip="player.textLocked ? 'unlock text' : 'lock text'">
                    <lock v-if="player.textLocked"/>
                    <unlock v-else/></button>
                </div>
                <textarea readonly v-bind:value="player.text"/>
            </div>
        </div>
    </div>
</template>

<style scoped>
.player-text-container{
    grid-column: span 3;
    display: flex;
    flex-direction: column;
    gap: 20px;

    .heading-container{
        display: flex;
        flex-direction: row;
        justify-content: center;
        gap: 20px;

        button{
            margin-bottom: 10px;
        }
    }

    .text-container{
        display: grid;
        grid-template-columns: repeat(3, 1fr);
        gap: 20px;
        justify-content: center;

        .player-text{
            min-width: 25%;
            display: flex;
            flex-direction: column;
            gap: 10px;
            .name-container{
                display: flex;
                gap: 10px;

                button{
                    padding: 0;

                    &::after{
                        font-size: 16px;
                        line-height: 1.5;
                    }
                }
                .name{
                    align-content: center;
                    width: 100%;
                }
            }

            textarea{
                max-height: 200px;
                height: 100%;
            }
        }
    }
}

    @media screen and (max-width: 1400px){
        .player-text-container{
            .text-container{
                grid-template-columns: repeat(2, 1fr);
            }
        }
    }

    @media screen and (max-width: 500px){
        .player-text-container{
            .text-container{
                grid-template-columns: 1fr;
            }
        }
    }
</style>
