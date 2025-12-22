import React, { useEffect, useState } from 'react';
import client from '../api/client';

export default function ProjectAccess() {
  const [projects, setProjects] = useState<any[]>([]);
  const [roles, setRoles] = useState<any[]>([]);
  const [selectedProjects, setSelectedProjects] = useState<number[]>([]);
  const [selectedRoles, setSelectedRoles] = useState<number[]>([]);
  const [assignments, setAssignments] = useState<any[]>([]);
  const [loading, setLoading] = useState(false);
  const [error, setError] = useState<string | null>(null);

  useEffect(() => {
    fetchAll();
  }, []);

  async function fetchAll() {
    try {
      const [p, r, a] = await Promise.allSettled([
        client.get('/api/admin/projects'),
        client.get('/api/admin/roles'),
        client.get('/api/admin/project-assignments')
      ]);
      if (p.status === 'fulfilled') setProjects(p.value.data);
      if (r.status === 'fulfilled') setRoles(r.value.data);
      if (a.status === 'fulfilled') setAssignments(a.value.data);
    } catch (e) {
      // ignore
    }
  }

  async function loadProjectRolesForSelection(projectIds: number[]) {
    // when multiple projects selected, compute union of their assigned role ids
    try {
      const roleIdSets: number[][] = [];
      for (const pid of projectIds) {
        const res = await client.get(`/api/admin/projects/${pid}/roles`);
        const ids = res.data.map((x: any) => x.id);
        roleIdSets.push(ids);
      }
      // union
      const union = Array.from(new Set(roleIdSets.flat()));
      setSelectedRoles(union);
    } catch (err) {
      setSelectedRoles([]);
    }
  }

  function toggleProject(id: number) {
    setSelectedProjects(prev => {
      const next = prev.includes(id) ? prev.filter(x => x !== id) : [...prev, id];
      // update selected roles based on new selection
      if (next.length) loadProjectRolesForSelection(next);
      else setSelectedRoles([]);
      return next;
    });
  }

  function toggleRole(id: number) {
    setSelectedRoles(prev => prev.includes(id) ? prev.filter(x => x !== id) : [...prev, id]);
  }

  async function submit() {
    setError(null);
    if (!selectedProjects.length) {
      setError('Please select at least one project');
      return;
    }
    if (!selectedRoles.length) {
      setError('Select at least one role to assign');
      return;
    }
    setLoading(true);
    try {
      // call bulk assign endpoint
      await client.post('/api/admin/projects/assign-multiple', { project_ids: selectedProjects, roles: selectedRoles });
      alert('Roles assigned to selected projects');
      fetchAll();
    } catch (err: any) {
      setError(err?.response?.data?.message || 'Failed to assign');
    } finally {
      setLoading(false);
    }
  }

  return (
    <div className="max-w-6xl mx-auto p-4">
      <h2 className="text-xl mb-4">Project Access Management</h2>

      <div className="grid grid-cols-3 gap-4 mb-6">
        <div className="col-span-1 bg-white p-4 rounded shadow">
          <div className="text-sm mb-2">Select Projects (multi-select)</div>
          <div className="max-h-52 overflow-auto space-y-1 mb-3">
            {projects.map(p => (
              <label key={p.id} className="flex items-center space-x-2">
                <input type="checkbox" checked={selectedProjects.includes(p.id)} onChange={() => toggleProject(p.id)} />
                <span>{p.name ?? `Project ${p.id}`}</span>
              </label>
            ))}
          </div>

          <div className="mt-2">
            <div className="text-sm mb-2">Available Roles</div>
            <div className="space-y-1 max-h-48 overflow-auto">
              {roles.map(r => (
                <label key={r.id} className="flex items-center space-x-2">
                  <input type="checkbox" checked={selectedRoles.includes(r.id)} onChange={() => toggleRole(r.id)} />
                  <span>{r.name}</span>
                </label>
              ))}
            </div>
          </div>

          {error && <div className="text-sm text-red-600 mt-2">{error}</div>}

          <button onClick={submit} className="mt-3 bg-brand text-white px-3 py-2" disabled={loading}>
            {loading ? 'Saving...' : 'Assign Roles to Selected Projects'}
          </button>
        </div>

        <div className="col-span-2 bg-white p-4 rounded shadow">
          <h3 className="font-medium mb-2">Current Assignments</h3>
          <div className="space-y-2">
            {assignments.length ? assignments.map(a => (
              <div key={a.id} className="flex justify-between items-center border-b py-2">
                <div>
                  <div className="text-sm font-medium">{a.project_name ?? `Project ${a.project_id}`}</div>
                  <div className="text-sm text-slate-600">Role: {a.role_name}</div>
                </div>
                <div className="text-sm text-slate-500">By: {a.created_by ?? '-'}</div>
              </div>
            )) : <div className="text-sm text-slate-500">No assignments</div>}
          </div>
        </div>
      </div>
    </div>
  );
}
