import {createThirdwebClient} from "thirdweb";

export const thirdwebClient = createThirdwebClient({
    clientId: window.config.THIRDWEB_CLIENT,
});
