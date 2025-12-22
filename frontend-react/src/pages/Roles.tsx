import React, { useEffect, useState } from 'react';
import client from '../api/client';

export default function RolesPage() {
  const [roles, setRoles] = useState<any[]>([]);
  const [permissions, setPermissions] = useState<string[]>([]);
  const [users, setUsers] = useState<any[]>([]);
  const [newRole, setNewRole] = useState('');

  useEffect(() => {
    fetchAll();
  }, []);

  async function fetchAll() {
    const [pr, rr, ur] = await Promise.allSettled([
      client.get('/api/admin/permissions'),
      client.get('/api/admin/roles'),
      client.get('/api/admin/users/roles')
    ]);
    if (pr.status === 'fulfilled') setPermissions(pr.value.data);
    if (rr.status === 'fulfilled') setRoles(rr.value.data);
    if (ur.status === 'fulfilled') setUsers(ur.value.data);
  }

  async function createRole() {
    if (!newRole) return;
    await client.post('/api/admin/roles', { name: newRole });
    setNewRole('');
    fetchAll();
  }

  async function toggleRolePermission(roleId: number, permission: string, enabled: boolean) {
    const role = roles.find(r => r.id === roleId);
    const perms = new Set(role.permissions);
    if (enabled) perms.add(permission); else perms.delete(permission);
    await client.post(`/api/admin/roles/${roleId}/permissions`, { permissions: Array.from(perms) });
    fetchAll();
  }

  async function assignRolesToUser(userId: number, rolesToAssign: string[]) {
    await client.post(`/api/admin/users/${userId}/roles`, { roles: rolesToAssign });
    fetchAll();
  }

  return (
    <div className="max-w-6xl mx-auto p-4">
      <h2 className="text-xl mb-2">Roles & Permissions</h2>

      <div className="mb-4 grid grid-cols-2 gap-4">
        <div className="bg-white p-4 rounded shadow">
          <h3 className="font-medium mb-2">Create Role</h3>
          <input className="border p-2 w-full mb-2" value={newRole} onChange={e => setNewRole(e.target.value)} placeholder="Role name" />
          <button className="bg-brand text-white px-3 py-1" onClick={createRole}>Create</button>
        </div>

        <div className="bg-white p-4 rounded shadow">
          <h3 className="font-medium mb-2">All Permissions</h3>
          <div className="text-sm text-slate-600">{permissions.join(', ')}</div>
        </div>
      </div>

      <div className="grid gap-4">
        {roles.map(r => (
          <div key={r.id} className="bg-white p-4 rounded shadow">
            <div className="flex justify-between items-center">
              <div className="font-semibold">{r.name}</div>
              <div className="text-sm">{r.permissions?.length ?? 0} permissions</div>
            </div>
            <div className="mt-2 grid grid-cols-3 gap-2">
              {permissions.map(p => {
                const checked = r.permissions?.includes(p);
                return (
                  <label key={p} className="flex items-center space-x-2 text-sm">
                    <input type="checkbox" checked={!!checked} onChange={e => toggleRolePermission(r.id, p, e.target.checked)} />
                    <span>{p}</span>
                  </label>
                );
              })}
            </div>
          </div>
        ))}
      </div>

      <div className="mt-6 bg-white p-4 rounded shadow">
        <h3 className="font-medium mb-2">Users & Roles</h3>
        {users.map(u => (
          <div key={u.id} className="flex items-center justify-between mb-2">
            <div>
              <div className="font-medium">{u.name} <span className="text-sm text-slate-500">({u.email})</span></div>
              <div className="text-sm">Roles: {u.roles.join(', ')}</div>
            </div>
            <div>
              <select defaultValue={u.roles[0] || ''} onChange={e => assignRolesToUser(u.id, e.target.value ? [e.target.value] : [])} className="border p-1">
                <option value="">-- set role --</option>
                {roles.map(r => <option key={r.id} value={r.name}>{r.name}</option>)}
              </select>
            </div>
          </div>
        ))}
      </div>
    </div>
  );
}
