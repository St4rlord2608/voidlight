/* example JSON for question and category:
{
  "nextCategoryId": 3,
  "nextQuestionId": 5,
  "categories": [
    {
      "id": 1,
      "name": "History"
    },
    {
      "id": 2,
      "name": "Science"
    }
  ],
  "questions": [
    {
      "id": 1,
      "question": "Who was the first president of the USA?",
      "answer": "George Washington",
      "categoryIds": [1],
      "lobbyTypes": ["buzzer", "jeopardy"],
      "clues": [
        { "value": "Served two terms", "order": 1 },
        { "value": "Picture on the dollar bill", "order": 2 }
      ]
    },
    {
      "id": 2,
      "question": "What is the chemical symbol for water?",
      "answer": "H2O",
      "categoryIds": [2],
      "lobbyTypes": ["quiz_poker"],
      "clues": [
        { "value": "Made of two elements", "order": 1 }
      ]
    },
    {
      "id": 3,
      "question": "What year did World War II end?",
      "answer": "1945",
      "categoryIds": [1, 2],
      "lobbyTypes": ["buzzer"],
      "clues": [
        { "value": "Followed the surrender of Japan", "order": 1 },
        { "value": "Victory in Europe Day was earlier", "order": 2 }
      ]
    },
    {
      "id": 4,
      "question": "What is the name of the bomb in TickTackBlow?",
      "answer": "The Bomb",
      "categoryIds": [],
      "lobbyTypes": ["tick_tack_blow"],
      "clues": [
        { "value": "Don't let the timer run out!", "order": 1 }
      ]
    }
  ]
}
 */

import { Question, QuestionsData } from '@/types/Question';
import { Category } from '@/types/Category';
import { SubLobby } from '@/types/subLobby';

const questionsDataKey = 'questionsData'

export function loadQuestionsData(): QuestionsData{
    const storedData = localStorage.getItem(questionsDataKey);
    if(storedData){
        try{
            const parsedData = JSON.parse(storedData) as QuestionsData;

            if(parsedData && parsedData.categories && parsedData.questions && parsedData.nextCategoryId && parsedData.nextQuestionId){
                return parsedData;
            }else{
                console.warn('stored questions data corrupt, initializing defaults.');
                return initializeQuestionsData();
            }
        }catch(error){
            console.error('Error parsing questions data from localstorage:', error);
            localStorage.removeItem(questionsDataKey);
        }
    }else{
        return initializeQuestionsData();
    }
}

export function initializeQuestionsData(): QuestionsData{
    return {
        nextCategoryId: 1,
        nextQuestionId: 1,
        categories: [],
        questions: [],
    };
}

export function addCategory(questionsData: QuestionsData, name: string, question: Question = null): QuestionsData{
    if(!name) return questionsData;

    if(questionsData.categories.some(cat => cat.name.toLowerCase() === name.toLowerCase())){
        return questionsData;
    }

    const newCat: Category = {
        id: questionsData.nextCategoryId,
        name: name
    };
    questionsData.categories.push(newCat);
    questionsData.nextCategoryId++;
    if(question != null){
        const index = questionsData.questions.findIndex(quest => quest.id == question.id);
        if(index != -1){
            questionsData.questions[index].categoryIds.push(newCat.id);
        }
    }
    return questionsData;
}

export function getCategory(questionsData: QuestionsData, name: string): Category{
    const index = questionsData.categories.findIndex(cat => cat.name == name);
    if(index != -1){
        return questionsData.categories[index];
    }else{
        return {id: -1, name: ''};
    }
}

export function changeCategory(questionsData: QuestionsData, category: Category): QuestionsData{
    const index = questionsData.categories.findIndex(cat => cat.id == category.id);
    if(index != -1){
        questionsData.categories[index] = category;
    }
    return questionsData;
}

export function deleteCategory(questionsData: QuestionsData, category: Category): QuestionsData{
    questionsData.categories = questionsData.categories.filter(cat => cat.id != category.id);

    questionsData.questions.forEach(question => {
        question.categoryIds = question.categoryIds.filter(id => id !== category.id);
    })

    return questionsData;
}

export function categoryExists(questionsData: QuestionsData, category: Category): QuestionsData{
    const index = questionsData.categories.findIndex(cat => cat.name == category.name);
    return index != -1;
}

export function getCategoriesForQuestion(questionsData: QuestionsData, question: Question): Category[]{
    if(!question.categoryIds || !questionsData.categories) return [];
    return questionsData.categories.filter(category => question.categoryIds.includes(category.id));
}

export function addQuestion(questionsData: QuestionsData, newQuestion: Question): QuestionsData{
    questionsData.questions.push(newQuestion);
    questionsData.nextQuestionId++;
    return questionsData;
}

export function changeQuestion(questionsData: QuestionsData, changedQuestion: Question): QuestionsData{
    const index = questionsData.questions.findIndex(question => question.id == changedQuestion.id);
    if(index != -1) {
        questionsData.questions[index] = changedQuestion;
    }
    return questionsData
}

export function deleteQuestion(questionsData: QuestionsData, question: Question): QuestionsData{
    questionsData.questions = questionsData.questions.filter(quest => quest.id != question.id);
    return questionsData;
}

export function saveQuestionsData(newData: QuestionsData){
    localStorage.setItem(questionsDataKey, JSON.stringify(newData));
}

export function questionContainsLobbyType(question: Question, subLobby: SubLobby){
    return question.lobbyTypes.some(lobbyType => lobbyType == subLobby.lobby_type);
}
