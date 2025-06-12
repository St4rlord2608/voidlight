<script setup lang="ts">
import Heading from '@/components/general/Heading.vue';
import CopyText from '@/components/general/copy-text.vue';
import { DBLobby, Lobby } from '@/types/lobby';
import {
    BoardCell,
    BoardColumn, DBBoardCell,
    DBBoardColumn,
    DBJeopardyBoardCell,
    JeopardyBoardCell,
    JeopardyLobby
} from '@/types/jeopardy';
import { DBQuestion, Question, QuestionsData } from '@/types/Question';
import { onMounted, ref, Ref } from 'vue';
import { loadQuestionsData } from '@/lib/question';
import Modal from '@/components/general/Modal.vue';
import { Category, DBCategory } from '@/types/Category';
import TextAutocompleteInput from '@/components/input/TextAutocompleteInput.vue';
import axios from 'axios';

    interface Props{
        propLobby: DBLobby,
        propJeopardyLobby: JeopardyLobby,
        propErrorMessage?: object | null
    }

    const props = withDefaults(defineProps<Props>(), {
        propErrorMessage: null
    })

    const questionsData: Ref<QuestionsData> = ref([]);
    const availableCategories: Ref<Category[]> = ref([]);
    const availableCategoryNames: Ref<string[]> = ref([]);
    const availableQuestions: Ref<Question[]> = ref([]);
    const newCatName: Ref<string> = ref("");
    const filteredQuestions: Ref<Question[]> = ref([]);
    const newQuestionPoints: Ref<number> = ref(100);
    const showQuestionModal: Ref<boolean> = ref(false);
    const showCategoryModal: Ref<boolean> = ref(false);
    const boardColumns: Ref<BoardColumn[]> = ref([]);
    const DBBoardColumns: Ref<DBBoardColumn[]> = ref([]);
    const showErrorMessage: Ref<boolean> = ref(false);
    const errorMessage: Ref<string> = ref('');
    const isEditing: Ref<boolean> = ref(true);

    function toggleCategorySelection(){
        showCategoryModal.value = true;
    }

    function switchIsEditing(newIsEditing: boolean){
        isEditing.value = newIsEditing;
    }

    function reorderBoardColumnCells(boardColumn: BoardColumn){
        boardColumn.cells.sort((a, b) => a.points - b.points);
    }

