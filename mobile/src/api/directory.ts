import { apiClient } from './client';
import type { Department, Location, User } from '../types';

export function listUsers(): Promise<User[]> {
  return apiClient.get<{ data: User[] }>('/users').then((res) => res.data.data);
}

export function listDepartments(): Promise<Department[]> {
  return apiClient.get<{ data: Department[] }>('/departments').then((res) => res.data.data);
}

export function listLocations(): Promise<Location[]> {
  return apiClient.get<{ data: Location[] }>('/locations').then((res) => res.data.data);
}
