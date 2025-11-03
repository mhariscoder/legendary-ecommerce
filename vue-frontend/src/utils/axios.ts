import axios from 'axios';
import { toast } from 'vue3-toastify';

const axiosClient = axios.create({
  baseURL: import.meta.env.VITE_API_BASE_URL || 'http://127.0.0.1:8001/api',
  timeout: 10000,
});

axiosClient.interceptors.request.use(config => {
  const token = localStorage.getItem('token');
  if (token) config.headers.Authorization = `Bearer ${token}`;
  return config;
});

axiosClient.interceptors.response.use(
  response => {
    toast.success('Request was successful!');
    return response;
  },
  error => {
    const { status, message, errors } = error.response?.data || {};

    if (status === false && message === 'Validation error' && errors) {
      const errorMessages = Object.values(errors).flat();
      const errorMessage = errorMessages.join(' ');
      toast.error(errorMessage);
    } else {
      const defaultMessage = message || 'Something went wrong';
      toast.error(defaultMessage);
    }

    return Promise.reject(error);
  }
);

export default axiosClient;
