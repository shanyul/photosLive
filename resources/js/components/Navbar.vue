<template>
    <nav class="navbar">
        <RouterLink class="navbar__brand" to="/">
            Photo Live
        </RouterLink>
        <div class="navbar__menu">
            <div v-if="isLogin" class="navbar__item">
                <button class="button" @click="showForm = ! showForm">
                    <i class="icon ion-md-add"></i>
                    发布照片
                </button>
            </div>
            <span v-if="isLogin" class="navbar__item">{{ username }}</span>
            <div v-else class="navbar__item">
                <RouterLink class="button button--link" to="/login">
                    登录 / 注册
                </RouterLink>
            </div>
        </div>
        <PhotoForm v-model="showForm" />
    </nav>
</template>

<script>
    import PhotoForm from './PhotoForm.vue'

    export default {
        components: {
            PhotoForm
        },
        data () {
            return {
                showForm: false
            }
        },
        computed: {
            isLogin () {
                return this.$store.getters['auth/check']
            },
            username () {
                return this.$store.getters['auth/username']
            }
        }
    }
</script>
