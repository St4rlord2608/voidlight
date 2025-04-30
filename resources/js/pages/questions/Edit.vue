<script setup lang="ts">

import { Clue, Question, QuestionsData } from '@/types/Question';
import { Category } from '@/types/Category';
import { QuestionSubLobby, SubLobby } from '@/types/subLobby';
import ToggleSwitch from '@/components/general/toggleSwitch.vue';
import { onMounted, ref, Ref, watch } from 'vue';
import {
    addCategory,
    addQuestion, categoryExists,
    getCategoriesForQuestion, getCategory,
    loadQuestionsData,
    questionContainsLobbyType,
    saveQuestionsData
} from '@/lib/question';
import TextAutocompleteInput from '@/components/input/TextAutocompleteInput.vue';
import Heading from '@/components/general/Heading.vue';

interface Props{
    subLobbies: SubLobby,
    questionId: number,
}

const props = withDefaults(defineProps<Props>(), {})

const questionSubLobbies: Ref<QuestionSubLobby[]> = ref([]);
const question: Ref<Question> = ref({id: -1, question: '', answer: '', clues: [], lobbyTypes: [], categoryIds: []});
const categories: Ref<Category[]> = ref([]);
const allCategoryNames: Ref<string[]> = ref([]);
const questionsData: Ref<QuestionsData> = ref([]);
const isNew: Ref<boolean> = ref(false);
const newCategoryName: Ref<string> = ref('');

watch(questionSubLobbies, () => {
    question.value.lobbyTypes = questionSubLobbies.value.filter(subLobby => subLobby.active).map(activeSubLobby => activeSubLobby.lobby_type);
}, {deep:true})

function handleSave(){
    if(isNew.value){
        question.value.id = questionsData.value.nextQuestionId;
        questionsData.value = addQuestion(questionsData.value, question.value);
    }
    const newCategories = categories.value.filter(category => category.id == -1);
    newCategories.forEach(newCat => {
        questionsData.value = addCategory(questionsData.value, newCat.name, question.value);
    })
    saveQuestionsData(questionsData.value);
}

function handleAddClue(){
    const newClue: Clue = { value: '', order: question.value.clues.length + 1};
    question.value.clues.push(newClue);
}

function handleAddCategory(){
    let category: Category = {id: -1, name: newCategoryName.value};
    if(categoryExists(questionsData.value, category)){
        category = getCategory(questionsData.value, category.name);
        if(!question.value.categoryIds)question.value.categoryIds = [];
        if(!question.value.categoryIds.some(id => id == category.id)){
            question.value.categoryIds.push(category.id);
        }
    }
    if(!categories.value.some(cat => cat.name == category.name)){
        categories.value.push(category);
    }
    newCategoryName.value = '';
}

function handleRemoveCategory(category: Category){
    const index = categories.value.findIndex(cat => cat.name == category.name);
    if(index != -1){
        categories.value.splice(index, 1);
    }
    if(category.id != -1){
        const index = question.value.categoryIds.findIndex(id => id == category.id);
        if(index != -1){
            question.value.categoryIds.splice(index, 1);
        }
    }
}

function handleDeleteClue(clue: Clue){
    const index = question.value.clues.findIndex(c => c.order == clue.order);
    if(index != -1){
        question.value.clues.splice(index, 1);
        reorderClues();
    }
}

function reorderClues(){
    if(!question.value || !question.value.clues) return;
    question.value.clues.sort((a, b) => a.order - b.order);
    question.value.clues.forEach((clue, index) => {
        clue.order = index + 1;
    })
}

onMounted(() => {
    questionsData.value = loadQuestionsData();
    if(questionsData.value.categories){
        allCategoryNames.value = questionsData.value.categories.map(category => category.name);
        console.log(allCategoryNames.value)
        console.log(questionsData.value.categories)
    }
    const index = questionsData.value.questions.findIndex(quest => quest.id == props.questionId);
    if(index != -1){
        question.value = questionsData.value.questions[index];
        categories.value = getCategoriesForQuestion(questionsData.value, question.value);
    }else{
        isNew.value = true;
    }
    if(question.value.clues){
        question.value.clues.sort((a, b) => {
            return a.order - b.order;
        })
    }
    questionSubLobbies.value = props.subLobbies.map(subLobby => ({
        id: subLobby.id,
        lobby_type: subLobby.lobby_type,
        label: subLobby.label,
        active: questionContainsLobbyType(question.value, subLobby)
    }));
})
</script>

