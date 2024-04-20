import {thirdwebClient} from "../services/thirdwebClient";
import type {Wallet} from "thirdweb/src/wallets/interfaces/wallet";
import {ConnectButton, ConnectEmbed, useActiveWallet} from "thirdweb/react";
import {onConnect} from "../utils/auth/onConnect.ts";
import {createWallet, inAppWallet} from "thirdweb/wallets";
import {Button, Dialog, DialogBody, DialogFooter, DialogHeader, Tooltip} from "@material-tailwind/react";
import {useEffect, useState} from "react";
import {GlobalStoreState, useGlobalStore} from "../stores/useGlobalStore.ts";

export default function TwConnectButtonWrapper(
    {
        type = 'button'
    } :
    {
        type?: 'button' | 'embed'
    }
) {
    const [awaitSign, setAwaitSign] = useState(false);
    const [tryAgain, setTryAgain] = useState(false);
    const [secondsCooldown, setCooldown] = useState(3000);
    const wallet = useActiveWallet();
    const setJustConnected = useGlobalStore((state: GlobalStoreState) => state.setJustConnected);

    const wallets: Wallet[] = [
        createWallet("io.metamask"),
        createWallet("com.trustwallet.app"),
        createWallet("com.coinbase.wallet"),
        createWallet("walletConnect"),
        inAppWallet({
            auth: {
                options: [
                    "email",
                    "google",
                    "apple",
                    "facebook",
                ],
            },
        }),
    ];

    /**
     * Add a cooldown to the 'Try again' button
     */
    useEffect(() => {
        if (awaitSign) {
            const interval = setInterval(() => {
                if (secondsCooldown >= 1000) {
                    setCooldown(secondsCooldown - 1000);
                }

                if (secondsCooldown === 0) {
                    setTryAgain(true);
                }
            }, 1000);

            return () => clearInterval(interval);
        }

    }, [tryAgain, awaitSign, secondsCooldown])

    /**
     *  Dialog to show when the user is awaiting signature
     */
    const dialog = (
        <Dialog size="xs" open={awaitSign}>
            <DialogHeader>
                Awaiting signature
            </DialogHeader>
            <DialogBody>
                After signing the request, you will be connected to the NFT marketplace. If you are not prompted to
                sign, please press the 'Try again' button.
            </DialogBody>
            <DialogFooter>
                <Button variant="gradient" disabled={!tryAgain} onClick={() => {
                    onConnect(wallet, setAwaitSign, setJustConnected);
                    setCooldown(3000 + 2000);
                }}>
                    Try again {secondsCooldown > 0 && <>({secondsCooldown / 1000} seconds)</>}
                </Button>
            </DialogFooter>
        </Dialog>
    )

    if (type === 'embed') {
        return (
            <>
                <ConnectEmbed
                    client={thirdwebClient}
                    wallets={wallets}
                    theme={"light"}
                    className="m-auto"
                    detailsModal={{
                        hideDisconnect: true,
                    }}
                    connectButton={{
                        label: "Connect Wallet",
                    }}
                    connectModal={{
                        size: "wide",
                        welcomeScreen: {
                            title: "Sign in to NFT marketplace",
                            subtitle:
                                "Choose a sign in method from the left menu to get started.",
                        },
                        termsOfServiceUrl:
                            "http://nft-marketplace.lndo.site/auth/sign-in",
                        privacyPolicyUrl:
                            "http://nft-marketplace.lndo.site/auth/sign-in",
                    }}
                    onConnect={(e) => onConnect(e, setAwaitSign, setJustConnected)}
                    onDisconnect={() => {
                        console.log('out')
                    }}
                    showThirdwebBranding={false}
                />
                {dialog}
            </>
        )
    }

    return (
        <>
            <ConnectButton
                client={thirdwebClient}
                wallets={wallets}
                theme={"light"}
                detailsModal={{
                    hideDisconnect: true,
                }}
                connectButton={{
                    label: "Connect Wallet",
                }}
                modalSize="wide"
                connectModal={{
                    size: "wide",
                    welcomeScreen: {
                        title: "Sign in to NFT marketplace",
                        subtitle:
                            "Choose a sign in method from the left menu to get started.",
                    },
                    termsOfServiceUrl:
                        "http://nft-marketplace.lndo.site/auth/sign-in",
                    privacyPolicyUrl:
                        "http://nft-marketplace.lndo.site/auth/sign-in",
                }}
                onConnect={(e) => onConnect(e, setAwaitSign, setJustConnected)}
                showThirdwebBranding={false}
            />
            {dialog}
        </>
    )
}
