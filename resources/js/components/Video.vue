<template>
    <div>
        <video
            id="my-video"
            class="video-js vjs-big-play-centered"
            controls
            preload="auto"
            data-setup="{}"
        >
            Your browser does not support video tag
        </video>
    </div>
</template>

<script>
import videojs from 'video.js';
import Pusher from "pusher-js/with-encryption";
const debounce = require('debounce');

export default {
    name: 'Video',

    data() {
        return {
            movie: null,
            video: null,
            isSeeking: false,
            isEditing: false,
        }
    },

    mounted() {
        this.video = videojs('#my-video', {
            techOrder: ["html5"],
            autoplay: false,
            sources: [{
                type: "video/mp4",
                src: '/storage/assets/HarryPotter.mp4'
            }],
            poster: "https://www.classiccinemas.com/fg-proxy/?url=https%3A%2F%2Fimage.tmdb.org%2Ft%2Fp%2Foriginal%2FkT8bDEAgEYBKhRJtqM97qTw6uRW.jpg",
            fill: true,
            controlBar: {
                fullscreenToggle: false,
                pictureInPictureToggle: false,
            },
            userActions: {
                doubleClick: () => this.toggleFullscreen(),
            },
        });

        this.$store.dispatch('getTimeline').then((response) => {
            this.movie = response.data;
            this.isSeeking = true;
            this.video.ready(() => {
                this.video.currentTime(this.movie.seconds || 0);
            })
        });

        this.video.on('seeked', this.seek);

        this.video.on('play', () => this.playOrPause('play'));

        this.video.on('pause', () => this.playOrPause('pause'));

        const pusher = new Pusher(process.env.MIX_PUSHER_APP_KEY, {
            cluster: process.env.MIX_PUSHER_APP_CLUSTER,
            encrypted: true,
            authEndpoint: process.env.MIX_APP_URL + '/broadcasting/auth',
        });

        pusher.subscribe('timelines')
            .bind('timeline.seeked', ({ timeline }) => {
                this.isSeeking = true;
                this.video.currentTime(timeline.seconds);
            })
            .bind('timeline.played', ({ timeline }) => {
                this.isEditing = true;
                this.isSeeking = true;
                this.video.currentTime(timeline.seconds);
                this.video.play();
            })
            .bind('timeline.paused', ({ timeline }) => {
                this.isEditing = true;
                this.isSeeking = true;
                this.video.currentTime(timeline.seconds);
                this.video.pause();
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
            } else {
                if (document.exitFullscreen) {
                    document.exitFullscreen();
                } else if (document.msExitFullscreen) {
                    document.msExitFullscreen();
                } else if (document.mozCancelFullScreen) {
                    document.mozCancelFullScreen();
                } else if (document.webkitExitFullscreen) {
                    document.webkitExitFullscreen();
                }
            }
        },

        storeTimeline(value = '') {
            return this.$store.dispatch('storeTimeline', {
                seconds: this.video.currentTime(),
                seeked: value === 'seeked',
                is_paused: value === 'is_paused',
                is_played: value === 'is_played',
            }).then(() => {
                this.isEditing = false;
            });
        },

        playOrPause: debounce(function (value) {
            console.log(value + ' ' + this.video.readyState() + ' ' + this.isEditing)
            if (this.video.readyState() !== 4) {
                return;
            } else if (this.isEditing) {
                this.isEditing = false;
                return
            }

            this.storeTimeline(value === 'pause' ? 'is_paused' : 'is_played');
        }, 300),

        seek: debounce(function () {
            console.log('seeked ' + this.video.readyState() + ' ' + this.isSeeking)
            if (this.video.readyState() !== 4) {
                return;
            } else if (this.isSeeking) {
                this.isSeeking = false;
                return;
            }

            this.isEditing = true;

            this.storeTimeline('seeked');
        }, 300),
    },
}
</script>
