import { useToast } from 'vue3-toastify';

export const useCustomToast = () => {
  const toast = useToast();
  const showSuccessToast = (message: string) => toast.success(message);
  const showErrorToast = (message: string) => toast.error(message);

  return {
    showSuccessToast,
    showErrorToast,
  };
};
