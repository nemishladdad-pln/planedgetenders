import React from 'react';
import TenderCard from './TenderCard';

export default function TenderList({ tenders }: { tenders: any[] }) {
  if (!tenders.length) return <div className="p-4 bg-white rounded">No tenders found.</div>;
  return (
    <div className="grid gap-3">
      {tenders.map(t => <TenderCard key={t.id} tender={t} />)}
    </div>
  );
}
