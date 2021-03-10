<template>
    <div class="c-video tw-bg-black">
        <div v-show="hasEnded" class="tw-h-screen tw-flex tw-justify-center tw-items-center">
            <img src="/storage/assets/posters/Hamasor 2.jpg">
        </div>
        <video ref="video" class="tw-h-screen">
            <source src="/storage/assets/movies/Hamasor 2.mp4">
            Your browser does not support video tag
        </video>
        <div class="controls" :class="{ 'paused' : ! isPlaying }">
            <div ref="progressBarContainer" class="progress-bar-container tw-relative tw-duration-300 tw-w-full tw-bg-gray-800 tw-cursor-pointer" style="direction: ltr">
                <div v-if="isHovering.place" class="progress-hover" :style="`left: ${isHovering.place}px`" v-tooltip="isHovering.timeline">
                    <img src="/storage/assets/heart.png" alt="heart">
                </div>
                <div class="progress-bar tw-duration-300" :style="`width: ${progressBarWidth}%`" />
            </div>
            <div v-if="video" class="tw-w-full tw-h-12 tw-flex tw-items-center buttons tw-px-4">
                <div class="tw-flex-1">
                    <button @click="toggleFullscreen()" class="btn tw-h-12 tw-duration-300">
                        <fa-icon v-if="! isFullscreen" icon="expand" class="tw-text-white" />
                        <fa-icon v-else icon="compress" class="tw-text-white" />
                    </button>
                </div>
                <div class="tw-flex">
                    <div v-if="video && video.currentTime && video.duration" class="tw-flex tw-items-center tw-text-white tw-ml-2">
                        <span>
                            {{ Math.floor((video.duration / (60*60)) % 60) }}:{{ Math.floor((video.duration / 60) % 60) }}:{{ Math.floor((video.duration) % 60) }}
                        </span>
                        /
                        <span>
                            {{ Math.floor((video.currentTime / (60*60)) % 60) }}:{{ Math.floor((video.currentTime / 60) % 60) }}:{{ Math.floor((video.currentTime) % 60) }}
                        </span>
                    </div>
                    <div v-if="video.volume >= 0" @mouseover="showVolumeSlider = true" @mouseleave="showVolumeSlider = false" class="tw-h-12 tw-flex tw-items-center tw-ml-2" style="padding: 0 10px">
                        <transition name="slide-fade">
                            <div v-if="showVolumeSlider" class="tw-w-10 tw-ml-2">
                                <vue-slider v-model="volume" class="tw-cursor-pointer" />
                            </div>
                        </transition>

                        <div class="tw-w-4 tw-duration-300">
                            <fa-icon v-if="video.volume > 0.3" icon="volume-up" class="tw-text-white" />
                            <fa-icon v-else-if="video.volume > 0" icon="volume-down" class="tw-text-white" />
                            <fa-icon v-else icon="volume-mute" class="tw-text-white" />
                        </div>
                    </div>
                    <button @click="togglePlayPause()" class="btn tw-h-12 tw-duration-300">
                        <fa-icon v-if="! isPlaying || hasEnded" icon="play" class="tw-text-white" />
                        <fa-icon v-else icon="pause" class="tw-text-white" />
                    </button>
                </div>
            </div>
        </div>
    </div>
</template>

<script>
import VueSlider from 'vue-slider-component'
import 'vue-slider-component/theme/antd.css'
import Pusher from "pusher-js/with-encryption";
const debounce = require('debounce');

