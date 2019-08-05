<template>
    <div class="pagination">
        <RouterLink
            v-if="! isFirstPage"
            :to="`/?page=${currentPage - 1}`"
            class="button"
        >&laquo; 前一页</RouterLink>
        <RouterLink
            v-if="! isLastPage"
            :to="`/?page=${currentPage + 1}`"
            class="button"
        >后一页 &raquo;</RouterLink>
    </div>
</template>

<script>
    export default {
        props: {
            currentPage: {
                type: Number,
                required: true
            },
            lastPage: {
                type: Number,
                required: true
            }
        },
        computed: {
            isFirstPage () {
                return this.currentPage === 1
            },
            isLastPage () {
                return this.currentPage === this.lastPage
            }
        }
    }
</script>
