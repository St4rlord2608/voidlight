<script setup lang="ts">
import { ref, watch } from 'vue';

    const props = defineProps({
        items: {
            type: Array as () => string[],
            default: () => []
        },
        modelValue: {
            type: String,
            required: true
        },
        placeholder: {
            type: String,
            default: 'Search...'
        }
    })

    const searchTerm = ref(props.modelValue);
    const filteredItems = ref<string[]>([]);
    const showSuggestions = ref(false);
    let blurTimeoutId: number | null = null;

    function filterItems(){
        if(!searchTerm.value){
            filteredItems.value = props.items;
            showSuggestions.value = filteredItems.value.length > 0;
        }else{
            const lowerSearch = searchTerm.value.toLowerCase();
            filteredItems.value = props.items.filter(item => item.toLowerCase().includes(lowerSearch));
            showSuggestions.value = filteredItems.value.length > 0;
        }

        emit('update:model-value', searchTerm.value)
    }

    function selectItem(item: string){
        searchTerm.value = item;
        emit('update:model-value', item);
        showSuggestions.value = false;
        filteredItems.value = [];
        emit('on-enter');
    }

    function handleBlur(){
        if(blurTimeoutId) clearTimeout(blurTimeoutId);

        blurTimeoutId = window.setTimeout(() => {
            showSuggestions.value = false;
        }, 150);
    }

    function handleEnter(){
        emit('on-enter');
    }

    watch(() => props.modelValue, (newValue) => {
        if(newValue !== searchTerm.value){
            searchTerm.value = newValue ?? '';
            if(showSuggestions.value){
                filterItems();
            }
        }
    })

    const emit = defineEmits([
        'update:model-value',
        'on-enter'
    ])

</script>

<template>
    <div class="autocomplete-container">
        <input
            :class="showSuggestions? 'with-suggestion' : ''"
            type="text"
            v-model="searchTerm"
            @input="filterItems"
            @blur="handleBlur"
            @focus="filterItems"
            @keydown.enter="handleEnter"
            :placeholder="placeholder"
            autocomplete="off"
        />
        <ul v-if="showSuggestions && filteredItems.length > 0" class="suggestions-list">
            <li v-for="item in filteredItems"
                :key="item"
                @mousedown="selectItem(item)">
            {{ item }}
            </li>
        </ul>
    </div>
</template>

<style scoped>
.autocomplete-container {
    position: relative;
    width: 100%;

    .with-suggestion{
        border-bottom-left-radius: 0;
        border-bottom-right-radius: 0;
    }
}

.suggestions-list {
    position: absolute;
    left: 0;
    right: 0;
    top: 100%;
    background-color: var(--secondary100);
    border: 1px solid var(--accent-secondary100);
    border-top: none;
    border-radius: 0 0 4px 4px;
    list-style: none;
    margin: 0;
    padding: 0;
    max-height: 150px;
    overflow-y: auto;
    z-index: 10;
    box-shadow: 0 2px 4px rgba(0,0,0,0.1);
}

.suggestions-list li {
    padding: 8px 12px;
    cursor: pointer;
}

.suggestions-list li:hover {
    background-color: var(--button-warning40);
}
</style>
