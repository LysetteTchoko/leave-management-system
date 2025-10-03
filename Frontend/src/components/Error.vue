<template>
    <div class="text-center m-2 text-base text-red-700 p-2 bg-red-200" v-if='error'>
        {{error}}
    </div>  
</template>


<script>
 export default{
    name: 'AppError',
    props: ['error']
 }
</script>