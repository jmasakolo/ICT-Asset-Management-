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
  IonText,
  IonTitle,
  IonToolbar,
} from '@ionic/react';
import { type FormEvent, useEffect, useState } from 'react';
import { useHistory, useParams } from 'react-router-dom';
import { createAsset, getAsset, updateAsset } from '../../api/assets';
import { listDepartments, listLocations, listUsers } from '../../api/directory';
import {
  ASSET_CONDITIONS,
  ASSET_STATUSES,
  type AssetCondition,
  type AssetStatus,
  type Department,
  type Location,
  type User,
} from '../../types';

export default function AssetForm() {
  const { id } = useParams<{ id?: string }>();
  const history = useHistory();
  const isEditing = Boolean(id);

  const [name, setName] = useState('');
  const [category, setCategory] = useState('');
  const [model, setModel] = useState('');
  const [serialNumber, setSerialNumber] = useState('');
  const [assetTag, setAssetTag] = useState('');
  const [condition, setCondition] = useState<AssetCondition>('new');
  const [status, setStatus] = useState<AssetStatus>('configuration');
  const [value, setValue] = useState('0');
  const [assignedUserId, setAssignedUserId] = useState<number | null>(null);
  const [departmentId, setDepartmentId] = useState<number | null>(null);
  const [locationId, setLocationId] = useState<number | null>(null);
  const [receivedAt, setReceivedAt] = useState('');
  const [warrantyExpiresAt, setWarrantyExpiresAt] = useState('');

  const [users, setUsers] = useState<User[]>([]);
  const [departments, setDepartments] = useState<Department[]>([]);
  const [locations, setLocations] = useState<Location[]>([]);

  const [loading, setLoading] = useState(true);
  const [saving, setSaving] = useState(false);
  const [error, setError] = useState<string | null>(null);

  useEffect(() => {
    const directory = Promise.all([listUsers(), listDepartments(), listLocations()]).then(
      ([u, d, l]) => {
        setUsers(u);
        setDepartments(d);
        setLocations(l);
      },
    );

    const existing = id
      ? getAsset(Number(id)).then((asset) => {
          setName(asset.name);
          setCategory(asset.category);
          setModel(asset.model ?? '');
          setSerialNumber(asset.serial_number ?? '');
          setAssetTag(asset.asset_tag ?? '');
          setCondition(asset.condition);
          setStatus(asset.status);
          setValue(String(asset.value));
          setAssignedUserId(asset.assigned_user_id);
          setDepartmentId(asset.department_id);
          setLocationId(asset.location_id);
          setReceivedAt(asset.received_at ?? '');
          setWarrantyExpiresAt(asset.warranty_expires_at ?? '');
        })
      : Promise.resolve();

    Promise.all([directory, existing]).finally(() => setLoading(false));
  }, [id]);

  async function handleSubmit(event: FormEvent) {
    event.preventDefault();
    setError(null);
    setSaving(true);
    try {
      const payload = {
        name,
        category,
        model: model || null,
        serial_number: serialNumber || null,
        asset_tag: assetTag || null,
        condition,
        status,
        value: Number(value) || 0,
        assigned_user_id: assignedUserId,
        department_id: departmentId,
        location_id: locationId,
        received_at: receivedAt || null,
        warranty_expires_at: warrantyExpiresAt || null,
      };
      if (id) {
        await updateAsset(Number(id), payload);
      } else {
        await createAsset(payload);
      }
      history.push('/assets');
    } catch (err: any) {
      const errors = err?.response?.data?.errors;
      const firstError = errors ? (Object.values(errors)[0] as string[] | undefined)?.[0] : null;
      setError(firstError ?? err?.response?.data?.message ?? 'Unable to save this asset.');
    } finally {
      setSaving(false);
    }
  }

  return (
    <IonPage>
      <IonHeader>
        <IonToolbar>
          <IonButtons slot="start">
            <IonBackButton defaultHref="/assets" />
          </IonButtons>
          <IonTitle>{isEditing ? 'Edit Asset' : 'New Asset'}</IonTitle>
        </IonToolbar>
      </IonHeader>

      <IonContent className="ion-padding">
        <IonLoading isOpen={loading} message="Loading..." />

        <form onSubmit={handleSubmit}>
          <IonList>
            <IonItem>
              <IonInput
                label="Name"
                labelPlacement="stacked"
                value={name}
                required
                maxlength={255}
                onIonInput={(e) => setName(e.detail.value ?? '')}
              />
            </IonItem>

            <IonItem>
              <IonInput
                label="Category"
                labelPlacement="stacked"
                value={category}
                required
                maxlength={255}
                onIonInput={(e) => setCategory(e.detail.value ?? '')}
              />
            </IonItem>

            <IonItem>
              <IonInput
                label="Model"
                labelPlacement="stacked"
                value={model}
                maxlength={255}
                onIonInput={(e) => setModel(e.detail.value ?? '')}
              />
            </IonItem>

            <IonItem>
              <IonInput
                label="Serial number"
                labelPlacement="stacked"
                value={serialNumber}
                maxlength={255}
                onIonInput={(e) => setSerialNumber(e.detail.value ?? '')}
              />
            </IonItem>

            <IonItem>
              <IonInput
                label="Asset tag"
                labelPlacement="stacked"
                value={assetTag}
                maxlength={255}
                onIonInput={(e) => setAssetTag(e.detail.value ?? '')}
              />
            </IonItem>

            <IonItem>
              <IonSelect
                label="Condition"
                labelPlacement="stacked"
                value={condition}
                onIonChange={(e) => setCondition(e.detail.value as AssetCondition)}
              >
                {ASSET_CONDITIONS.map((c) => (
                  <IonSelectOption key={c} value={c}>
                    {c}
                  </IonSelectOption>
                ))}
              </IonSelect>
            </IonItem>

            <IonItem>
              <IonSelect
                label="Status"
                labelPlacement="stacked"
                value={status}
                onIonChange={(e) => setStatus(e.detail.value as AssetStatus)}
              >
                {ASSET_STATUSES.map((s) => (
                  <IonSelectOption key={s} value={s}>
                    {s}
                  </IonSelectOption>
                ))}
              </IonSelect>
            </IonItem>

            <IonItem>
              <IonInput
                label="Value"
                labelPlacement="stacked"
                type="number"
                min="0"
                step="0.01"
                value={value}
                onIonInput={(e) => setValue(e.detail.value ?? '0')}
              />
            </IonItem>

            <IonItem>
              <IonSelect
                label="Assigned to"
                labelPlacement="stacked"
                interface="popover"
                value={assignedUserId}
                onIonChange={(e) => setAssignedUserId(e.detail.value)}
              >
                <IonSelectOption value={null}>Unassigned</IonSelectOption>
                {users.map((u) => (
                  <IonSelectOption key={u.id} value={u.id}>
                    {u.name}
                  </IonSelectOption>
                ))}
              </IonSelect>
            </IonItem>

            <IonItem>
              <IonSelect
                label="Department"
                labelPlacement="stacked"
                interface="popover"
                value={departmentId}
                onIonChange={(e) => setDepartmentId(e.detail.value)}
              >
                <IonSelectOption value={null}>None</IonSelectOption>
                {departments.map((d) => (
                  <IonSelectOption key={d.id} value={d.id}>
                    {d.name}
                  </IonSelectOption>
                ))}
              </IonSelect>
            </IonItem>

            <IonItem>
              <IonSelect
                label="Location"
                labelPlacement="stacked"
                interface="popover"
                value={locationId}
                onIonChange={(e) => setLocationId(e.detail.value)}
              >
                <IonSelectOption value={null}>None</IonSelectOption>
                {locations.map((l) => (
                  <IonSelectOption key={l.id} value={l.id}>
                    {l.name}
                  </IonSelectOption>
                ))}
              </IonSelect>
            </IonItem>

            <IonItem>
              <IonInput
                label="Received"
                labelPlacement="stacked"
                type="date"
                value={receivedAt}
                onIonInput={(e) => setReceivedAt(e.detail.value ?? '')}
              />
            </IonItem>

            <IonItem>
              <IonInput
                label="Warranty expires"
                labelPlacement="stacked"
                type="date"
                value={warrantyExpiresAt}
                onIonInput={(e) => setWarrantyExpiresAt(e.detail.value ?? '')}
              />
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
