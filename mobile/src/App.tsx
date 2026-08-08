import { IonApp, IonRouterOutlet } from '@ionic/react';
import { IonReactRouter } from '@ionic/react-router';
import { Route, Switch } from 'react-router-dom';
import { AuthProvider, useAuth } from './auth/AuthContext';
import Login from './pages/Login';
import MainTabs from './pages/MainTabs';

function Routes() {
  const { isLoading } = useAuth();

  if (isLoading) {
    // Session restore from Preferences happens once on boot; avoid a flash
    // of the login screen while that's in flight.
    return null;
  }

  return (
    <IonReactRouter>
      <IonRouterOutlet>
        {/* Switch is required here: without it, the non-exact "/" route
            below also matches "/login" and React Router tries to render
            both routes at once, which IonRouterOutlet can't resolve into a
            single active page (renders neither). */}
        <Switch>
          <Route exact path="/login" component={Login} />
          <Route path="/" component={MainTabs} />
        </Switch>
      </IonRouterOutlet>
    </IonReactRouter>
  );
}

export default function App() {
  return (
    <IonApp>
      <AuthProvider>
        <Routes />
      </AuthProvider>
    </IonApp>
  );
}
