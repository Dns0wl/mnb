# Hayu Widyas Office Dashboard

Internal dashboard for **dash.hayuwidyas.com** built with Next.js 14, TypeScript, and Tailwind CSS. The first module, **Manual Paper**, lets admins generate branded A5 PDF manual papers for customers with preview and download actions.

## Features
- Dashboard shell with sidebar navigation and topbar.
- Manual Paper list with customer name, purchase date, purchase channel, and actions.
- Add New Manual Paper modal with validation for required fields.
- Responsive, CSS-driven A5 Manual Paper template (no image assets) with branded layout.
- Client-side PDF generation using html2canvas + jsPDF with automatic file naming.

## Getting Started
1. Install dependencies:
   ```bash
   npm install
   ```
2. Run the development server:
   ```bash
   npm run dev
   ```
3. Open [http://localhost:3000](http://localhost:3000) to view the dashboard.

## Manual Paper Flow
1. Visit `/manual-paper` (default route) to see existing records.
2. Click **Add New Manual Paper**, fill in the form, then **Save & Generate** to add it to the list.
3. Use **Preview PDF** to open a modal with the A5 template rendered in-browser.
4. Use **Download PDF** to export an A5 portrait PDF named `ManualPaper-{CustomerName}-{YYYYMMDD}.pdf`.

## Tech Stack
- Next.js 14 (App Router)
- TypeScript & React 18
- Tailwind CSS
- html2canvas + jsPDF for PDF output
