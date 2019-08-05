<template>
    <div class="photo">
        <figure class="photo__wrapper">
            <img
                class="photo__image"
                :class="imageClass"
                :src="item.url"
                :alt="`Photo by ${item.owner.name}`"
                @load="setAspectRatio"
                ref="image"
            >
        </figure>
        <RouterLink
            class="photo__overlay"
            :to="`/photos/${item.id}`"
            :title="`View the photo by ${item.owner.name}`"
        >
            <div class="photo__controls">
                <button
                    class="photo__action photo__action--like"
                    :class="{ 'photo__action--liked': item.liked_by_user }"
                    title="Like photo"
                    @click.prevent="like"
                >
                    <i class="icon ion-md-heart"></i>{{ item.likes_count }}
                </button>
                <a
                    class="photo__action"
                    title="Download photo"
                    @click.stop
                    :href="`/photos/${item.id}/download`"
                >
                    <i class="icon ion-md-arrow-round-down"></i>
                </a>
                <button
                    class="photo__action"
                    title="Delete photo"
                    @click.prevent="delItem"
                >
                    <i class="icon ion-md-trash"></i>
                </button>
            </div>
            <div class="photo__username">
                {{ item.owner.name }}
            </div>
        </RouterLink>
    </div>
</template>

<script>
    export default {
        props: {
            item: {
                type: Object,
                required: true
            }
        },
        data () {
            return {
                landscape: false,
                portrait: false
            }
        },
        computed: {
            imageClass () {
                return {
                    'photo__image--landscape': this.landscape,
                    'photo__image--portrait': this.portrait
                }
            }
        },
        methods: {
            setAspectRatio () {
                if (! this.$refs.image) {
                    return false
                }
                const height = this.$refs.image.clientHeight;
                const width = this.$refs.image.clientWidth;
                this.landscape = height / width <= 0.75;
                this.portrait = ! this.landscape
            },
            like () {
                this.$emit('like', {
                    id: this.item.id,
                    liked: this.item.liked_by_user
                })
            },
            delItem () {
                if(confirm('确定删除图片？')){
                    this.$emit('delItem', {
                        id: this.item.id,
                    })
                }
            }
        },
        watch: {
            $route () {
                this.landscape = false;
                this.portrait = false
            }
        }
    }
</script>
