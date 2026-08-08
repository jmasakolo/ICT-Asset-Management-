import {
  IonBadge,
  IonButton,
  IonContent,
  IonFab,
  IonFabButton,
  IonHeader,
  IonIcon,
  IonInfiniteScroll,
  IonInfiniteScrollContent,
  IonItem,
  IonLabel,
  IonList,
  IonPage,
  IonRefresher,
  IonRefresherContent,
  IonSelect,
  IonSelectOption,
  IonTitle,
  IonToolbar,
  type RefresherEventDetail,
} from '@ionic/react';
import { addOutline, logOutOutline } from 'ionicons/icons';
import { useCallback, useEffect, useState } from 'react';
import { useHistory } from 'react-router-dom';
import { listAssets } from '../../api/assets';
import { useAuth } from '../../auth/AuthContext';
import { ASSET_STATUSES, type Asset, type AssetListMeta, type AssetStatus } from '../../types';

const STATUS_COLORS: Record<AssetStatus, string> = {
  active: 'success',
  maintenance: 'warning',
  repair: 'danger',
  configuration: 'medium',
  retired: 'dark',
};

export default function AssetList() {
  const history = useHistory();
  const { logout } = useAuth();
  const [assets, setAssets] = useState<Asset[]>([]);
  const [meta, setMeta] = useState<AssetListMeta | null>(null);
  const [status, setStatus] = useState<AssetStatus | 'all'>('all');
  const [page, setPage] = useState(1);
  const [loading, setLoading] = useState(true);

  const load = useCallback(
    async (targetPage: number, append: boolean) => {
      const response = await listAssets({
        status: status === 'all' ? undefined : status,
        page: targetPage,
      });
      setMeta(response.meta);
      setAssets((prev) => (append ? [...prev, ...response.data] : response.data));
    },
    [status],
  );

  useEffect(() => {
    setLoading(true);
    setPage(1);
    load(1, false).finally(() => setLoading(false));
  }, [load]);

  async function handleRefresh(event: CustomEvent<RefresherEventDetail>) {
    await load(1, false);
    setPage(1);
    event.detail.complete();
  }

  async function handleLoadMore(event: CustomEvent<void>) {
    const nextPage = page + 1;
    await load(nextPage, true);
    setPage(nextPage);
    (event.target as HTMLIonInfiniteScrollElement).complete();
  }

  const hasMore = meta ? page < meta.last_page : false;

  return (
    <IonPage>
      <IonHeader>
        <IonToolbar>
          <IonTitle>Assets</IonTitle>
          <IonButton slot="end" fill="clear" onClick={() => logout()}>
            <IonIcon slot="icon-only" icon={logOutOutline} />
          </IonButton>
        </IonToolbar>
        <IonToolbar>
          <IonSelect
            label="Status"
            interface="popover"
            value={status}
            onIonChange={(e) => setStatus((e.detail.value as AssetStatus | 'all') ?? 'all')}
          >
            <IonSelectOption value="all">All</IonSelectOption>
            {ASSET_STATUSES.map((s) => (
              <IonSelectOption key={s} value={s}>
                {s}
              </IonSelectOption>
            ))}
          </IonSelect>
        </IonToolbar>
      </IonHeader>

      <IonContent>
        <IonRefresher slot="fixed" onIonRefresh={handleRefresh}>
          <IonRefresherContent />
        </IonRefresher>

        {!loading && assets.length === 0 && (
          <p className="ion-padding ion-text-center">No assets yet.</p>
        )}

        <IonList>
          {assets.map((asset) => (
            <IonItem
              key={asset.id}
              button
              detail
              onClick={() => history.push(`/assets/${asset.id}/edit`)}
            >
              <IonLabel>
                <h2>{asset.name}</h2>
                <p>
                  {asset.category}
                  {asset.asset_tag ? ` · ${asset.asset_tag}` : ''}
                </p>
              </IonLabel>
              <IonBadge slot="end" color={STATUS_COLORS[asset.status]}>
                {asset.status}
              </IonBadge>
            </IonItem>
          ))}
        </IonList>

        <IonInfiniteScroll disabled={!hasMore} onIonInfinite={handleLoadMore}>
          <IonInfiniteScrollContent loadingText="Loading more assets..." />
        </IonInfiniteScroll>

        <IonFab vertical="bottom" horizontal="end" slot="fixed">
          <IonFabButton onClick={() => history.push('/assets/new')}>
            <IonIcon icon={addOutline} />
          </IonFabButton>
        </IonFab>
      </IonContent>
    </IonPage>
  );
}
