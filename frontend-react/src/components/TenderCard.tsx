import React from 'react';

export default function TenderCard({ tender }: { tender: any }) {
  const budgetType = tender?.budget?.type;
  const budgetAmount = tender?.budget?.amount;

  return (
    <article className="bg-white p-4 rounded shadow-sm flex justify-between items-center">
      <div>
        <div className="text-lg font-medium">{tender.title}</div>
        <div className="text-sm text-slate-500">{tender.category} / {tender.subcategory}</div>
      </div>
      <div className="text-right">
        <div className="text-sm">{tender.status}</div>
        <div className="text-sm text-slate-600">
          {budgetType === 'lumpsum'
            ? `₹ ${budgetAmount ?? '-'}`
            : (budgetType === 'file' ? 'Budget File' : '—')}
        </div>
      </div>
    </article>
  );
}
