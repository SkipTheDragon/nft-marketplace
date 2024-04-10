import { createInertiaApp } from '@inertiajs/react'
import { createRoot } from 'react-dom/client'

createInertiaApp({
    resolve: name => {
        return require(`./controllers/${name}.tsx`);
    },
    setup({ el, App, props }) {
        createRoot(el).render(<App {...props} />)
    },
})
