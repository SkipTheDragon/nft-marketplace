import {
    ThirdwebProvider,
} from "thirdweb/react";
import './index.css';
import Navbar from "./components/NavBar";
import {Toaster} from "react-hot-toast";
import {thirdwebClient} from "./services/thirdwebClient.ts";
import {ThemeProvider} from "@material-tailwind/react";
import {StrictMode} from "react";
import AccountWeb3SyncMiddleware from "./middlewares/AccountWeb3SyncMiddleware.tsx";

export default function Wrapper({children}) {
    return (
        <StrictMode>
            <ThemeProvider>
                <ThirdwebProvider
                    clientId={thirdwebClient.clientId}
                >
                    <AccountWeb3SyncMiddleware >
                        <Navbar/>

                        {children}

                        <Toaster/>
                    </AccountWeb3SyncMiddleware>
                </ThirdwebProvider>
            </ThemeProvider>
        </StrictMode>
    )
}

