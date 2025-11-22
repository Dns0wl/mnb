'use client'

import { useMemo, useRef, useState } from 'react'
import Modal from '../../components/Modal'
import ManualPaperForm from '../../components/ManualPaperForm'
import ManualPaperList from '../../components/ManualPaperList'
import ManualPaperTemplate from '../../components/ManualPaperTemplate'
import { ManualPaperRecord } from '../../types/manual-paper'
import { formatFileDate } from '../../lib/date'
import jsPDF from 'jspdf'
import html2canvas from 'html2canvas'

export default function ManualPaperPage() {
  const [items, setItems] = useState<ManualPaperRecord[]>([
    {
      id: '1',
      customerName: 'Ayu Pramesti',
      purchaseDate: '2024-06-20',
      purchaseChannel: 'Customer Service'
    },
    {
      id: '2',
      customerName: 'Budi Santoso',
      purchaseDate: '2024-05-15',
      purchaseChannel: 'Marketplace'
    }
  ])
  const [open, setOpen] = useState(false)
  const [preview, setPreview] = useState<ManualPaperRecord | null>(null)
  const captureRefs = useRef<Map<string, HTMLDivElement>>(new Map())

  const handleAdd = (record: ManualPaperRecord) => {
    setItems((prev) => [record, ...prev])
    setOpen(false)
  }

  const handlePreview = (record: ManualPaperRecord) => setPreview(record)

  const handleDownload = async (record: ManualPaperRecord) => {
    const node = captureRefs.current.get(record.id)
    if (!node) return
    const canvas = await html2canvas(node, { scale: 2 })
    const imgData = canvas.toDataURL('image/png')
    const pdf = new jsPDF({ format: 'a5', unit: 'mm', orientation: 'portrait' })
    const pageWidth = pdf.internal.pageSize.getWidth()
    const pageHeight = pdf.internal.pageSize.getHeight()
    pdf.addImage(imgData, 'PNG', 0, 0, pageWidth, pageHeight)
    const fileDate = formatFileDate(record.purchaseDate)
    pdf.save(`ManualPaper-${record.customerName}-${fileDate}.pdf`)
  }

  const hiddenTemplates = useMemo(
    () =>
      items.map((item) => (
        <div
          key={item.id}
          className="absolute opacity-0 pointer-events-none -z-10"
          style={{ top: 0, left: 0 }}
          ref={(node) => {
            if (node) captureRefs.current.set(item.id, node)
          }}
        >
          <ManualPaperTemplate {...item} />
        </div>
      )),
    [items]
  )

  return (
    <div className="space-y-8">
      <section className="flex flex-col md:flex-row md:items-center justify-between gap-4">
        <div>
          <h2 className="text-2xl font-semibold">Manual Paper Generator</h2>
          <p className="text-neutral-400 text-sm">Generate A5 customer manual paper PDF for Hayu Widyas.</p>
        </div>
        <button
          onClick={() => setOpen(true)}
          className="inline-flex items-center justify-center gap-2 px-4 py-2 rounded-lg bg-white text-neutral-900 font-semibold hover:bg-neutral-200 focus:outline-none focus:ring-2 focus:ring-neutral-700"
        >
          Add New Manual Paper
        </button>
      </section>

      <ManualPaperList items={items} onPreview={handlePreview} onDownload={handleDownload} />

      <Modal title="Manual Paper" open={open} onClose={() => setOpen(false)}>
        <ManualPaperForm onSubmit={handleAdd} onCancel={() => setOpen(false)} />
      </Modal>

      {preview && (
        <Modal title="Preview Manual Paper" open={!!preview} onClose={() => setPreview(null)}>
          <div className="flex justify-center">
            <ManualPaperTemplate {...preview} />
          </div>
        </Modal>
      )}

      <div aria-hidden className="fixed top-0 left-0">
        {hiddenTemplates}
      </div>
    </div>
  )
}
