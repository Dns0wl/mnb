'use client'

import { format } from '../lib/date'

export interface ManualPaperTemplateProps {
  customerName: string
  purchaseDate: string
  purchaseChannel: string
}

export default function ManualPaperTemplate({ customerName, purchaseDate, purchaseChannel }: ManualPaperTemplateProps) {
  const formattedDate = format(purchaseDate)

  return (
    <div className="a5-surface leather-pattern text-neutral-900 relative overflow-hidden bg-white">
      <div className="absolute inset-0 opacity-70" aria-hidden>
        <div className="absolute inset-0 bg-[radial-gradient(circle_at_20%_20%,rgba(0,0,0,0.04),transparent_35%),radial-gradient(circle_at_80%_10%,rgba(0,0,0,0.04),transparent_25%)]" />
      </div>
      <div className="relative z-10 h-full flex flex-col">
        <div className="px-8 pt-10 pb-6 flex flex-col items-center text-center gap-2">
          <div className="w-14 h-14 rounded-lg bg-neutral-900 text-white flex items-center justify-center font-semibold text-2xl tracking-tight">
            H
          </div>
          <div className="font-script text-2xl text-neutral-800">hayu widyas</div>
          <div className="text-neutral-700 text-sm uppercase tracking-[0.28em] mt-2">Thanks To</div>
          <div className="bg-white/70 border border-neutral-200 rounded-xl px-4 py-3 mt-2 shadow-sm min-w-[260px]">
            <p className="text-sm text-neutral-600">Customer Name</p>
            <p className="font-semibold text-lg text-neutral-900">{customerName}</p>
            <p className="text-sm text-neutral-600 mt-2">Purchase Date</p>
            <p className="font-semibold text-neutral-900">{formattedDate}</p>
            <p className="text-sm text-neutral-600 mt-2">Purchase Channel</p>
            <p className="font-semibold text-neutral-900">{purchaseChannel}</p>
          </div>
        </div>

        <div className="px-10">
          <div className="deco-divider text-neutral-700">
            <span className="w-2 h-2 rounded-full bg-neutral-800" aria-hidden />
          </div>
        </div>

        <div className="mt-6 flex-1 flex flex-col">
          <div className="mx-0 flex-1 relative">
            <div className="absolute inset-x-0 top-[20%] h-[160px] bg-neutral-900 text-white flex flex-col items-center justify-center gap-3 shadow-lg">
              <div className="flex flex-col items-center">
                <div className="w-9 h-9 rounded-full border border-white/40 flex items-center justify-center text-xs tracking-widest uppercase">
                  HW
                </div>
                <div className="mt-2 text-lg font-display">Genuine Leather 100%</div>
              </div>
            </div>
          </div>
        </div>

        <div className="relative mt-auto">
          <div className="px-8 pb-8 pt-20">
            <div className="relative">
              <div className="absolute right-0 bottom-0 w-[70%] h-[140px] bg-neutral-900 text-white rounded-tl-[60px] rounded-tr-3xl rounded-bl-3xl p-6 shadow-xl">
                <p className="text-[11px] tracking-[0.2em] text-neutral-300">CUSTOMER SERVICE ON WHATSAPP</p>
                <p className="text-2xl font-semibold mt-2">+62 813 8370 8797</p>
              </div>
            </div>
          </div>
        </div>
      </div>
    </div>
  )
}