export default {
    name: 'Video',

    components: {
        VueSlider
    },

    data() {
        return {
            video: null,
            progressBarWidth: 0,
            isPlaying: false,
            isFullscreen: false,
            hasEnded: true,
            isHovering: {
                place: null,
                timeline: '',
            },
            autoTimelinesInterval: null,
            showVolumeSlider: false,
            volume: 100,
        }
    },

    mounted() {
        const _vm = this;
        this.video = this.$refs.video;

        this.video.addEventListener('timeupdate', () => {
            this.updateProgressBar();

            if(this.video.ended) {
                this.isPlaying = false;
                this.hasEnded = true;

                this.video.pause();
                this.video.currentTime = 0;

                this.storeTimeline();
                this.clearAutoTimelinesInterval();
            }
        });

        this.$refs.progressBarContainer.addEventListener('mousemove', function (e) {
            const bcr = this.getBoundingClientRect();
            const place = (e.clientX - bcr.left) / bcr.width;
            const timeline = place * _vm.video.duration;

            _vm.isHovering = {
                place: e.clientX,
                timeline: `${Math.floor((timeline / (60*60)) % 60)}:${Math.floor((timeline / 60) % 60)}:${Math.floor(timeline % 60)}`
            }
        });

        this.$refs.progressBarContainer.addEventListener('mouseleave', () => {
            _vm.isHovering = {
                place: null,
                timeline: '',
            }
        });

        this.$refs.progressBarContainer.addEventListener('click', function (e) {
            const bcr = this.getBoundingClientRect();
            const place = (e.clientX - bcr.left) / bcr.width;

            _vm.video.currentTime = place * _vm.video.duration;
            _vm.storeTimeline('seeked');

            _vm.hasEnded = false;
        });

        this.$store.dispatch('getTimeline').then((response) => {
            this.video.currentTime = response.data.seconds || 1 / 100000;

            this.hasEnded = this.video.currentTime <= 1 / 100000;
        });

        const pusher = new Pusher(process.env.MIX_PUSHER_APP_KEY, {
            cluster: process.env.MIX_PUSHER_APP_CLUSTER,
            encrypted: true,
            authEndpoint: process.env.MIX_APP_URL + '/broadcasting/auth',
        });

        pusher.subscribe('timelines')
            .bind('timeline.seeked', ({ timeline }) => {
                this.video.currentTime = timeline.seconds;
                this.hasEnded = false;

                this.updateProgressBar();
            })
            .bind('timeline.played', ({ timeline }) => {
                this.isPlaying = true;
                this.hasEnded = false;
                this.video.currentTime = timeline.seconds;
                this.video.play();

                this.updateProgressBar();
                this.createAutoTimelinesInterval();
            })
            .bind('timeline.paused', ({ timeline }) => {
                this.isPlaying = false;
                this.hasEnded = false;
                this.video.currentTime = timeline.seconds;
                this.video.pause();

                this.updateProgressBar();
                this.clearAutoTimelinesInterval();
            });
    },

    methods: {
        toggleFullscreen() {
            const elem = document.documentElement;

            if (! document.fullscreenElement && ! document.mozFullScreenElement && ! document.webkitFullscreenElement && ! document.msFullscreenElement) {
                if (elem.requestFullscreen) {
                    elem.requestFullscreen();
                } else if (elem.msRequestFullscreen) {
                    elem.msRequestFullscreen();
                } else if (elem.mozRequestFullScreen) {
                    elem.mozRequestFullScreen();
                } else if (elem.webkitRequestFullscreen) {
                    elem.webkitRequestFullscreen(Element.ALLOW_KEYBOARD_INPUT);
                }

                this.isFullscreen = true;

                return;
            }

            if (document.exitFullscreen) {
                document.exitFullscreen();
            } else if (document.msExitFullscreen) {
                document.msExitFullscreen();
            } else if (document.mozCancelFullScreen) {
                document.mozCancelFullScreen();
            } else if (document.webkitExitFullscreen) {
                document.webkitExitFullscreen();
            }

            this.isFullscreen = false;
        },

        storeTimeline(value = '') {
            return this.$store.dispatch('storeTimeline', {
                seconds: this.video.currentTime,
                seeked: value === 'seeked',
                is_paused: value === 'is_paused',
                is_played: value === 'is_played',
            });
        },

        togglePlayPause() {
            if(this.video.paused) {
                this.video.play();
                this.storeTimeline('is_played');
            } else {
                this.video.pause();
                this.storeTimeline('is_paused');
            }

            this.hasEnded = false;
            this.isPlaying = ! this.isPlaying;
        },

        seek: debounce(function () {
            this.storeTimeline('seeked');
        }, 300),

        updateProgressBar() {
            this.progressBarWidth = (this.video.currentTime / this.video.duration) * 100;
        },

        createAutoTimelinesInterval() {
            this.autoTimelinesInterval = setInterval(() => {
                this.storeTimeline();
            }, 30000)
        },

        clearAutoTimelinesInterval() {
            clearInterval(this.autoTimelinesInterval);
        }
    },

    watch: {
        volume() {
            this.video.volume = this.volume / 100
        }
    },
}
</script>

<style scoped>
.c-video {
    width: calc(100vw - 288px);
    height: 100vh;
    position: relative;
    overflow: hidden;
}

.c-video:hover .controls, .paused {
    transform: translateY(0) !important;
}

.controls {
    display: flex;
    position: absolute;
    bottom: 0;
    width: 100%;
    flex-wrap: wrap;
    background-color: rgba(0, 0, 0, 0.7);
    transform: translateY(100%) translateY(-2px);
    transition: all .3s;
}

.progress-bar-container {
    height: 8px;
}

.progress-bar {
    height: 8px;
    background-color: var(--base-bg-pink);
}

.progress-hover {
    position: absolute;
    height: 24px;
    width: 24px;
    top: 50%;
    bottom: 50%;
    border-radius: 50%;
    transform: translate(-50%, -50%);
}

.progress-bar-container:hover, .progress-bar-container:hover > .progress-bar {
    height: 12px;
}

.buttons .btn {
    cursor: pointer;
    outline: none;
    padding: 0 10px;
}

button {
    outline: none;
}

.slide-fade-leave-active,
.slide-fade-enter-active {
    transition: 300ms;
}

.slide-fade-enter, .slide-fade-leave-to {
    transform: translate(-12px, 0);
}
</style>

<style>
.vue-slider-process {
    background-color: var(--base-bg-pink) !important;
}

.vue-slider-dot-handle {
    border: 2px solid var(--base-bg-pink) !important;
}
</style>
