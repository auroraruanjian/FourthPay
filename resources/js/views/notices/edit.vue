<template>
    <article-form :is-edit="true" />
</template>

<script>
    import ArticleForm from './form'

    export default {
        name: "notices_edit",
        components: { ArticleForm }
    }
</script>

<style scoped>

</style>