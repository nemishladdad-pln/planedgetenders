import React from 'react';
import { Link } from 'react-router-dom';
import { useAuth } from '../auth/AuthContext';

export default function AdminPanel() {
  const { user } = useAuth();

  // basic role-check
  const isAdmin = user?.roles?.some((r: string) => r === 'Admin');

  if (!isAdmin) {
    return <div className="max-w-3xl mx-auto p-4">Access denied. Admins only.</div>;
  }

  return (
    <div className="max-w-5xl mx-auto p-4">
      <h1 className="text-2xl font-semibold mb-4">Admin Panel</h1>
      <div className="grid grid-cols-2 gap-4">
        <Link to="/admin/roles" className="p-4 bg-white rounded shadow">Manage Roles & Permissions</Link>
        <Link to="/admin/projects" className="p-4 bg-white rounded shadow">Project Access & Permissions</Link>
      </div>
    </div>
  );
}
