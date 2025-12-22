import { useRouter } from 'next/router'
import useSWR from 'swr'
const fetcher = (url: string) => fetch(url).then(r => r.json())

export default function VendorPage() {
  const { query } = useRouter()
  const id = query.id as string
  const { data } = useSWR(() => id ? `/api/tenders/vendors/${id}` : null, fetcher)
  const v = data || null

  return (
    <main className="p-6 max-w-3xl mx-auto">
      {!v && <div>Loading...</div>}
      {v && (
        <section>
          <h1 className="text-2xl font-bold mb-2">{v.name}</h1>
          <div className="text-sm text-gray-600">Contact: {v.contact || '—'}</div>
          <div className="mt-4">
            <h2 className="font-semibold">History</h2>
            <ul className="mt-2 space-y-2">
              {(v.history || []).slice(0,20).map((h: any, idx: number) => (
                <li key={idx} className="text-sm text-gray-700">{h.ts}: {h.event || JSON.stringify(h)}</li>
              ))}
            </ul>
          </div>
        </section>
      )}
    </main>
  )
}
