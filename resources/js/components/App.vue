<template>
    <div>
        <div class="tw-flex">
            <Chat class="chat tw-w-72 tw-h-screen tw-flex tw-flex-col tw-bg-gray-800 tw-z-50" />

            <Video class="tw-flex-1" />
        </div>
    </div>
</template>

<script>
import Chat from './Chat';
import Video from './Video';

export default {
    name: 'App',
    components: {
        Chat,
        Video,
    }
}
</script>

<style>
.chat {
    background: linear-gradient(0deg, rgba(31, 41, 55, 0.85), rgba(31, 41, 55, 0.95)), url(/storage/assets/gray-hearts.gif) 50%;
}
</style>
