'use client'

import Link from 'next/link'
import { usePathname } from 'next/navigation'
import { LayoutDashboard } from './icons/LayoutDashboard'

const navItems = [{ name: 'Manual Paper', href: '/manual-paper' }]

export default function Sidebar() {
  const pathname = usePathname()
  return (
    <aside className="bg-neutral-900/70 border-r border-neutral-800 px-5 py-6 flex flex-col gap-8">
      <div className="flex items-center gap-3">
        <div className="w-11 h-11 rounded-xl bg-neutral-50 text-neutral-900 flex items-center justify-center font-bold text-lg tracking-tight">
          HW
        </div>
        <div>
          <p className="text-sm text-neutral-400">hayu widyas</p>
          <p className="font-semibold text-lg leading-tight">Office Dashboard</p>
        </div>
      </div>
      <nav className="flex-1 space-y-2">
        {navItems.map((item) => {
          const active = pathname?.startsWith(item.href)
          return (
            <Link
              key={item.name}
              href={item.href}
              className={`flex items-center gap-2 px-3 py-2 rounded-xl transition-colors border border-transparent ${
                active ? 'bg-neutral-800/80 border-neutral-700 text-white' : 'text-neutral-400 hover:text-white hover:bg-neutral-800/50'
              }`}
            >
              <LayoutDashboard className="w-5 h-5" />
              <span>{item.name}</span>
            </Link>
          )
        })}
      </nav>
      <div className="text-xs text-neutral-500">
        <p>hayu widyas</p>
        <p className="text-neutral-400">Crafted for internal use</p>
      </div>
    </aside>
  )
}
