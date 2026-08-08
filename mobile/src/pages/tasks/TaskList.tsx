import {
  IonButton,
  IonCheckbox,
  IonContent,
  IonFab,
  IonFabButton,
  IonHeader,
  IonIcon,
  IonInfiniteScroll,
  IonInfiniteScrollContent,
  IonItem,
  IonItemOption,
  IonItemOptions,
  IonItemSliding,
  IonLabel,
  IonList,
  IonPage,
  IonRefresher,
  IonRefresherContent,
  IonSegment,
  IonSegmentButton,
  IonSelect,
  IonSelectOption,
  IonTitle,
  IonToolbar,
  type RefresherEventDetail,
} from '@ionic/react';
import { addOutline, logOutOutline, trashOutline } from 'ionicons/icons';
import { useCallback, useEffect, useState } from 'react';
import { useHistory } from 'react-router-dom';
import { deleteTask, listTasks, toggleTask } from '../../api/tasks';
import { useAuth } from '../../auth/AuthContext';
import type { Task, TaskFilter, TaskListMeta, TaskSort } from '../../types';

export default function TaskList() {
  const history = useHistory();
  const { logout } = useAuth();
  const [tasks, setTasks] = useState<Task[]>([]);
  const [meta, setMeta] = useState<TaskListMeta | null>(null);
  const [filter, setFilter] = useState<TaskFilter>('all');
  const [sort, setSort] = useState<TaskSort>('due');
  const [page, setPage] = useState(1);
  const [loading, setLoading] = useState(true);

  const load = useCallback(
    async (targetPage: number, append: boolean) => {
      const response = await listTasks({ filter, sort, page: targetPage });
      setMeta(response.meta);
      setTasks((prev) => (append ? [...prev, ...response.data] : response.data));
    },
    [filter, sort],
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

  async function handleToggle(task: Task) {
    await toggleTask(task.id);
    // Reload rather than patch the local array in place — meta.counts
    // (the segment badge numbers) only comes from the list endpoint, so a
    // local patch would leave those stale until the next filter/refresh.
    await load(1, false);
    setPage(1);
  }

  async function handleDelete(task: Task) {
    await deleteTask(task.id);
    await load(1, false);
    setPage(1);
  }

  const hasMore = meta ? page < meta.last_page : false;

  return (
    <IonPage>
      <IonHeader>
        <IonToolbar>
          <IonTitle>Tasks</IonTitle>
          <IonButton slot="end" fill="clear" onClick={() => logout()}>
            <IonIcon slot="icon-only" icon={logOutOutline} />
          </IonButton>
        </IonToolbar>
        <IonToolbar>
          <IonSegment
            value={filter}
            onIonChange={(e) => setFilter((e.detail.value as TaskFilter) ?? 'all')}
          >
            <IonSegmentButton value="all">
              <IonLabel>All{meta ? ` (${meta.counts.all})` : ''}</IonLabel>
            </IonSegmentButton>
            <IonSegmentButton value="active">
              <IonLabel>Active{meta ? ` (${meta.counts.active})` : ''}</IonLabel>
            </IonSegmentButton>
            <IonSegmentButton value="done">
              <IonLabel>Done{meta ? ` (${meta.counts.done})` : ''}</IonLabel>
            </IonSegmentButton>
          </IonSegment>
        </IonToolbar>
        <IonToolbar>
          <IonSelect
            label="Sort by"
            interface="popover"
            value={sort}
            onIonChange={(e) => setSort((e.detail.value as TaskSort) ?? 'due')}
          >
            <IonSelectOption value="due">Due date</IonSelectOption>
            <IonSelectOption value="priority">Priority</IonSelectOption>
            <IonSelectOption value="title">Title</IonSelectOption>
            <IonSelectOption value="created">Created</IonSelectOption>
          </IonSelect>
        </IonToolbar>
      </IonHeader>

      <IonContent>
        <IonRefresher slot="fixed" onIonRefresh={handleRefresh}>
          <IonRefresherContent />
        </IonRefresher>

        {!loading && tasks.length === 0 && (
          <p className="ion-padding ion-text-center">No tasks yet.</p>
        )}

        <IonList>
          {tasks.map((task) => (
            <IonItemSliding key={task.id}>
              <IonItem>
                <IonCheckbox
                  slot="start"
                  checked={task.is_done}
                  onIonChange={() => handleToggle(task)}
                />
                <IonLabel
                  onClick={() => history.push(`/tasks/${task.id}/edit`)}
                  style={{ cursor: 'pointer' }}
                >
                  <h2 style={task.is_done ? { textDecoration: 'line-through' } : undefined}>
                    {task.title}
                  </h2>
                  <p>
                    {task.priority}
                    {task.due_date ? ` · due ${task.due_date}` : ''}
                    {task.is_overdue ? ' · overdue' : ''}
                    {task.is_due_today ? ' · due today' : ''}
                  </p>
                </IonLabel>
              </IonItem>
              <IonItemOptions side="end">
                <IonItemOption color="danger" onClick={() => handleDelete(task)}>
                  <IonIcon slot="icon-only" icon={trashOutline} />
                </IonItemOption>
              </IonItemOptions>
            </IonItemSliding>
          ))}
        </IonList>

        <IonInfiniteScroll disabled={!hasMore} onIonInfinite={handleLoadMore}>
          <IonInfiniteScrollContent loadingText="Loading more tasks..." />
        </IonInfiniteScroll>

        <IonFab vertical="bottom" horizontal="end" slot="fixed">
          <IonFabButton onClick={() => history.push('/tasks/new')}>
            <IonIcon icon={addOutline} />
          </IonFabButton>
        </IonFab>
      </IonContent>
    </IonPage>
  );
}
