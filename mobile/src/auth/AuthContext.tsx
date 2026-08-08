import { Preferences } from '@capacitor/preferences';
import {
  createContext,
  type ReactNode,
  useCallback,
  useContext,
  useEffect,
  useState,
} from 'react';
import * as authApi from '../api/auth';
import { setUnauthorizedHandler, TOKEN_STORAGE_KEY } from '../api/client';
import type { User } from '../types';

interface AuthContextValue {
  user: User | null;
  isAuthenticated: boolean;
  isLoading: boolean;
  login: (email: string, password: string) => Promise<void>;
  logout: () => Promise<void>;
}

const AuthContext = createContext<AuthContextValue | null>(null);

export function AuthProvider({ children }: { children: ReactNode }) {
  const [user, setUser] = useState<User | null>(null);
  const [isAuthenticated, setIsAuthenticated] = useState(false);
  const [isLoading, setIsLoading] = useState(true);

  const clearSession = useCallback(async () => {
    await Preferences.remove({ key: TOKEN_STORAGE_KEY });
    setUser(null);
    setIsAuthenticated(false);
  }, []);

  useEffect(() => {
    // There's no /api/me endpoint to validate a stored token on boot, so a
    // present token is treated as "authenticated for now" — a stale/expired
    // one is caught by the 401 interceptor on the first real API call.
    (async () => {
      const { value: token } = await Preferences.get({ key: TOKEN_STORAGE_KEY });
      setIsAuthenticated(Boolean(token));
      setIsLoading(false);
    })();
  }, []);

  useEffect(() => {
    setUnauthorizedHandler(() => {
      setUser(null);
      setIsAuthenticated(false);
    });
    return () => setUnauthorizedHandler(null);
  }, []);

  const login = useCallback(async (email: string, password: string) => {
    const { token, user: loggedInUser } = await authApi.login(email, password);
    await Preferences.set({ key: TOKEN_STORAGE_KEY, value: token });
    setUser(loggedInUser);
    setIsAuthenticated(true);
  }, []);

  const logout = useCallback(async () => {
    // Logout must never get "stuck" because the network call failed — the
    // token might already be dead (e.g. expired), so always clear local
    // session state regardless of how the API call turns out.
    try {
      await authApi.logout();
    } catch {
      // ignore — clearing local state below is what actually logs the user out
    }
    await clearSession();
  }, [clearSession]);

  return (
    <AuthContext.Provider value={{ user, isAuthenticated, isLoading, login, logout }}>
      {children}
    </AuthContext.Provider>
  );
}

export function useAuth(): AuthContextValue {
  const context = useContext(AuthContext);
  if (!context) {
    throw new Error('useAuth must be used within an AuthProvider');
  }
  return context;
}