function reorderDBBoardColumnCells(boardColumn: DBBoardColumn){
    boardColumn.cells.sort((a, b) => a.points - b.points);
}

    function filterCategories(){
        const allCategories: Category[] = questionsData.value.categories;
        const allQuestions: Question[] = questionsData.value.questions;
        availableCategories.value = allCategories.filter(category => {
            const hasMatchingQuestion = allQuestions.some(question => {
                const belongsToCategory = question.categoryIds.includes(category.id);

                const meetsLobbyCriteria = question.lobbyTypes.includes('jeopardy') ||question.lobbyTypes.length === 0;

                return belongsToCategory && meetsLobbyCriteria;
            });
            return hasMatchingQuestion;
        })
    }

    function addNewCat(){
        if(boardColumns.value.length >= 5)return;
        const cat = availableCategories.value.find(cat => cat.name === newCatName.value);
        if(cat){
            const newBoardColumn: BoardColumn = {
                category: cat,
                cells: [],
            }
            boardColumns.value.push(newBoardColumn);
        }else{
            errorMessage.value = `Category '${newCatName.value}' not found`;
            showErrorMessage.value = true;
        }
        showCategoryModal.value = false;
        newCatName.value = '';
    }

    function toggleQuestionSelection(category: Category){
        filteredQuestions.value = availableQuestions.value.filter(question => question.categoryIds.some(id => id == category.id));
        showQuestionModal.value = true;
    }

    async function addNewQuestion(question: Question){
        if(newQuestionPoints.value == null || isNaN(newQuestionPoints.value) || newQuestionPoints.value == 0){
            newQuestionPoints.value = 100;
        }
        const index = boardColumns.value.findIndex(boardColumn => question.categoryIds.some(id => id == boardColumn.category.id));
        if(index != -1){
            const selectedBoardColumn = boardColumns.value[index];
            const newBoardCell: BoardCell = {
                points: newQuestionPoints.value,
                question: question,
                answered: false
            }
            try{
                const payload = {
                    category: {
                        localId: selectedBoardColumn.category.id,
                        name: selectedBoardColumn.category.name
                    },
                    question:{
                        localId: question.id,
                        question: question.question,
                        answer: question.answer,
                        clues: question.clues.map(c => ({value: c.value, order: c.order}))
                    },
                    points: newQuestionPoints.value,
                    userId: 'testUser'
                }
                const response = await axios.post(`/api/jeopardy/${props.propLobby.lobby_code}/board_cell`, payload);
                addNewDBBoardCell(response.data[0]);
                selectedBoardColumn.cells.push(newBoardCell);
                reorderBoardColumnCells(selectedBoardColumn);
            }catch(error){
                errorMessage.value = 'Error in uploading question. Please try again'
                showErrorMessage.value = true;
                showQuestionModal.value = false;
            }

        }else{
            errorMessage.value = 'Selected question does not have the selected Category'
            showErrorMessage.value = true;
            showQuestionModal.value = false;
        }
        showQuestionModal.value = false;
    }

    function addNewDBBoardCell(dbBoardCell: DBJeopardyBoardCell){
        const index = DBBoardColumns.value.findIndex(boardColumn => dbBoardCell.category.local_id == boardColumn.category.local_id);
        let selectedDBBoardColumn: DBBoardColumn;
        if(index != -1){
            selectedDBBoardColumn = DBBoardColumns.value[index];
        }else{
            selectedDBBoardColumn = {
                category: dbBoardCell.category,
                cells: [],
            }
            DBBoardColumns.value.push(selectedDBBoardColumn)
        }
        const newDBBoardCell: DBBoardCell = {
            points: dbBoardCell.points,
            question: dbBoardCell.question,
            answered: dbBoardCell.answered,
        }
        selectedDBBoardColumn.cells.push(newDBBoardCell);
        reorderDBBoardColumnCells(selectedDBBoardColumn);
        console.log(dbBoardCell)
        console.log(DBBoardColumns.value)
    }

    function deleteBoardColumn(deleteBoardColumn: BoardColumn){
        const index = boardColumns.value.findIndex(boardColumn => boardColumn == deleteBoardColumn)
        if(index != -1){
            boardColumns.value.splice(index, 1)
        }
    }

    function deleteBoardCell(boardColumn: BoardColumn, boardCell: BoardCell){
        const index = boardColumn.cells.findIndex(cell => cell == boardCell);
        if(index != -1){
            boardColumn.cells.splice(index, 1);
            reorderBoardColumnCells(boardColumn);
        }
    }

    onMounted(() => {
        questionsData.value = loadQuestionsData();
        filterCategories();
        availableQuestions.value = questionsData.value.questions;
        boardColumns.value.push({
            category: availableCategories.value[0],
            cells: []
        });
        availableCategoryNames.value = availableCategories.value.map(category => category.name);
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
                <div class="button-container">
                    <button v-if="isEditing" @click="switchIsEditing(false)" class="primary">switch to Play</button>
                    <button v-else @click="switchIsEditing(true)" class="warning">switch to Edit</button>
                </div>
                <div v-if="isEditing" class="edit-container">
                    <div class="edit-column" :key="boardColumn.category.id" v-for="boardColumn in boardColumns">
                        <div class="edit-category">
                            <div class="category-name">{{ boardColumn.category.name }}</div>
                            <button class="delete-category error" @click="deleteBoardColumn(boardColumn)">-</button>
                        </div>
                        <ul class="edit-row">
                            <li class="edit-row-item" :key="boardCell.question.id" v-for="boardCell in boardColumn.cells">
                                <div class="edit-question">
                                    <div class="question-points">{{ boardCell.points }}</div>
                                    <button class="delete-question error" @click="deleteBoardCell(boardColumn, boardCell)">-</button>
                                </div>
                            </li>
                            <li class="edit-row-item" v-if="boardColumn.cells.length < 5">
                                <button class="add-question" @click="toggleQuestionSelection(boardColumn.category)">+</button>
                            </li>
                        </ul>
                    </div>
                    <div class="add-column" v-if="boardColumns.length < 5">
                        <button class="add-category" @click="toggleCategorySelection">new Category</button>
                    </div>
                </div>
                <div class="play-container" v-else>
                    <div class="column" :key="boardColumn.category.id" v-for="boardColumn in DBBoardColumns">
                        <div class="category-name">{{ boardColumn.category.name }}</div>
                        <ul class="row">
                            <li class="row-item" :key="boardCell.question.id" v-for="boardCell in boardColumn.cells">
                                    <button class="question-points">{{ boardCell.points }}</button>
                            </li>
                        </ul>
                    </div>
                </div>
            </div>
        </div>
        <Modal class="question-modal" v-model:visible="showQuestionModal"><div>
            <div class="question-points-container">
                <label for="points">Points</label>
                <input id="points" name="points" v-model="newQuestionPoints" type="number"/>
            </div>
            <div>Questions</div>
            <ul class="question-list" v-if="filteredQuestions.length > 0">
                <li class="question-list-item"
                    :key="question.id"
                    v-for="question in filteredQuestions"
                    @click="addNewQuestion(question)">
                    <div class="question-tab">{{ question.question }}</div>
                    <div class="clue-count-tab">Clues: {{ question.clues.length }}</div>
                </li>
            </ul>
            <div v-else>
                No questions for this category found
            </div>
        </div></Modal>
        <Modal v-model:visible="showCategoryModal"><div>
            <TextAutocompleteInput
                :items="availableCategoryNames"
                v-model:model-value="newCatName"
                @on-enter="addNewCat"/>
        </div></Modal>
        <Modal v-model:visible="showErrorMessage">
            {{ errorMessage }}
        </Modal>
    </section>
</template>

<style scoped>
    .jeopardy-play-container{
        .button-container{
            display: flex;
            justify-content: center;
            margin-bottom: 20px;
        }
        .edit-container{
            display: grid;
            grid-template-columns: repeat(5, 1fr);
            justify-items: center;

            &.edit-container{
                .edit-column{
                    .edit-category{
                        border: 1px solid var(--accent-secondary50);
                        margin-bottom: 20px;
                        width: fit-content;
                        border-radius: 10px;
                        display: flex;
                        justify-content: center;

                        .category-name{
                            padding: 10px;
                        }

                        .delete-category{
                            line-height: unset;
                            padding: 0 10px;
                        }
                    }
                    .edit-row{
                        display: flex;
                        flex-direction: column;
                        gap: 10px;
                    }
                    .edit-row-item{
                        display: flex;
                        justify-content: center;

                        .edit-question, .add-question{
                            background: var(--secondary20);
                        }

                        .edit-question{
                            width: fit-content;
                            border-radius: 10px;
                            display: flex;
                            justify-content: center;

                            .question-points{
                                padding: 10px;
                            }

                            .delete-question{
                                line-height: unset;
                                padding: 0 10px;
                            }
                        }

                        .add-question{
                            width: 40px;
                            height: 40px;
                            line-height: 0;
                        }
                    }
                }

                .add-column{
                    .add-category{
                        border: 1px solid var(--accent-secondary50);
                    }
                }
            }
        }

        .play-container{
            display: grid;
            grid-template-columns: repeat(5, 1fr);
            justify-items: center;
            .column{

                .category-name{
                    border: 1px solid var(--accent-secondary50);
                    margin-bottom: 20px;
                    width: fit-content;
                    border-radius: 10px;
                    display: flex;
                    justify-content: center;
                    padding: 10px;
                }
                .row{
                    display: flex;
                    flex-direction: column;
                    gap: 10px;

                    .row-item{
                        display: flex;
                        justify-content: center;
                        .question-points{
                            width: fit-content;
                            border-radius: 10px;
                            display: flex;
                            justify-content: center;
                            background: var(--secondary20);
                        }
                    }
                }
            }
        }
    }

    .question-modal{
        .question-points-container{
            margin-bottom: 20px;
        }
        .question-list{
            display: flex;
            flex-direction: column;
            gap: 10px;
            .question-list-item{
                display: flex;
                padding: 10px 20px;
                justify-content: space-between;
                gap: 20px;
                background: var(--secondary20);
                border-radius: 20px;

                &:hover{
                    background: var(--secondary60);
                    cursor: pointer;
                }

                .clue-count-tab{
                    min-width: fit-content;
                }
            }
        }
    }
</style>
