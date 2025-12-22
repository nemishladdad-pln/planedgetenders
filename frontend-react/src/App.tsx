import React, { useEffect, useState } from 'react';
import { BrowserRouter, Routes, Route, Link } from 'react-router-dom';
import axios from 'axios';
import { AuthProvider } from './auth/AuthContext';
import ProtectedRoute from './auth/ProtectedRoute';
import Login from './pages/Login';
import Dashboard from './pages/Dashboard';
import Header from './components/Header';
import AdminPanel from './pages/AdminPanel';
import RolesPage from './pages/Roles';
import ProjectAccess from './pages/ProjectAccess';
import BudgetUploadPage from './pages/BudgetUploadPage';

type Tender = {
  id: number | string;
  title: string;
  status?: string;
  category?: string;
  subcategory?: string;
  budget?: { type?: string; amount?: number; file?: string };
};

export default function App() {
  const [tenders, setTenders] = useState<Tender[]>([]);
  const [loading, setLoading] = useState(true);

  useEffect(() => {
    axios.get('/api/tenders')
      .then(res => {
        // Accept both paginated and array responses
        const data = Array.isArray(res.data) ? res.data : (res.data.data || res.data);
        setTenders(data || []);
      })
      .catch(() => setTenders([]))
      .finally(() => setLoading(false));
  }, []);

  return (
    <AuthProvider>
      <BrowserRouter>
        <Header
          siteTitle="Planedge"
          categories={[
            { name: 'RCC', subcategories: ['Concrete', 'Rebar', 'Finishing'] },
            { name: 'Electrical', subcategories: ['Wiring', 'Lighting'] }
          ]}
        />
        <Routes>
          <Route path="/login" element={<Login />} />
          <Route path="/dashboard" element={<ProtectedRoute><Dashboard /></ProtectedRoute>} />
          <Route path="/admin" element={<ProtectedRoute><AdminPanel /></ProtectedRoute>} />
          <Route path="/admin/roles" element={<ProtectedRoute><RolesPage /></ProtectedRoute>} />
          <Route path="/admin/projects" element={<ProtectedRoute><ProjectAccess /></ProtectedRoute>} />
          <Route path="/tenders/:id/budget" element={<ProtectedRoute><BudgetUploadPage /></ProtectedRoute>} />
          <Route path="/" element={<div className="p-4 max-w-5xl mx-auto"><h1 className="text-xl">Welcome to Planedge</h1><p><Link to="/dashboard" className="text-brand">Go to dashboard</Link></p></div>} />
        </Routes>
      </BrowserRouter>
    </AuthProvider>
  );
}
