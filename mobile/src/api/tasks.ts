import { apiClient } from './client';
import type { Task, TaskFilter, TaskInput, TaskListResponse, TaskSort } from '../types';

export interface ListTasksParams {
  filter?: TaskFilter;
  sort?: TaskSort;
  page?: number;
  per_page?: number;
}

// Single-resource endpoints wrap the resource in {"data": {...}} (Laravel's
// default JsonResource envelope) — the same wrapper the list endpoints use
// around their `data` array, just around a single object here.
export function listTasks(params: ListTasksParams = {}): Promise<TaskListResponse> {
  return apiClient.get<TaskListResponse>('/tasks', { params }).then((res) => res.data);
}

export function getTask(id: number): Promise<Task> {
  return apiClient.get<{ data: Task }>(`/tasks/${id}`).then((res) => res.data.data);
}

export function createTask(input: TaskInput): Promise<Task> {
  return apiClient.post<{ data: Task }>('/tasks', input).then((res) => res.data.data);
}

export function updateTask(id: number, input: Partial<TaskInput>): Promise<Task> {
  return apiClient.put<{ data: Task }>(`/tasks/${id}`, input).then((res) => res.data.data);
}

export function deleteTask(id: number): Promise<void> {
  return apiClient.delete(`/tasks/${id}`).then(() => undefined);
}

export function toggleTask(id: number): Promise<Task> {
  return apiClient.patch<{ data: Task }>(`/tasks/${id}/toggle`).then((res) => res.data.data);
}
