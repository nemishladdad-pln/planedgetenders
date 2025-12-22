import React, { useEffect, useState } from 'react';
import client from '../api/client';
import TenderList from '../components/TenderList';
import { useAuth } from '../auth/AuthContext';

export default function Dashboard() {
  const [summary, setSummary] = useState<any>(null);
  const [tenders, setTenders] = useState<any[]>([]);
  const [loading, setLoading] = useState(true);
  const { logout } = useAuth();

  useEffect(() => {
    async function load() {
      try {
        const [d, t] = await Promise.all([
          client.get('/api/admin/dashboard').then(r => r.data).catch(() => null),
          client.get('/api/tenders').then(r => {
            const data = Array.isArray(r.data) ? r.data : (r.data.data || r.data);
            return data || [];
          }).catch(() => [])
        ]);
        setSummary(d);
        setTenders(t);
      } finally {
        setLoading(false);
      }
    }
    load();
  }, []);

  return (
    <div className="max-w-6xl mx-auto p-4">
      <div className="flex justify-between items-center mb-4">
        <h2 className="text-xl font-semibold">Dashboard</h2>
        <div>
          <button onClick={logout} className="px-3 py-1 bg-red-500 text-white rounded">Logout</button>
        </div>
      </div>

      {loading ? <div>Loading...</div> : (
        <>
          <div className="grid grid-cols-3 gap-4 mb-6">
            <div className="bg-white p-4 rounded shadow">
              <div className="text-sm">Registered users</div>
              <div className="text-2xl font-bold">{summary?.users ?? '-'}</div>
            </div>
            <div className="bg-white p-4 rounded shadow">
              <div className="text-sm">Tenders floated</div>
              <div className="text-2xl font-bold">{summary?.tenders ?? '-'}</div>
            </div>
            <div className="bg-white p-4 rounded shadow">
              <div className="text-sm">Tender status</div>
              <div className="text-sm">{summary?.tender_status ? Object.entries(summary.tender_status).map(([k,v])=>`${k}:${v}`).join(', ') : '-'}</div>
            </div>
          </div>

          <section>
            <h3 className="text-lg mb-2">Tenders</h3>
            <TenderList tenders={tenders} />
          </section>
        </>
      )}
    </div>
  );
}
