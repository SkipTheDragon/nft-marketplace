import axios from "axios";
import Routing from "fos-router";
import {router} from "@inertiajs/react";
import toast from "react-hot-toast";

export function onDisconnect() {

    axios.post(Routing.generate('app_auth_disconnect')).then(() => {
        toast.success('Disconnected successfully');
        router.visit(Routing.generate('app_auth_connect'));
    })

}
