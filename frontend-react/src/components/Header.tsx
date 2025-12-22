import React from 'react';

type Category = { name: string; subcategories?: string[] };

export default function Header({ siteTitle, categories }: { siteTitle: string; categories: Category[] }) {
  return (
    <header className="bg-white shadow-sm">
      <div className="max-w-5xl mx-auto px-4 py-3 flex items-center justify-between">
        <div className="flex items-center space-x-3">
          <div className="text-lg font-bold text-brand">{siteTitle}</div>
          <nav className="hidden md:flex space-x-2 text-sm">
            {categories.map(cat => (
              <div key={cat.name} className="relative group">
                <button className="px-3 py-1 rounded hover:bg-slate-100">{cat.name}</button>
                {cat.subcategories && (
                  <div className="absolute left-0 mt-1 bg-white border rounded shadow-md opacity-0 group-hover:opacity-100 transition p-2 z-10">
                    {cat.subcategories.map(s => <div className="text-sm px-2 py-1" key={s}>{s}</div>)}
                  </div>
                )}
              </div>
            ))}
          </nav>
        </div>
        <div className="text-sm">
          <a className="px-3 py-1 rounded bg-brand text-white" href="/login">Login</a>
        </div>
      </div>
    </header>
  );
}
