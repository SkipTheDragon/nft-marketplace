import {useActiveWallet} from "thirdweb/react";
import toast from "react-hot-toast";
import Routing from "fos-router";
import axios from "axios";
import useInertiaSharedState from "../hooks/useInertiaSharedState";
import {SharedState} from "../hooks/useInertiaSharedState/shared-state.type.ts";
import {GlobalStoreState, useGlobalStore} from "../stores/useGlobalStore.ts";
import {useEffect} from "react";

/**
 * This middleware is responsible for syncing the wallet and the user session.
 * @param children
 * @constructor
 */
export default function AccountWeb3SyncMiddleware({children}) {
    const wallet = useActiveWallet();
    const currentAuthState = useInertiaSharedState(SharedState.AUTH);
    const isUserConnected = currentAuthState.user !== null;
    const justConnected = useGlobalStore((state: GlobalStoreState) => state.justConnected);

    useEffect(() => {
        if (justConnected) {
            return;
        }

        const id = setTimeout(() => {
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
        }, 30000) // The user has 30 seconds to reconnect

        return () => clearTimeout(id);
    }, [wallet, isUserConnected, justConnected])

    return children
}
