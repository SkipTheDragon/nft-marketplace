import {ThirdwebProvider} from "thirdweb/react";
import './index.css';

export default function Wrapper({children}) {
    return (
        <ThirdwebProvider>
            {children}
        </ThirdwebProvider>
    )
}

