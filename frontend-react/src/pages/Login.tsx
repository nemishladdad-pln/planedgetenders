import React, { useState } from 'react';
import { useNavigate } from 'react-router-dom';
import { useAuth } from '../auth/AuthContext';
import client from '../api/client';

export default function Login() {
  const { loginWithPassword, loginWithOtp } = useAuth();
  const navigate = useNavigate();
  const [email, setEmail] = useState('');
  const [password, setPassword] = useState('');
  const [phone, setPhone] = useState('');
  const [otp, setOtp] = useState('');
  const [tab, setTab] = useState<'password'|'otp'>('password');

  async function submitPassword(e: React.FormEvent) {
    e.preventDefault();
    try {
      await loginWithPassword(email, password);
      navigate('/dashboard');
    } catch (err) { alert('Login failed'); }
  }

  async function requestOtp() {
    await client.post('/api/auth/request-otp', { phone });
    alert('OTP sent to WhatsApp (placeholder).');
  }

  async function submitOtp(e: React.FormEvent) {
    e.preventDefault();
    try {
      await loginWithOtp(phone, otp);
      navigate('/dashboard');
    } catch { alert('OTP verify failed'); }
  }

  return (
    <div className="max-w-md mx-auto mt-12">
      <div className="bg-white p-6 rounded shadow">
        <div className="flex space-x-2 mb-4">
          <button className={`px-3 py-1 ${tab==='password'?'bg-brand text-white':''}`} onClick={() => setTab('password')}>Password</button>
          <button className={`px-3 py-1 ${tab==='otp'?'bg-brand text-white':''}`} onClick={() => setTab('otp')}>OTP (WhatsApp)</button>
        </div>

        {tab === 'password' ? (
          <form onSubmit={submitPassword}>
            <input value={email} onChange={e => setEmail(e.target.value)} className="w-full mb-2 p-2 border" placeholder="Email" />
            <input value={password} onChange={e => setPassword(e.target.value)} type="password" className="w-full mb-2 p-2 border" placeholder="Password" />
            <button className="w-full bg-brand text-white py-2">Login</button>
          </form>
        ) : (
          <form onSubmit={submitOtp}>
            <input value={phone} onChange={e => setPhone(e.target.value)} className="w-full mb-2 p-2 border" placeholder="Phone" />
            <div className="flex gap-2">
              <button type="button" onClick={requestOtp} className="flex-1 bg-slate-200 p-2">Request OTP</button>
              <input value={otp} onChange={e => setOtp(e.target.value)} className="w-24 p-2 border" placeholder="Code" />
            </div>
            <button className="w-full mt-3 bg-brand text-white py-2">Verify</button>
          </form>
        )}
      </div>
    </div>
  );
}
