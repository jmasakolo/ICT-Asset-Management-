import {
  IonIcon,
  IonLabel,
  IonRouterOutlet,
  IonTabBar,
  IonTabButton,
  IonTabs,
} from '@ionic/react';
import { checkboxOutline, cubeOutline } from 'ionicons/icons';
import { Redirect, Route, Switch } from 'react-router-dom';
import { useAuth } from '../auth/AuthContext';
import AssetForm from './assets/AssetForm';
import AssetList from './assets/AssetList';
import TaskForm from './tasks/TaskForm';
import TaskList from './tasks/TaskList';

export default function MainTabs() {
  const { isAuthenticated } = useAuth();

  if (!isAuthenticated) {
    return <Redirect to="/login" />;
  }

  return (
    <IonTabs>
      <IonRouterOutlet>
        <Switch>
          <Route exact path="/tasks" component={TaskList} />
          <Route exact path="/tasks/new" component={TaskForm} />
          <Route exact path="/tasks/:id/edit" component={TaskForm} />
          <Route exact path="/assets" component={AssetList} />
          <Route exact path="/assets/new" component={AssetForm} />
          <Route exact path="/assets/:id/edit" component={AssetForm} />
          <Route exact path="/" render={() => <Redirect to="/tasks" />} />
        </Switch>
      </IonRouterOutlet>

      <IonTabBar slot="bottom">
        <IonTabButton tab="tasks" href="/tasks">
          <IonIcon icon={checkboxOutline} />
          <IonLabel>Tasks</IonLabel>
        </IonTabButton>
        <IonTabButton tab="assets" href="/assets">
          <IonIcon icon={cubeOutline} />
          <IonLabel>Assets</IonLabel>
        </IonTabButton>
      </IonTabBar>
    </IonTabs>
  );
}
