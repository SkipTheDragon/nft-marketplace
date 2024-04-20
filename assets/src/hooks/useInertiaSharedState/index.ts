import {usePage} from "@inertiajs/react";
import {SharedState} from "./shared-state.type.ts";
import {SharedStateMapping} from "./shared-state-mapping.type.ts";

/**
 * Get the global props for the given namespace. The global props must be defined inside a EvenListener in the backend.
 * @param namespace
 */
export default function useInertiaSharedState<T extends SharedState>(namespace: T): SharedStateMapping[T] {
    const propsForNamespace = usePage().props['GLOBALS::' + namespace];

    if (!propsForNamespace) {
        throw new Error(`Global namespace ${namespace} is not defined in the page props.`);
    }

    return propsForNamespace as any;
}
