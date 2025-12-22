import useSWR from 'swr'

const fetcher = (url: string) => fetch(url).then(r => r.json())

export default function Home() {
  const { data } = useSWR('/api/tenders/upcoming', fetcher)
  const tenders = data?.data || []

  return (
    <main className="p-6 max-w-4xl mx-auto">
      <h1 className="text-3xl font-bold mb-4">Planedge — Upcoming Tenders</h1>
      <ul className="space-y-3">
        {tenders.map((t: any) => (
          <li key={t.id} className="p-4 border rounded-md">
            <div className="flex justify-between">
              <div>
                <div className="text-lg font-semibold">{t.title}</div>
                <div className="text-sm text-gray-500">{t.category} {t.subcategory ? `› ${t.subcategory}` : ''}</div>
              </div>
              <div className="text-right">
                <div className="text-sm">{t.dueDate ? new Date(t.dueDate).toLocaleString() : '—'}</div>
                <div className="text-xs text-gray-400">{t.status}</div>
              </div>
            </div>
          </li>
        ))}
      </ul>
    </main>
  )
}
