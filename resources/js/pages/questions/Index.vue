<script setup lang="ts">
import { Question, QuestionsData } from '@/types/Question';
import { onMounted, ref, Ref, watch } from 'vue';
    import { Category } from '@/types/Category';
import { deleteQuestion, loadQuestionsData, saveQuestionsData } from '@/lib/question';
import Heading from '@/components/general/Heading.vue';

    interface Props{
        propQuestions: Question[]

        propCategories: Category[]

        propErrorMessage?: object | null
    }

    const props = withDefaults(defineProps<Props>(), {
        propErrorMessage: null
    })

    const questions: Ref<Question[]> = ref([]);
    const categories: Ref<Category[]> = ref([]);
    const questionsData: Ref<QuestionsData> = ref([]);

    watch(questionsData, (newData) => {
        saveQuestionsData(newData);
    }, { deep:true })

     function handleDeleteQuestion(question: Question){
        questionsData.value = deleteQuestion(questionsData.value, question);
    }

    function handleEdit(question: Question){
        window.location.href = `/question/${question.id}`;
    }

    function handleCreate(){
        window.location.href = `/question/new`;
    }

    onMounted(() => {
        questions.value = props.propQuestions;
        categories.value = props.propCategories;
        console.log(questions.value)
        questionsData.value = loadQuestionsData();
    })
</script>

<template>
    <section class="question-listing-section">
        <Heading back-link="/" home-link="/">Questions</Heading>
        <div class="questions-container">
            <div class="question-filter-container card">

            </div>
            <div class="question-listing-container card">
                <h2>Manage</h2>
                <ul class="question-listing">
                    <li class="question-card" v-bind:key="question.id" v-for="question in questionsData.questions">
                        <div class="question-text">{{ question.question }}</div>
                        <div class="manage-button-container">
                            <button class="warning" @click="handleEdit(question)">Edit</button>
                            <button class="error" @click="handleDeleteQuestion(question)">Delete</button>
                        </div>
                    </li>
                    <li class="add-card"><button class="success" @click="handleCreate">New</button></li>
                </ul>
            </div>
        </div>
    </section>
</template>

<style scoped>
.question-listing-section{
    .questions-container{
        display: grid;
        gap: 40px;
        grid-template-columns: 400px 1fr;

        .question-listing-container{
            .question-listing{
                padding: 10px 20px;
                display: flex;
                flex-direction: column;
                gap: 20px;
                .question-card{
                    padding: 5px 10px;
                    display: flex;
                    flex-direction: row;
                    justify-content: space-between;
                    border: 1px solid var(--accent-secondary50);
                    border-radius: 20px;
                    background: var(--secondary15);
                    overflow: hidden;

                    .question-text{
                        align-content: center;
                    }

                    .manage-button-container{
                        display: flex;
                        flex-direction: row;
                        gap: 10px;
                    }
                }

                .add-card{
                    align-self: center;
                }
            }
        }
    }
}
</style>
