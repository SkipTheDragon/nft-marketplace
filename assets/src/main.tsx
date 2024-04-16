import { createInertiaApp } from '@inertiajs/react'
import { createRoot } from 'react-dom/client'
import Wrapper from "./Wrapper";

createInertiaApp({
    resolve: name => {
        const pages = import.meta.glob('./controllers/**/*.tsx', { eager: true })
        return pages[`./controllers/${name}.tsx`];
    },
    setup({ el, App, props }) {
        createRoot(el).render(<Wrapper isUserConnected={props.initialPage.props.auth.user !== null}><App {...props}/></Wrapper>)
    },
})
