/** @type {import('tailwindcss').Config} */
export default {
  content: [
    "./resources/**/*.blade.php",
    "./resources/**/*.js",
    "./resources/**/*.vue",
    "./public/js/**/*.js",
  ],
  theme: {
    extend: {
      fontFamily: {
          sans: ['Plus Jakarta Sans', 'sans-serif'],
          display: ['Sora', 'sans-serif'],
      },
      colors: {
          primary: '#0F766E',
          'primary-light': '#10B981',
          teal: { deep: '#0f766e' },
          orange: '#f97316',
      },
      borderRadius: { '2xl': '16px', '3xl': '24px' },
      keyframes: {
          fadeUp: {
              from: { opacity: '0', transform: 'translateY(16px)' },
              to: { opacity: '1', transform: 'translateY(0)' },
          },
      },
      animation: {
          fadeUp: 'fadeUp 0.6s ease both',
          'fadeUp-delay-1': 'fadeUp 0.6s 0.1s ease both',
          'fadeUp-delay-2': 'fadeUp 0.6s 0.2s ease both',
          'fadeUp-delay-3': 'fadeUp 0.6s 0.3s ease both',
      },
      boxShadow: {
          'teal-md': '0 6px 18px rgba(15,118,110,0.22)',
          'teal-lg': '0 10px 25px rgba(15,118,110,0.25)',
          'teal-xl': '0 15px 30px rgba(15,118,110,0.35)',
          'green-sm': '0 4px 12px rgba(16,185,129,0.3)',
          'red-sm': '0 4px 12px rgba(239,68,68,0.3)',
      },
    },
  },
  plugins: [],
}
