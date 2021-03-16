<template>
    <div @click="! isEditing ? $refs.message.focus() : ''">
        <div class="tw-flex tw-justify-between tw-px-8 tw-py-4 tw-border-b tw-border-base-white tw-mb-4">
            <button @click="openEditor" class="focus:tw-outline-none">
                <img :src="$store.state.avatar" :alt="$store.state.avatar" class="tw-w-12 tw-h-12 scale">
            </button>
            <div class="tw-flex tw-items-center tw-text-base-white tw-font-bold tw-text-lg" style="height: inherit">
                Movies2Gether
            </div>
        </div>

        <template v-if="! isEditing">
            <div class="tw-flex-1 tw-flex tw-flex-col-reverse tw-overflow-y-scroll tw-overflow-x-hidden tw-mb-4 tw-px-8">
                <div>
                    <div v-for="(message, index) in $store.state.messages" :key="index" class="tw-flex tw-items-center tw-justify-around tw-py-1">
                        <div class="tw-w-8 tw-h-8 tw-ml-2">
                            <img :src="message.formatted_avatar" alt="image" class="tw-w-8 tw-h-8">
                        </div>

                        <div class="tw-flex tw-flex-col tw-w-4/5 tw-max-w-4/5">
                            <div v-if="message.name" class="tw-w-full tw-font-bold tw-text-base-white tw-break-words" style="font-size: 14px">
                                {{ message.name }}
                            </div>

                            <div class="tw-w-full tw-font-medium  tw-break-words" style="font-size: 14px"
                                 :class="message.timeline_id ? 'tw-text-gray-400' : 'tw-text-base-white'"
                            >
                                {{ message.message }}
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <form @submit.prevent="store" class="tw-border-t tw-border-base-white tw-px-8 tw-py-4">
                <label>
                    <input v-model="message"
                           ref="message"
                           type="text"
                           placeholder="כתב/י הודעה..."
                           autocomplete="off"
                           class="tw-bg-transparent tw-w-full focus:tw-outline-none tw-placeholder-gray-300 tw-text-base-white"
                           :disabled="isStoring"
                    >
                </label>
            </form>
        </template>

        <form v-else-if="! isPickingAvatar" @submit.prevent="edit" class="tw-flex tw-justify-center tw-items-center tw-flex-col tw-px-8">
            <button @click="isPickingAvatar = true" type="button" class="focus:tw-outline-none">
                <img :src="editPayload.avatar" :alt="editPayload.avatar" class="tw-w-16 tw-h-16 scale">
            </button>

            <label class="tw-text-pink-300 tw-mt-4">
                שם משתמש/ת
                <input v-model="editPayload.name"
                       type="text"
                       :placeholder="$store.state.name || 'שם משתמש/ת'"
                       autocomplete="off"
                       class="tw-w-full tw-bg-gray-600 focus:tw-outline-none tw-placeholder-gray-300 tw-text-base-white tw-rounded-md tw-p-2 tw-mt-2 tw-duration-300 tw-border-2 tw-border-transparent hover:tw-border-pink-300 focus:tw-border-pink-300"
                >
            </label>

            <button type="submit" class="tw-w-full tw-bg-pink-300 tw-text-base-white tw-font-medium tw-rounded-md tw-p-2 tw-mt-4 focus:tw-outline-none">
                שמר/י
            </button>
        </form>

        <div v-else class="tw-flex tw-justify-center tw-items-center tw-flex-wrap tw-px-8">
            <button v-for="(avatar, index) in avatars" :key="index" @click="pickAvatar(avatar)" type="button" class="focus:tw-outline-none tw-m-1">
                <img :src="avatar" :alt="avatar" class="tw-w-16 tw-h-16 scale">
            </button>
        </div>
    </div>
</template>

<script>
import Pusher from 'pusher-js/with-encryption';

export default {
    name: 'Chat',

    data() {
        return {
            message: '',
            isStoring: false,

            isEditing: false,
            isPickingAvatar: false,
            editPayload: {
                name: this.$store.state.name,
                avatar: this.$store.state.avatar,
            },
            avatars: [],
        }
    },

    created() {
        this.$store.dispatch('getMessages');

        this.$store.dispatch('getAvatars').then((response) => {
            this.avatars = response.data;
        });

        const pusher = new Pusher(process.env.MIX_PUSHER_APP_KEY, {
            cluster: process.env.MIX_PUSHER_APP_CLUSTER,
            encrypted: true,
            authEndpoint: process.env.MIX_APP_URL + '/broadcasting/auth',
        });

        pusher.subscribe('messages')
            .bind('message.created', ({ message }) => {
                this.$store.commit('addMessage', message);
            });
    },

    methods: {
        store() {
            if (! this.message || this.isStoring) {
                return;
            }

            this.$store.dispatch('storeMessage', this.message).then(() => {
                this.message = '';
                this.isStoring = false;

                this.$refs.message.focus();
            }).catch((err) => {
                console.log(err.response)
                this.isStoring = false;
                this.$refs.message.focus();
            });
        },

        openEditor() {
            this.isEditing = ! this.isEditing;
            this.isPickingAvatar = false;

            this.editPayload = {
                name: this.$store.state.name,
                avatar: this.$store.state.avatar,
            };
        },

        pickAvatar(avatar) {
            this.editPayload.avatar = avatar;
            this.isPickingAvatar = false;
        },

        edit() {
            if (! this.isEditing) {
                return;
            }

            this.$store.dispatch('storeCredentials', this.editPayload).then(() => {
                this.isEditing = false;
            });
        },
    },
}
</script>
