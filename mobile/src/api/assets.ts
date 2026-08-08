import { apiClient } from './client';
import type { Asset, AssetInput, AssetListResponse, AssetStatus } from '../types';

export interface ListAssetsParams {
  status?: AssetStatus;
  page?: number;
  per_page?: number;
}

// Single-resource endpoints wrap the resource in {"data": {...}} (Laravel's
// default JsonResource envelope) — the same wrapper the list endpoints use
// around their `data` array, just around a single object here.
export function listAssets(params: ListAssetsParams = {}): Promise<AssetListResponse> {
  return apiClient.get<AssetListResponse>('/assets', { params }).then((res) => res.data);
}

export function getAsset(id: number): Promise<Asset> {
  return apiClient.get<{ data: Asset }>(`/assets/${id}`).then((res) => res.data.data);
}

export function createAsset(input: AssetInput): Promise<Asset> {
  return apiClient.post<{ data: Asset }>('/assets', input).then((res) => res.data.data);
}

export function updateAsset(id: number, input: Partial<AssetInput>): Promise<Asset> {
  return apiClient.put<{ data: Asset }>(`/assets/${id}`, input).then((res) => res.data.data);
}
