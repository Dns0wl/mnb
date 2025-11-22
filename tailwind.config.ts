import type { Config } from 'tailwindcss'

const config: Config = {
  content: [
    './app/**/*.{js,ts,jsx,tsx,mdx}',
    './components/**/*.{js,ts,jsx,tsx,mdx}'
  ],
  theme: {
    extend: {
      fontFamily: {
        display: ['"Playfair Display"', 'serif'],
        body: ['Inter', 'sans-serif'],
        script: ['"Great Vibes"', 'cursive']
      },
      colors: {
        brand: {
          50: '#f4f4f2',
          100: '#e6e6e0',
          900: '#0c0c0a'
        }
      },
      boxShadow: {
        glow: '0 0 0 1px rgba(255,255,255,0.06), 0 10px 50px rgba(0,0,0,0.35)'
      },
      backgroundImage: {
        'leather-texture':
          'repeating-linear-gradient(45deg, rgba(0,0,0,0.03) 0, rgba(0,0,0,0.03) 2px, transparent 2px, transparent 6px), repeating-linear-gradient(-45deg, rgba(0,0,0,0.02) 0, rgba(0,0,0,0.02) 3px, transparent 3px, transparent 7px), linear-gradient(135deg, rgba(255,255,255,0.9), rgba(243,243,243,0.85))'
      }
    }
  },
  plugins: []
}
export default config
