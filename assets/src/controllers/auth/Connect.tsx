import {Typography} from "@material-tailwind/react";
import TwConnectButtonWrapper from "../../components/TwConnectButtonWrapper.tsx";

export default function Connect() {
    return (
        <section className="grid text-center h-screen items-center p-8">
            <div className="w-auto m-auto p-5">
                <Typography variant="h3" color="blue-gray" className="mb-2">
                    Connect to AppName
                </Typography>

                <Typography className="text-gray-600 font-normal text-[18px]">
                    One step away from accessing the best NFT marketplace.
                </Typography>

                <div className="mt-12">
                    <TwConnectButtonWrapper type="embed"/>
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
