export interface User {
  id: number;
  name: string;
  email: string;
}

export interface Department {
  id: number;
  name: string;
}

export interface Location {
  id: number;
  name: string;
}

export type Priority = 'high' | 'medium' | 'low';

export interface Task {
  id: number;
  title: string;
  notes: string | null;
  due_date: string | null;
  priority: Priority;
  is_done: boolean;
  is_overdue: boolean;
  is_due_today: boolean;
  completed_at: string | null;
  created_at: string;
  updated_at: string;
}

export type TaskFilter = 'all' | 'active' | 'done';
export type TaskSort = 'due' | 'priority' | 'title' | 'created';

export interface TaskListMeta {
  filter: TaskFilter;
  sort: TaskSort;
  counts: { all: number; active: number; done: number };
  page: number;
  per_page: number;
  total: number;
  last_page: number;
}

export interface TaskListResponse {
  data: Task[];
  meta: TaskListMeta;
  links: { prev: string | null; next: string | null };
}

export interface TaskInput {
  title: string;
  notes?: string | null;
  due_date?: string | null;
  priority?: Priority;
  is_done?: boolean;
}

export type AssetStatus = 'active' | 'maintenance' | 'repair' | 'configuration' | 'retired';
export type AssetCondition = 'new' | 'old';

export interface Asset {
  id: number;
  name: string;
  category: string;
  model: string | null;
  serial_number: string | null;
  asset_tag: string | null;
  status: AssetStatus;
  condition: AssetCondition;
  value: number;
  assigned_user_id: number | null;
  department_id: number | null;
  location_id: number | null;
  received_at: string | null;
  warranty_expires_at: string | null;
  created_at: string;
  updated_at: string;
}

export interface AssetListMeta {
  page: number;
  per_page: number;
  total: number;
  last_page: number;
}

export interface AssetListResponse {
  data: Asset[];
  meta: AssetListMeta;
  links: { prev: string | null; next: string | null };
}

export interface AssetInput {
  name: string;
  category: string;
  model?: string | null;
  serial_number?: string | null;
  asset_tag?: string | null;
  condition?: AssetCondition;
  status?: AssetStatus;
  value?: number;
  assigned_user_id?: number | null;
  department_id?: number | null;
  location_id?: number | null;
  received_at?: string | null;
  warranty_expires_at?: string | null;
}

export const TASK_PRIORITIES: Priority[] = ['high', 'medium', 'low'];
export const ASSET_STATUSES: AssetStatus[] = ['active', 'maintenance', 'repair', 'configuration', 'retired'];
export const ASSET_CONDITIONS: AssetCondition[] = ['new', 'old'];
