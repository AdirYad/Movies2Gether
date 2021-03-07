import Vue from "vue";
import Vuex from "vuex";
import { axiosInstance } from "./plugins/axios";

Vue.use(Vuex);

export default new Vuex.Store({
  state: {
    messages: [],
    name: localStorage.getItem("name") || null,
    avatar: localStorage.getItem("avatar") || '/storage/assets/user.png',
  },

  mutations: {
        setMessages(state, messages) {
          state.messages = messages;
        },

        addMessage(state, message) {
          state.messages.push(message);
        },

        setName(state, name) {
          state.name = name;
          localStorage.setItem("name", name);
        },

        setAvatar(state, avatar) {
          state.avatar = avatar;
          localStorage.setItem("avatar", avatar);
        },

        removeName(state) {
          state.name = null;
          localStorage.removeItem("name");
        },

        removeAvatar(state) {
          state.avatar = null;
          localStorage.removeItem("avatar");
        },
  },

  actions: {
      getMessages({ commit }) {
          return axiosInstance.get('/messages').then((response) => {
              commit('setMessages', response.data);
          });
      },

      storeMessage({ state, commit }, message) {
          const payload = {
              message,
              name: state.name,
              avatar: state.avatar,
          }

          return axiosInstance.post('/messages', payload);
      },

      storeCredentials({ commit }, payload) {
          if (payload.name) {
              commit('setName', payload.name);
          } else {
              commit('removeName');
          }

          commit('setAvatar', payload.avatar);
      },

      getAvatars() {
          return axiosInstance.get('/avatars');
      },

      getTimeline() {
          return axiosInstance.get('/timelines');
      },

      storeTimeline({ state }, payload) {
          payload['name'] = state.name;
          payload['avatar'] = state.avatar;

          return axiosInstance.post('/timelines', payload);
      },
  },
});
