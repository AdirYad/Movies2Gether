import axios from "axios";

const axiosInstance = axios.create({
  baseURL: "/api",
});

axiosInstance.interceptors.request.use(
  (config) => {
    config.headers = config.headers || {};

    return config;
  },
  (error) => {
    return Promise.reject(error);
  }
);

const axiosPlugin = {
  install(Vue) {
    Vue.prototype.$axios = axiosInstance;
  },
};

export default axiosPlugin;

export { axiosInstance };
