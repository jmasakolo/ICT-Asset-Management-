import axios from 'axios';
import { Preferences } from '@capacitor/preferences';

export const TOKEN_STORAGE_KEY = 'auth_token';

export const apiClient = axios.create({
  baseURL: import.meta.env.VITE_API_BASE_URL ?? 'https://assets.techwesz.com/api',
  headers: { Accept: 'application/json' },
});

apiClient.interceptors.request.use(async (config) => {
  const { value: token } = await Preferences.get({ key: TOKEN_STORAGE_KEY });
  if (token) {
    config.headers.Authorization = `Bearer ${token}`;
  }
  return config;
});

// Set by AuthContext on mount. Lets this plain axios interceptor (outside
// the React tree) trigger the same logout+redirect the UI uses, so a token
// that expires mid-session (24h Sanctum expiry) doesn't require every
// screen to individually check for 401.
let onUnauthorized: (() => void) | null = null;

export function setUnauthorizedHandler(fn: (() => void) | null): void {
  onUnauthorized = fn;
}

apiClient.interceptors.response.use(
  (response) => response,
  async (error) => {
    if (error.response?.status === 401) {
      await Preferences.remove({ key: TOKEN_STORAGE_KEY });
      onUnauthorized?.();
    }
    return Promise.reject(error);
  },
);
