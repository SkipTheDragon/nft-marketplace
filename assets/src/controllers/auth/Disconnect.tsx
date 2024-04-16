import {Button, Typography} from "@material-tailwind/react";
import {onDisconnect} from "../../utils/auth/onDisconnect.ts";
import {useDisconnect, useActiveWallet} from "thirdweb/react";

export default function () {
    const {disconnect} = useDisconnect();
    const account = useActiveWallet();

    return (
        <section className="grid text-center h-screen items-center p-8">
            <div className="w-auto m-auto p-5">
                <Typography variant="h3" color="blue-gray" className="mb-2">
                    Are you sure you want to disconnect?
                </Typography>

                <Typography className="text-gray-600 font-normal text-[18px]">
                    Press the button below to disconnect your wallet and invalidate this session.
                </Typography>

                <div className="mt-12">
                    <Button className="bg-red-400"
                            onClick={() => {
                                onDisconnect()
                                disconnect(account);
                            }}
                    >Disconnect</Button>
                </div>

            </div>
        </section>
    )
}
