import {Fragment, useEffect, useState} from "react";
import {
    Navbar,
    Collapse,
    Typography,
    Button,
    IconButton, Tooltip,
} from "@material-tailwind/react";
import NavList from "./NavList";
import TwConnectButtonWrapper from "../TwConnectButtonWrapper.tsx";
import {FaBarsStaggered, FaPowerOff} from "react-icons/fa6";
import {BiPowerOff} from "react-icons/bi";
import {IconBase} from "react-icons";
import Icon from "../Icon.tsx";
import {FaBars} from "react-icons/fa";
import {Link, router} from "@inertiajs/react";
import Routing from "fos-router";
import useInertiaSharedState from "../../hooks/useInertiaSharedState";
import {SharedState} from "../../hooks/useInertiaSharedState/shared-state.type.ts";


export function NavbarWithSimpleLinks() {
    const [open, setOpen] = useState(false);
    const handleOpen = () => setOpen((cur) => !cur);
    const currentUser = useInertiaSharedState(SharedState.AUTH).user;
    useEffect(() => {
        window.addEventListener(
            "resize",
            () => window.innerWidth >= 960 && setOpen(false)
        );
    }, []);

    return (
        <Navbar color="transparent" fullWidth>
            <div className="container mx-auto flex items-center justify-between text-blue-gray-900">
                <Link href={Routing.generate('app_home')}>
                    <Typography
                        href="#"
                        color="blue-gray"
                        className="mr-4 cursor-pointer text-lg font-bold"
                    >
                        Material Tailwind
                    </Typography>
                </Link>
                <div className="hidden lg:block">
                    <NavList/>
                </div>

                <div className="hidden lg:inline-block">
                    <div className="flex items-center gap-4">
                        <TwConnectButtonWrapper/>
                        {
                            currentUser &&
                            <Tooltip placement="bottom" content="Disconnect">
                                <Icon as={BiPowerOff}
                                      onClick={() => router.visit(Routing.generate('app_auth_disconnect_confirmation'))}
                                      className="text-red-400 h-6 w-6 cursor-pointer"/>
                            </Tooltip>
                        }
                    </div>
                </div>
                <IconButton
                    size="sm"
                    variant="text"
                    color="blue-gray"
                    onClick={handleOpen}
                    className="ml-auto inline-block text-blue-gray-900 lg:hidden"
                >
                    {open ? (
                        <FaBarsStaggered className="h-6 w-6" strokeWidth={2}/>
                    ) : (
                        <FaBars className="h-6 w-6" strokeWidth={2}/>
                    )}
                </IconButton>
            </div>
            <Collapse open={open}>
                <div className="mt-2 rounded-xl bg-white py-2">
                    <NavList/>
                    <TwConnectButtonWrapper/>
                    {
                        currentUser &&
                        <Button className="my-4 bg-red-400"
                                onClick={() => router.visit(Routing.generate('app_auth_disconnect_confirmation'))}
                                fullWidth>
                            Disconnect
                        </Button>
                    }
                </div>
            </Collapse>
        </Navbar>
    );
}

export default NavbarWithSimpleLinks;
