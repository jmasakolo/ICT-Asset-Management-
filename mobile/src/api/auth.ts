import { apiClient } from './client';
import type { User } from '../types';

export interface LoginResponse {
  token: string;
  user: User;
}

export function login(email: string, password: string): Promise<LoginResponse> {
  return apiClient.post<LoginResponse>('/login', { email, password }).then((res) => res.data);
}

export function logout(): Promise<void> {
  return apiClient.post('/logout').then(() => undefined);
}
