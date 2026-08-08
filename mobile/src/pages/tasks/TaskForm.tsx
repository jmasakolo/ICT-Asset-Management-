import {
  IonBackButton,
  IonButton,
  IonButtons,
  IonContent,
  IonHeader,
  IonInput,
  IonItem,
  IonList,
  IonLoading,
  IonPage,
  IonSelect,
  IonSelectOption,
  IonTextarea,
  IonTitle,
  IonToggle,
  IonToolbar,
  IonText,
} from '@ionic/react';
import { type FormEvent, useEffect, useState } from 'react';
import { useHistory, useParams } from 'react-router-dom';
import { createTask, getTask, updateTask } from '../../api/tasks';
import { TASK_PRIORITIES, type Priority } from '../../types';

export default function TaskForm() {
  const { id } = useParams<{ id?: string }>();
  const history = useHistory();
  const isEditing = Boolean(id);

  const [title, setTitle] = useState('');
  const [notes, setNotes] = useState('');
  const [dueDate, setDueDate] = useState('');
  const [priority, setPriority] = useState<Priority>('medium');
  const [isDone, setIsDone] = useState(false);
  const [loading, setLoading] = useState(isEditing);
  const [saving, setSaving] = useState(false);
  const [error, setError] = useState<string | null>(null);

  useEffect(() => {
    if (!id) return;
    getTask(Number(id))
      .then((task) => {
        setTitle(task.title);
        setNotes(task.notes ?? '');
        setDueDate(task.due_date ?? '');
        setPriority(task.priority);
        setIsDone(task.is_done);
      })
      .finally(() => setLoading(false));
  }, [id]);

  async function handleSubmit(event: FormEvent) {
    event.preventDefault();
    setError(null);
    setSaving(true);
    try {
      const payload = {
        title,
        notes: notes || null,
        due_date: dueDate || null,
        priority,
        is_done: isDone,
      };
      if (id) {
        await updateTask(Number(id), payload);
      } else {
        await createTask(payload);
      }
      history.push('/tasks');
    } catch (err: any) {
      const message =
        err?.response?.data?.errors?.title?.[0] ??
        err?.response?.data?.message ??
        'Unable to save this task.';
      setError(message);
    } finally {
      setSaving(false);
    }
  }

  return (
    <IonPage>
      <IonHeader>
        <IonToolbar>
          <IonButtons slot="start">
            <IonBackButton defaultHref="/tasks" />
          </IonButtons>
          <IonTitle>{isEditing ? 'Edit Task' : 'New Task'}</IonTitle>
        </IonToolbar>
      </IonHeader>

      <IonContent className="ion-padding">
        <IonLoading isOpen={loading} message="Loading..." />

        <form onSubmit={handleSubmit}>
          <IonList>
            <IonItem>
              <IonInput
                label="Title"
                labelPlacement="stacked"
                value={title}
                required
                maxlength={255}
                onIonInput={(e) => setTitle(e.detail.value ?? '')}
              />
            </IonItem>

            <IonItem>
              <IonInput
                label="Due date"
                labelPlacement="stacked"
                type="date"
                value={dueDate}
                onIonInput={(e) => setDueDate(e.detail.value ?? '')}
              />
            </IonItem>

            <IonItem>
              <IonSelect
                label="Priority"
                labelPlacement="stacked"
                value={priority}
                onIonChange={(e) => setPriority(e.detail.value as Priority)}
              >
                {TASK_PRIORITIES.map((p) => (
                  <IonSelectOption key={p} value={p}>
                    {p}
                  </IonSelectOption>
                ))}
              </IonSelect>
            </IonItem>

            <IonItem>
              <IonTextarea
                label="Notes"
                labelPlacement="stacked"
                value={notes}
                maxlength={5000}
                autoGrow
                onIonInput={(e) => setNotes(e.detail.value ?? '')}
              />
            </IonItem>

            <IonItem>
              <IonToggle checked={isDone} onIonChange={(e) => setIsDone(e.detail.checked)}>
                Done
              </IonToggle>
            </IonItem>
          </IonList>

          {error && (
            <IonText color="danger">
              <p className="ion-padding-start">{error}</p>
            </IonText>
          )}

          <IonButton expand="block" type="submit" className="ion-margin-top" disabled={saving}>
            Save
          </IonButton>
        </form>
      </IonContent>
    </IonPage>
  );
}
