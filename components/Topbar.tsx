'use client'

import { usePathname } from 'next/navigation'

const titles: Record<string, string> = {
  '/manual-paper': 'Manual Paper Generator'
}

export default function Topbar() {
  const pathname = usePathname()
  const title = titles[pathname || ''] || 'Dashboard'

  return (
    <header className="sticky top-0 z-20 bg-neutral-950/80 backdrop-blur border-b border-neutral-800 px-6 lg:px-10 py-4 flex items-center justify-between">
      <div>
        <p className="text-xs uppercase tracking-[0.2em] text-neutral-500">Hayu Widyas Office Dashboard</p>
        <h1 className="text-xl font-semibold">{title}</h1>
      </div>
      <div className="h-10 w-10 rounded-full bg-neutral-800 border border-neutral-700" aria-hidden />
    </header>
  )
}
