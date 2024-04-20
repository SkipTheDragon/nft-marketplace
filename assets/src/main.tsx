import {createInertiaApp} from '@inertiajs/react'
import {createRoot} from 'react-dom/client'
import Wrapper from "./Wrapper";

createInertiaApp({
    resolve: name => {
        const pages = import.meta.glob('./controllers/**/*.tsx', {eager: true})
        let page = pages[`./controllers/${name}.tsx`];
        page.default.layout = page.default.layout || (page => <Wrapper children={page}/>)
        return page
    },
    setup({el, App, props}) {
        console.log(props.initialPage.props)
        createRoot(el).render(<App {...props}/>)
    },
})
