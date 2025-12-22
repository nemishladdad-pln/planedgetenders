import React, { useState } from 'react';
import { useParams, useNavigate } from 'react-router-dom';
import client from '../api/client';

export default function BudgetUploadPage() {
  const { id } = useParams<{ id: string }>();
  const navigate = useNavigate();
  const [type, setType] = useState<'lumpsum'|'file'>('lumpsum');
  const [amount, setAmount] = useState('');
  const [file, setFile] = useState<File | null>(null);
  const [loading, setLoading] = useState(false);

  async function submit(e: React.FormEvent) {
    e.preventDefault();
    const fd = new FormData();
    fd.append('type', type);
    if (type === 'lumpsum') fd.append('amount', amount);
    if (file) fd.append('budgetFile', file);
    setLoading(true);
    try {
      await client.post(`/api/tenders/${id}/budget`, fd, {
        headers: { 'Content-Type': 'multipart/form-data' }
      });
      alert('Budget uploaded');
      navigate('/dashboard');
    } catch (err) {
      alert('Upload failed');
    } finally { setLoading(false); }
  }

  return (
    <div className="max-w-md mx-auto p-4 bg-white rounded shadow">
      <h3 className="mb-3 font-medium">Upload Budget for Tender {id}</h3>
      <form onSubmit={submit}>
        <div className="mb-2">
          <label className="block text-sm">Type</label>
          <select value={type} onChange={e => setType(e.target.value as any)} className="border p-2 w-full">
            <option value="lumpsum">Lumpsum (amount)</option>
            <option value="file">Upload File</option>
          </select>
        </div>

        {type === 'lumpsum' ? (
          <div className="mb-2">
            <label className="block text-sm">Amount</label>
            <input type="number" value={amount} onChange={e => setAmount(e.target.value)} className="border p-2 w-full" />
          </div>
        ) : (
          <div className="mb-2">
            <label className="block text-sm">Budget File</label>
            <input type="file" onChange={e => setFile(e.target.files?.[0] ?? null)} />
          </div>
        )}

        <button className="mt-3 bg-brand text-white px-3 py-2" disabled={loading}>{loading ? 'Uploading...' : 'Upload'}</button>
      </form>
    </div>
  );
}
