import React from 'react';
import TenderCard from './TenderCard';

export default { title: 'Components/TenderCard', component: TenderCard };

export const Example = () => (
  <div style={{ width: 500 }}>
    <TenderCard tender={{ id: 't-1', title: 'Sample Tender', category: 'RCC', subcategory: 'Concrete', status: 'Active', budget_type: 'lumpsum', budget_amount: 500000 }} />
  </div>
);
