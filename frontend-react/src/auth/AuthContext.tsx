import React, { createContext, useContext, useEffect, useState } from 'react';
import client, { setAuthToken } from '../api/client';

type AuthContextType = {
  token: string | null;
  user: any | null;
  loginWithPassword: (email: string, password: string) => Promise<void>;
  loginWithOtp: (phone: string, code: string) => Promise<void>;
  logout: () => void;
};

const AuthContext = createContext<AuthContextType | undefined>(undefined);

export function AuthProvider({ children }: { children: React.ReactNode }) {
  const [token, setToken] = useState<string | null>(() => localStorage.getItem('token'));
  const [user, setUser] = useState<any | null>(null);

  useEffect(() => {
    setAuthToken(token);
    if (token) {
      // attempt to fetch current user
      client.get('/api/user').then(r => setUser(r.data)).catch(() => setUser(null));
    } else {
      setUser(null);
    }
  }, [token]);

  async function loginWithPassword(email: string, password: string) {
    const r = await client.post('/api/login', { email, password });
    const t = r.data?.token || r.data?.access_token || null;
    if (t) {
      setToken(t);
      localStorage.setItem('token', t);
      setAuthToken(t);
      const me = await client.get('/api/user');
      setUser(me.data);
    }
  }

  async function loginWithOtp(phone: string, code: string) {
    const r = await client.post('/api/auth/verify-otp', { phone, code });
    // backend may return token on verify; handle token if present
    const t = r.data?.token || null;
    if (t) {
      setToken(t);
      localStorage.setItem('token', t);
      setAuthToken(t);
      const me = await client.get('/api/user');
      setUser(me.data);
    }
  }

  function logout() {
    setToken(null);
    setUser(null);
    localStorage.removeItem('token');
    setAuthToken(null);
  }

  return (
    <AuthContext.Provider value={{ token, user, loginWithPassword, loginWithOtp, logout }}>
      {children}
    </AuthContext.Provider>
  );
}

export function useAuth() {
  const ctx = useContext(AuthContext);
  if (!ctx) throw new Error('useAuth must be used within AuthProvider');
  return ctx;
}
