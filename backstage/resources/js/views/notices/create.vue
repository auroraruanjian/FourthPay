<template>
    <article-form :is-edit="false" />
</template>

<script>
    import ArticleForm from './form'

    export default {
        name: "notices_create",
        components: { ArticleForm }
    }
</script>

<style scoped>

</style>