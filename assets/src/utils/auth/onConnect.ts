import type {Wallet} from "thirdweb/src/wallets/interfaces/wallet";
import axios from "axios";
import Routing from "fos-router";
import toast from "react-hot-toast";

export async function onConnect(t: Wallet, setAwaitSign: (awaitSign: boolean) => void, setJustConnected: () => void){
    // Get the nonce and sign the message:123
    const account = t.getAccount();
    const chain = t.getChain();

    if (!account || !chain) {
        return;
    }

    setJustConnected();

    /**
     * @see src/TransferObject/ConnectDto.php
     */
    const payload: {
        chainId: number;
        address: string;
        type: string;
        signature: string | undefined;
    } = {
        chainId: chain.id,
        address: account.address,
        type: t.id,
        signature: undefined,
    };

    const message = await axios.get(Routing.generate('app_auth_message_to_sign', payload))

    setAwaitSign(true);

    payload.signature = await account.signMessage({
        message: message.data.message
    });

    axios
        .post(Routing.generate('app_auth_connect_post'), payload)
        .then(() => {
            toast.success("Connected successfully! Refreshing...");
            setTimeout(() => window.location.replace(Routing.generate('app_home')), 1000)
        })
        .catch(error => {
            toast.error("Failed to connect!");
            t.disconnect();
            setTimeout(() => window.location.reload(), 1000)
        })
        .finally(() => {
            setAwaitSign(false);
        })
}
