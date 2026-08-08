import {
  IonButton,
  IonContent,
  IonInput,
  IonItem,
  IonList,
  IonLoading,
  IonPage,
  IonText,
  IonTitle,
  IonToolbar,
} from '@ionic/react';
import { type FormEvent, useState } from 'react';
import { Redirect } from 'react-router-dom';
import { useAuth } from '../auth/AuthContext';

export default function Login() {
  const { login, isAuthenticated } = useAuth();
  const [email, setEmail] = useState('');
  const [password, setPassword] = useState('');
  const [error, setError] = useState<string | null>(null);
  const [submitting, setSubmitting] = useState(false);

  if (isAuthenticated) {
    return <Redirect to="/tasks" />;
  }

  async function handleSubmit(event: FormEvent) {
    event.preventDefault();
    setError(null);
    setSubmitting(true);
    try {
      await login(email, password);
    } catch (err: any) {
      const message =
        err?.response?.data?.errors?.email?.[0] ??
        err?.response?.data?.message ??
        'Unable to log in. Check your connection and try again.';
      setError(message);
    } finally {
      setSubmitting(false);
    }
  }

  return (
    <IonPage>
      <IonContent className="ion-padding">
        <IonToolbar>
          <IonTitle>ICT Asset Management</IonTitle>
        </IonToolbar>

        <form onSubmit={handleSubmit}>
          <IonList>
            <IonItem>
              <IonInput
                label="Email"
                labelPlacement="stacked"
                type="email"
                value={email}
                required
                autocomplete="username"
                onIonInput={(e) => setEmail(e.detail.value ?? '')}
              />
            </IonItem>
            <IonItem>
              <IonInput
                label="Password"
                labelPlacement="stacked"
                type="password"
                value={password}
                required
                autocomplete="current-password"
                onIonInput={(e) => setPassword(e.detail.value ?? '')}
              />
            </IonItem>
          </IonList>

          {error && (
            <IonText color="danger">
              <p className="ion-padding-start">{error}</p>
            </IonText>
          )}

          <IonButton
            expand="block"
            type="submit"
            className="ion-margin-top"
            disabled={submitting}
          >
            Log in
          </IonButton>
        </form>

        <IonLoading isOpen={submitting} message="Logging in..." />
      </IonContent>
    </IonPage>
  );
}
