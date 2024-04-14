import {ConnectButton,} from "thirdweb/react";
import {createWallet, inAppWallet,} from "thirdweb/wallets";
import {thirdwebClient} from "../../services/thirdwebClient";
import {Alert, Typography} from "@material-tailwind/react";
import Routing from 'fos-router'
import axios from "axios";
import type {Wallet} from "thirdweb/src/wallets/interfaces/wallet";
import {ReactNode, useState} from "react";

const accountAbstraction = {
    chain: '',
    factoryAddress: "YOUR_FACTORY_ADDRESS",
    gasless: true,
};

enum Status {
    WAITING,
    LOADING,
    SUCCESS,
    ERROR,
    AWAITING_SIGNATURE,
}

export default function Connect() {
    const [alert, setAlert] = useState<ReactNode | null>(null);
    const [status, setStatus] = useState<Status>(Status.WAITING);

    const onConnect = async (t: Wallet) => {
        // Get the nonce and sign the message:123
        const account = t.getAccount();
        const chain = t.getChain();

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

        payload.signature = await account.signMessage({
            message: message.data.message
        });

        axios
            .post(Routing.generate('app_auth_connect_post'), payload)
            .then(response => {
                setAlert(
                    <Alert
                        icon={
                            <svg
                                xmlns="http://www.w3.org/2000/svg"
                                viewBox="0 0 24 24"
                                fill="currentColor"
                                className="h-6 w-6"
                            >
                                <path
                                    fillRule="evenodd"
                                    d="M2.25 12c0-5.385 4.365-9.75 9.75-9.75s9.75 4.365 9.75 9.75-4.365 9.75-9.75 9.75S2.25 17.385 2.25 12zm13.36-1.814a.75.75 0 10-1.22-.872l-3.236 4.53L9.53 12.22a.75.75 0 00-1.06 1.06l2.25 2.25a.75.75 0 001.14-.094l3.75-5.25z"
                                    clipRule="evenodd"
                                />
                            </svg>
                        }
                        className="rounded-none border-l-4 border-[#2ec946] bg-[#2ec946]/10 font-medium text-[#2ec946]"
                    >
                        You have successfully signed in. We will redirect you to the dashboard.
                    </Alert>
                )
            })
            .catch(error => {
                t.disconnect();
                setAlert(
                    <Alert
                        icon={
                            <svg
                                xmlns="http://www.w3.org/2000/svg"
                                viewBox="0 0 24 24"
                                fill="currentColor"
                                className="h-6 w-6"
                            >
                                <path
                                    fillRule="evenodd"
                                    d="M12 2C6.486 2 2 6.486 2 12s4.486 10 10 10 10-4.486 10-10S17.514 2 12 2zm0 16a1 1 0 01-1-1v-6a1 1 0 012 0v6a1 1 0 01-1 1zm0-8a1 1 0 00-1 1v5a1 1 0 002 0v-5a1 1 0 00-1-1z"
                                    clipRule="evenodd"
                                />
                            </svg>
                        }
                        className="rounded-none border-l-4 border-[#f36] bg-[#f36]/10 font-medium text-[#f36]"
                    >
                        {error.response.data.message}
                    </Alert>
                )
            })
    }

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

    return (
        <section className="grid text-center h-screen items-center p-8">
            <div className="w-auto m-auto p-5">
                <Typography variant="h3" color="blue-gray" className="mb-2">
                    Connect to AppName
                </Typography>
                <Typography className="text-gray-600 font-normal text-[18px]">
                    Press the button below and sign the message to sign in.
                </Typography>

                {
                    alert && <div className="my-4">{alert}</div>
                }

                <div className="mt-12">
                    <ConnectButton
                        client={thirdwebClient}
                        wallets={wallets}
                        // accountAbstraction={accountAbstraction}
                        theme={"dark"}
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
                        onConnect={onConnect}
                        showThirdwebBranding={false}
                    />
                </div>
                <Typography
                    variant="small"
                    color="gray"
                    className="mt-4 text-center font-normal"
                >
                    What is a wallet?{" "}
                    <a href="https://www.coinbase.com/learn/crypto-basics/what-is-a-crypto-wallet" target="_blank"
                       className="font-medium text-gray-900">
                        Learn more here
                    </a>
                </Typography>
            </div>
        </section>
    );
}
