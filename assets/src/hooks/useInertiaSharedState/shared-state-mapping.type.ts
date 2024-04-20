import {AuthType} from "./types/auth.type.ts";
import {SharedState} from "./shared-state.type.ts";

export interface SharedStateMapping {
    [SharedState.AUTH]: AuthType;
}