<template>
<section class="edit-question-section">
    <heading v-if="!isNew" back-link="/questions" home-link="/">Edit Question</heading>
    <heading v-else back-link="/questions" home-link="/">Create question</heading>
    <div class="edit-container">
        <div class="lobby-type-container card">
            <h2>Lobby types</h2>
            <div class="lobby-type-setting" v-bind:key="questionSubLobby.id" v-for="questionSubLobby in questionSubLobbies">
                <label :for="questionSubLobby.label">{{ questionSubLobby.label }}</label>
                <toggle-switch :input-id="questionSubLobby.lobby_type" v-model:model-value="questionSubLobby.active"/>
            </div>
        </div>
        <div class="clue-container card">
            <h2>Clues</h2>
            <ul class="clue-list">
                <li class="clue-card" v-bind:key="clue.order" v-for="clue in question.clues">
                    <h3 class="clue-order">{{ clue.order }}.</h3>
                    <div class="clue-setting-container">
                        <textarea v-model="clue.value"/>
                        <button class="error" @click="handleDeleteClue(clue)">Delete</button>
                    </div>
                </li>
                <li class="add-clue-card">
                    <button class="success" @click="handleAddClue">Add Clue</button>
                </li>
            </ul>
        </div>
        <div class="value-container card">
            <div class="input-container">
                <div class="input-block">
                    <h2>Question</h2>
                    <textarea v-model="question.question"/>
                </div>
                <div class="input-block">
                    <h2>Answer</h2>
                    <textarea v-model="question.answer"/>
                </div>
                <div class="category-input-block">
                    <h2>Categories</h2>
                    <div class="new-category-input-block">
                        <TextAutocompleteInput v-model:model-value="newCategoryName"
                                               :items="allCategoryNames"
                                               placeholder="Enter category"
                                                @on-enter="handleAddCategory"/>
                        <button class="success add-category" @click="handleAddCategory">Add category</button>
                    </div>
                    <div class="category-list">
                        <div class="category-item" v-bind:key="category.id" v-for="category in categories">
                            {{ category.name }}
                            <button class="error" @click="handleRemoveCategory(category)">x</button>
                        </div>
                    </div>
                </div>
                <div class="button-container">
                    <button class="success" @click="handleSave">Save</button>
                </div>
            </div>
        </div>
    </div>
</section>
</template>

<style scoped>
    .edit-question-section{
        .edit-container{
            display: grid;
            grid-template-columns: 1fr 2fr;
            gap: 40px;

            .lobby-type-container{
                grid-column: 1;
                grid-row: 1;

                .lobby-type-setting{
                    display: grid;
                    grid-template-columns: 1fr 1fr;
                }
            }
            .clue-container{
                grid-column: 1;
                grid-row: 2;

                .clue-list{
                    display: flex;
                    flex-direction: column;
                    gap: 20px;
                }

                .clue-card{
                    display: grid;
                    grid-template-columns: 1fr 20fr;
                    gap: 30px;
                    background: var(--secondary15);
                    border-radius: 20px;
                    padding: 5px 10px;

                    .clue-order{
                        align-content: center;
                    }

                    .clue-setting-container{
                        display: flex;
                        width: 100%;
                    }
                }

                .add-clue-card{
                    display: flex;
                    justify-content: center;
                }
            }
            .value-container{
                grid-column: 2;
                grid-row: 1 / span 2;

                .input-container{
                    display: flex;
                    flex-direction: column;
                    gap: 20px;

                    .category-input-block{
                        .new-category-input-block{
                            margin-bottom: 20px;
                        }

                        .category-list{
                            display: flex;
                            gap: 15px;
                            flex-wrap: wrap;
                            padding: 10px 20px;
                            background: var(--secondary20);
                            border-radius: 10px;

                            .category-item{
                                padding: 5px 10px;
                                display: flex;
                                flex-direction: row;
                                gap: 10px;
                                justify-content: space-between;
                                border: 1px solid var(--accent-secondary50);
                                border-radius: 20px;
                                background: var(--secondary15);
                                overflow: hidden;
                                align-items: center;

                                button{
                                    width: 30px;
                                    height: 30px;
                                    border-radius: 50%;
                                    line-height: 0;
                                }
                            }
                        }
                    }
                }

                .new-category-input-block{
                    display: flex;

                    .add-category{
                        min-width: fit-content;
                    }
                }
            }
        }
    }
</style>
