<template>
    <button @click="handleClick">
        Increment Value (Current: {{ initialValue }})
    </button>
</template>

<script>
    export default{
        name: 'CounterButtonTest',

        props: {
            initialValue: {
                type: Number,
                required: true
            }
        },

        methods: {
            handleClick(){
                this.$emit('increment-requested');
                console.log('Button clicked');
            }
        },

        mounted(){
            console.log('button mounted');
        }
    }
</script>
