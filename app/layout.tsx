import type { Metadata } from 'next'
import { Inter, Playfair_Display, Great_Vibes } from 'next/font/google'
import './globals.css'
import Sidebar from '../components/Sidebar'
import Topbar from '../components/Topbar'

const inter = Inter({ subsets: ['latin'], variable: '--font-inter' })
const playfair = Playfair_Display({ subsets: ['latin'], variable: '--font-playfair' })
const greatVibes = Great_Vibes({ subsets: ['latin'], weight: '400', variable: '--font-greatvibes' })

export const metadata: Metadata = {
  title: 'Hayu Widyas Office Dashboard',
  description: 'Internal dashboard to generate branded Manual Paper PDFs.'
}

export default function RootLayout({ children }: { children: React.ReactNode }) {
  return (
    <html lang="en" className={`${inter.variable} ${playfair.variable} ${greatVibes.variable}`}>
      <body className="bg-neutral-950 text-neutral-50">
        <div className="min-h-screen grid grid-cols-1 lg:grid-cols-[260px_1fr]">
          <Sidebar />
          <div className="flex flex-col min-h-screen">
            <Topbar />
            <main className="flex-1 p-6 lg:p-10 bg-neutral-950">{children}</main>
          </div>
        </div>
      </body>
    </html>
  )
}
