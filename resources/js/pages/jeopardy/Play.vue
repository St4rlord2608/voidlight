<script setup lang="ts">
import Heading from '@/components/general/Heading.vue';
import CopyText from '@/components/general/copy-text.vue';
import { Lobby } from '@/types/lobby';
import { JeopardyLobby } from '@/types/jeopardy';
import { Question, QuestionsData } from '@/types/Question';
import { onMounted, ref, Ref } from 'vue';
import { loadQuestionsData } from '@/lib/question';
import Modal from '@/components/general/Modal.vue';
import { Category } from '@/types/Category';

    interface Props{
        propLobby: Lobby,
        propJeopardyLobby: JeopardyLobby,
        propErrorMessage?: object | null
    }

    const props = withDefaults(defineProps<Props>(), {
        propErrorMessage: null
    })

    const questionsData: Ref<QuestionsData> = ref([]);
    const availableCategories: Ref<Category[]> = ref([]);
    const availableQuestions: Ref<Question[]> = ref([]);
    const showQuestionModal: Ref<boolean> = ref(false);
    const showCategoryModal: Ref<boolean> = ref(false);

    function toggleCategorySelection(){
        showCategoryModal.value = true;
    }

    onMounted(() => {
        questionsData.value = loadQuestionsData();
        availableCategories.value = questionsData.value.categories;
        console.log(props.propLobby);
        console.log(props.propJeopardyLobby);
        console.log(props.propErrorMessage);
        console.log(questionsData.value);
    })

</script>

<template>
    <section>
        <Heading back-link="/join" home-link="/">Jeopardy: <copy-text value="hello"/></Heading>
        <div v-if="true" class="jeopardy-play-container">
            <div class="main-section card">
                <div>
                    <button @click="toggleCategorySelection">Category 1</button>
                    <ul>
                        <li>
                            <button>+</button>
                        </li>
                    </ul>
                </div>
            </div>
        </div>
        <Modal v-model:visible="showQuestionModal"><div>This is a Question test modal</div></Modal>
        <Modal v-model:visible="showCategoryModal"><div>This is a Category test modal</div></Modal>
    </section>
</template>

<style scoped>
    .jeopardy-play-container{
        .main-section{
            display: grid;
            grid-template-columns: repeat(5, 1fr);
        }
    }
</style>
