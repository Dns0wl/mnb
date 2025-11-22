import { ReactNode } from 'react'

interface ModalProps {
  title: string
  open: boolean
  onClose: () => void
  children: ReactNode
}

export default function Modal({ title, open, onClose, children }: ModalProps) {
  if (!open) return null
  return (
    <div className="modal-backdrop" role="dialog" aria-modal="true" aria-label={title}>
      <div className="modal-panel" onClick={(e) => e.stopPropagation()}>
        <div className="flex items-start justify-between gap-4 mb-4">
          <div>
            <p className="text-xs uppercase tracking-[0.2em] text-neutral-500">{title}</p>
            <h2 className="text-xl font-semibold">Add details</h2>
          </div>
          <button
            type="button"
            aria-label="Close dialog"
            onClick={onClose}
            className="text-neutral-400 hover:text-white rounded-lg p-2 focus:outline-none focus:ring-2 focus:ring-neutral-600"
          >
            ✕
          </button>
        </div>
        {children}
      </div>
    </div>
  )
}
