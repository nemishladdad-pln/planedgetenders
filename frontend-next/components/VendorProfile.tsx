export default function VendorProfile({ vendor }: any) {
  return (
    <div className="p-4 bg-white rounded shadow">
      <div className="text-xl font-semibold">{vendor.name}</div>
      <div className="text-sm text-gray-600">Contact: {vendor.contact}</div>
      <div className="mt-3">
        <h3 className="font-medium">History</h3>
        <ul className="mt-2 space-y-1">
          {(vendor.history || []).map((h: any, i: number) => (
            <li key={i} className="text-sm">{h.ts} — {h.event || JSON.stringify(h)}</li>
          ))}
        </ul>
      </div>
    </div>
  )
}
