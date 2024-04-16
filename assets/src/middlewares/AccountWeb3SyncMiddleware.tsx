import {useActiveWallet} from "thirdweb/react";
import toast from "react-hot-toast";
import {router} from "@inertiajs/react";
import Routing from "fos-router";
import {useEffect, useState} from "react";
import {onDisconnect} from "../utils/auth/onDisconnect.ts";
import axios from "axios";

export default function AccountWeb3SyncMiddleware({isUserConnected, children}) {
    const wallet = useActiveWallet();
    const [justConnected, setJustConnected] = useState(true); // TODO: Use global state
    const [timeoutDisabled, setTimeoutDisabled] = useState(false);

    useEffect(() => {
        if (!wallet || timeoutDisabled) {
            return;
        }

        const interval = setTimeout(() => {
            setJustConnected(false);
        }, 6000);

        wallet.subscribe('onConnect', () => {
            console.log("connected")
            setJustConnected(true);
            setTimeoutDisabled(true)
            clearTimeout(interval)
        });

    }, [isUserConnected, timeoutDisabled, wallet]);

    if (wallet && !isUserConnected) {
        wallet.disconnect().then(r => {
            toast.error('We have detected that your session expired so we disconnected your wallet. Please reconnect to continue.');
            setTimeout(() => {
                window.location.reload();
            }, 1000)
        });
    }

    if (isUserConnected && !wallet) {
        axios.post(Routing.generate('app_auth_disconnect')).then(() => {
            toast.error('We have detected that your session expired so we disconnected your wallet. Please reconnect to continue.');
            setTimeout(() => {
                window.location.reload();
            }, 1000)
        });
    }

    return children;
}
