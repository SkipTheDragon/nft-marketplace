import {create} from 'zustand'
import {devtools, persist} from 'zustand/middleware'

export interface GlobalStoreState {
    justConnected: boolean
    setJustConnected: () => void
}

/**
 * Global store for the application.
 */
export const useGlobalStore = create<GlobalStoreState>()(
    devtools(
        (set) => ({
            justConnected: false,
            setJustConnected: () => set(() => ({justConnected: true})),
        }),
        {
            name: 'global-store',
        },
    ),
)

