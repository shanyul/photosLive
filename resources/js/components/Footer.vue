<template>
    <footer class="footer">
        <button v-if="isLogin" class="button button--link" @click="logout">
            退出
        </button>
        <RouterLink v-else class="button button--link" to="/login">
            登录 / 注册
        </RouterLink>
    </footer>
</template>

<script>
    import { mapState, mapGetters } from 'vuex'

    export default {
        methods: {
            async logout () {
                await this.$store.dispatch('auth/logout')

                if (this.apiStatus) {
                    this.$router.push('/login')
                }
            }
        },
        computed: {
            ...mapState({
                apiStatus: state => state.auth.apiStatus
            }),
            ...mapGetters({
                isLogin: 'auth/check'
            })
        }
    }
</script>
