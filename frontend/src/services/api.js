import axios from 'axios'

const backendUrl = import.meta.env.VITE_BACKEND_URL || 'http://127.0.0.1:8000'

export const backend = axios.create({
  baseURL: backendUrl,
  timeout: 15000,
  withCredentials: true,
  withXSRFToken: true,
  headers: {
    Accept: 'application/json',
    'X-Requested-With': 'XMLHttpRequest',
  },
})

const api = axios.create({
  baseURL: import.meta.env.VITE_API_URL || `${backendUrl}/api`,
  timeout: 15000,
  withCredentials: true,
  withXSRFToken: true,
  headers: {
    Accept: 'application/json',
    'Content-Type': 'application/json',
    'X-Requested-With': 'XMLHttpRequest',
  },
})

export const ensureCsrfCookie = () => backend.get('/sanctum/csrf-cookie')
export const getValidationErrors = (error) => error?.response?.data?.errors ?? {}
export const getApiMessage = (error, fallback = 'Ocurrió un error inesperado.') => error?.response?.data?.message ?? fallback

export default api
