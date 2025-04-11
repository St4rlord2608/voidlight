<template>
    <div>
        <h2>Parent Component</h2>

        <p>Parents count: {{ count }}</p>

        <hr/>
        <CounterButtonTest :initial-value="count" @increment-requested="handleIncrement"/>
        <CounterButtonTest :initial-value="count" @increment-requested="handleIncrement"/>

    </div>
</template>

<script lang="js">
import CounterButtonTest from '@/components/tests/CounterButtonTest.vue';

export default{
    name: 'ParentContainer',

    components: {
        CounterButtonTest
    },

    data(){
        return{
            count: 0
        }
    },

    methods: {
        handleIncrement(){
            console.log('Parent received');
            this.count++;
        }
    },

    mounted(){
        console.log('ParentContainer mounted')
    }
}
</script>
