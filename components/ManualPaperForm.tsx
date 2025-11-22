'use client'

import { FormEvent, useState } from 'react'
import { ManualPaperRecord } from '../types/manual-paper'

interface ManualPaperFormProps {
  onSubmit: (record: ManualPaperRecord) => void
  onCancel: () => void
}

export default function ManualPaperForm({ onSubmit, onCancel }: ManualPaperFormProps) {
  const [customerName, setCustomerName] = useState('')
  const [purchaseDate, setPurchaseDate] = useState('')
  const [purchaseChannel, setPurchaseChannel] = useState('Customer Service')
  const [error, setError] = useState('')

  const handleSubmit = (e: FormEvent<HTMLFormElement>) => {
    e.preventDefault()
    if (!customerName || !purchaseDate || !purchaseChannel) {
      setError('Please fill all required fields.')
      return
    }
    const record: ManualPaperRecord = {
      id: crypto.randomUUID(),
      customerName,
      purchaseDate,
      purchaseChannel
    }
    onSubmit(record)
    setCustomerName('')
    setPurchaseDate('')
    setPurchaseChannel('Customer Service')
    setError('')
  }

  return (
    <form onSubmit={handleSubmit} className="space-y-4">
      <div>
        <label className="block text-sm text-neutral-300 mb-1">Customer Name *</label>
        <input
          type="text"
          value={customerName}
          onChange={(e) => setCustomerName(e.target.value)}
          className="w-full rounded-lg bg-neutral-800 border border-neutral-700 px-3 py-2 focus:outline-none focus:ring-2 focus:ring-neutral-500"
          required
          placeholder="Enter customer name"
        />
      </div>
      <div>
        <label className="block text-sm text-neutral-300 mb-1">Purchase Date *</label>
        <input
          type="date"
          value={purchaseDate}
          onChange={(e) => setPurchaseDate(e.target.value)}
          className="w-full rounded-lg bg-neutral-800 border border-neutral-700 px-3 py-2 focus:outline-none focus:ring-2 focus:ring-neutral-500"
          required
        />
      </div>
      <div>
        <label className="block text-sm text-neutral-300 mb-1">Purchase Channel *</label>
        <select
          value={purchaseChannel}
          onChange={(e) => setPurchaseChannel(e.target.value)}
          className="w-full rounded-lg bg-neutral-800 border border-neutral-700 px-3 py-2 focus:outline-none focus:ring-2 focus:ring-neutral-500"
          required
        >
          <option>Customer Service</option>
          <option>Marketplace</option>
          <option>Website</option>
        </select>
      </div>
      {error && <p className="text-red-400 text-sm">{error}</p>}
      <div className="flex items-center justify-end gap-3 pt-2">
        <button
          type="button"
          onClick={onCancel}
          className="px-4 py-2 rounded-lg border border-neutral-700 text-neutral-200 hover:bg-neutral-800 focus:outline-none focus:ring-2 focus:ring-neutral-600"
        >
          Cancel
        </button>
        <button
          type="submit"
          className="px-4 py-2 rounded-lg bg-white text-neutral-900 font-semibold hover:bg-neutral-200 focus:outline-none focus:ring-2 focus:ring-neutral-700"
        >
          Save &amp; Generate
        </button>
      </div>
    </form>
  )
}
