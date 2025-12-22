import React from 'react';

export default function TenderCard({ tender }: { tender: any }) {
  return (
    <article className="bg-white p-4 rounded shadow-sm flex justify-between items-center">
      <div>
        <div className="text-lg font-medium">{tender.title}</div>
        <div className="text-sm text-slate-500">{tender.category} / {tender.subcategory}</div>
      </div>
      <div className="text-right">
        <div className="text-sm">{tender.status}</div>
        <div className="text-sm text-slate-600">{tender.budget_type === 'lumpsum' ? `₹ ${tender.budget_amount ?? '-'}` : (tender.budget_type === 'file' ? 'Budget File' : '—')}</div>
      </div>
    </article>
  );
}
