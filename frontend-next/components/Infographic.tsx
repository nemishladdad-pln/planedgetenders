type Props = { users?: number; tenders?: number; subscriptions?: number; status?: Record<string, number> }
export default function Infographic({ users=0, tenders=0, subscriptions=0, status = {} }: Props) {
  return (
    <div className="grid grid-cols-1 sm:grid-cols-3 gap-4">
      <div className="p-4 bg-white rounded shadow">
        <div className="text-sm text-gray-500">Users</div>
        <div className="text-2xl font-bold">{users}</div>
      </div>
      <div className="p-4 bg-white rounded shadow">
        <div className="text-sm text-gray-500">Tenders</div>
        <div className="text-2xl font-bold">{tenders}</div>
      </div>
      <div className="p-4 bg-white rounded shadow">
        <div className="text-sm text-gray-500">Subscriptions</div>
        <div className="text-2xl font-bold">{subscriptions}</div>
      </div>
    </div>
  )
}
