'use client'

import { ManualPaperRecord } from '../types/manual-paper'
import { format } from '../lib/date'

interface ManualPaperListProps {
  items: ManualPaperRecord[]
  onPreview: (record: ManualPaperRecord) => void
  onDownload: (record: ManualPaperRecord) => void
}

export default function ManualPaperList({ items, onPreview, onDownload }: ManualPaperListProps) {
  return (
    <div className="card-panel p-4 md:p-6 space-y-4">
      <div className="hidden md:grid table-grid text-sm text-neutral-400 font-semibold px-2">
        <span>Customer Name</span>
        <span>Purchase Date</span>
        <span>Purchase Channel</span>
        <span className="text-right">Actions</span>
      </div>
      <div className="space-y-3">
        {items.map((item) => (
          <div
            key={item.id}
            className="table-grid bg-neutral-900/60 border border-neutral-800 rounded-xl px-4 py-3"
            aria-label={`Manual paper for ${item.customerName}`}
          >
            <div>
              <p className="font-semibold">{item.customerName}</p>
              <p className="text-sm text-neutral-400 md:hidden">{format(item.purchaseDate)}</p>
              <p className="text-sm text-neutral-400 md:hidden">{item.purchaseChannel}</p>
            </div>
            <div className="hidden md:block text-neutral-300">{format(item.purchaseDate)}</div>
            <div className="hidden md:block text-neutral-300">{item.purchaseChannel}</div>
            <div className="flex md:justify-end gap-2">
              <button
                onClick={() => onPreview(item)}
                className="px-3 py-2 rounded-lg bg-neutral-800 border border-neutral-700 text-sm hover:bg-neutral-700 focus:outline-none focus:ring-2 focus:ring-neutral-500"
              >
                Preview PDF
              </button>
              <button
                onClick={() => onDownload(item)}
                className="px-3 py-2 rounded-lg bg-white text-neutral-900 text-sm font-semibold hover:bg-neutral-200 focus:outline-none focus:ring-2 focus:ring-neutral-700"
              >
                Download PDF
              </button>
            </div>
          </div>
        ))}
        {items.length === 0 && (
          <div className="text-center text-neutral-500 py-6">No manual papers generated yet. Add a new one to get started.</div>
        )}
      </div>
    </div>
  )
}
