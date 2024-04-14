const withMT = require("@material-tailwind/react/utils/withMT");


/** @type {import('tailwindcss').Config} */
export default withMT({
  content: [
    "./assets/src/**/*.{js,ts,jsx,tsx}",
    "./templates/**/*.twig"
  ],
  theme: {
    extend: {},
  },
  plugins: [],
})
