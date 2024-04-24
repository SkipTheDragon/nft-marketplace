import {Logger, ViteDevServer} from "vite";
import { execFileSync } from "node:child_process";
import * as fs from "node:fs";
import * as path from "node:path";
import process from "node:process";

/**
 * Transforms an object into a shell argument.
 * Example: {key: 'value'} => '--key=value'
 * Values don't need to be escaped because node already does that.
 * @param key
 * @param value
 */
function shellArg(key: string, value: any): string {
    key = kebabize(key);
    return typeof value === "boolean" ? (value ? "--" + key : "") : "--" + key + "=" + value;
}

/**
 * Transforms a camelCase string into a kebab-case string.
 * @param str
 */
function kebabize(str: string): string {
    return str
        .split("")
        .map((letter, idx) => {
            return letter.toUpperCase() === letter ? `${idx !== 0 ? "-" : ""}${letter.toLowerCase()}` : letter;
        })
        .join("");
}

/**
 * Transforms an object into an array of shell arguments.
 * Example: {key: 'value'} => ['--key=value']
 * This function is recursive.
 * @param obj
 */
export const objectToArg = (obj: object): string[] => {
    return Object.keys(obj).reduce((pass, key) => {
        const val = obj[key];
        if (!val) {
            return pass;
        }
        if (key === "extraArgs" && typeof val === "object") {
            pass.push(...objectToArg(val));
            return pass;
        }

        if (Array.isArray(val)) {
            pass.push(...val.map((v) => shellArg(key, v)));
        } else {
            pass.push(shellArg(key, val));
        }
        return pass;
    }, []);
};


/**
 * @default ./fos-routing/index.ts
 */
export type VitePluginSymfonyFosRoutingOptions = {
    /**
     * Arguments to pass to the fos:js-routing:dump command
     */
    args?: {
        /**
         * You can check the available options by running `php bin/console fos:js-routing:dump --help`
         * The options below should be pretty self-explanatory.
         */
        target?: string;
        format?: string | "json" | "js";
        locale?: string;
        prettyPrint?: boolean;
        domain?: string[];
        /**
         * Extra arguments to pass to the command, in case the bundle gets updated and the vite plugin does not.
         * This way you won't have to wait for the vite plugin to be updated.
         */
        extraArgs?: object;
    };
    /**
     * File types to check for injecting the route data.
     * By default, we will inject the route data in js, jsx, ts, tsx and vue files.
     */
    transformCheckFileTypes?: RegExp;
    /**
     * A list of files to check for changes. When a file in this array changes, the plugin will dump the routes and
     * eventually if there are new routes we will initiate a full reload in hmr.
     */
    watchPaths?: string[];
    /**
     * The command to run to dump the routes. Default to php`
     */
    php?: string;
    /**
     * If true, the plugin will output errors and information to the console.
     */
    verbose?: boolean;
};


/**
 * Vite plugin to generate fos routes and inject them into the code.
 * Adapted from the original Webpack plugin made by FOS.
 * @author Tudorache Leonard Valentin <tudorache.leonard@wyverr.com>
 * @param pluginOptions
 * @param logger
 */
export default function fosRoutingPlugin(pluginOptions?: VitePluginSymfonyFosRoutingOptions) {
    let shouldInject = true; // Control when to inject
    let prevContent = null; // Previous content of the routes

    /**
     * Default plugin options.
     */
    const defaultPluginOptions = {
        args: {
            target: "var/cache/fosRoutes.json",
            format: "json",
            locale: "",
            prettyPrint: false,
            domain: [],
            extraArgs: {},
        },
        transformCheckFileTypes: /\.(js|jsx|ts|tsx|vue)$/,
        watchPaths: ["src/**/*.php"],
        verbose: false,
        php: "php",
    };

    /**
     * Merges the default options with the user options.
     */
    const finalPluginOptions: VitePluginSymfonyFosRoutingOptions = {
        ...defaultPluginOptions,
        ...pluginOptions,
    };

    /**
     * Resolves the target path.
     */
    const finalTarget = path.resolve(process.cwd(), finalPluginOptions.args.target);

    /**
     * Resolve the target path to a temporary file.
     */
    finalPluginOptions.args.target = path.resolve(
        process.cwd(),
        finalPluginOptions.args.target.replace(/\.json$/, ".tmp.json"),
    );

    /**
     * Prevents the target from being the same as the final target.
     */
    if (finalPluginOptions.args.target === finalTarget) {
        finalPluginOptions.args.target += ".tmp";
    }

    /**
     * Resolved target path.
     */
    const target = finalPluginOptions.args.target;

    function runDumpRoutesCmd() {
        if (finalPluginOptions.verbose) {
            console.log("Generating fos routes...");
        }
        const args = objectToArg(finalPluginOptions.args);

        // Dump routes
        execFileSync(finalPluginOptions.php, ["bin/console", "fos:js-routing:dump", ...args], {
            stdio: finalPluginOptions.verbose ? "inherit" : undefined,
        });
    }

    /**
     * Runs the command to generate the fos routes.
     * Also checks if the routes have changed and saves them to a file.
     * Then sets shouldInject to true if the routes have changed.
     */
    function runCmd() {
        shouldInject = false;

        try {
            runDumpRoutesCmd();
            const content = fs.readFileSync(target);
            if (fs.existsSync(target)) {
                fs.rmSync(target); // Remove the temporary file
            }
            // Check if there are new routes
            if (!prevContent || content.compare(prevContent) !== 0) {
                fs.mkdirSync(path.dirname(finalTarget), { recursive: true });
                fs.writeFileSync(finalTarget, content);
                prevContent = content;
                shouldInject = true;
            }
        } catch (err) {
            // logger.error(err.toString());
        }
        return [];
    }

    return {
        name: "rollup-plugin-symfony-fos-routing",
        /**
         * Runs the command on build start.
         */
        buildStart() {
            runCmd();
        },
        /**
         * Configures the server to watch for changes.
         * When a change is detected, the routes are dumped and the code is reloaded if the routes file content is changed.
         */
        configureServer(devServer: ViteDevServer) {
            const { watcher, ws } = devServer;
            const paths = [...finalPluginOptions.watchPaths, target];
            for (const path of paths) {
                watcher.add(path);
            }
            watcher.on("change", function (path) {
                /**
                 * Dump the routes if a php file is changed.
                 */
                if (path.endsWith('.php')) {
                    runDumpRoutesCmd();
                }

                /**
                 * Reload the code if the routes file content is changed.
                 */
                if (target === path) {
                    if (finalPluginOptions.verbose) {
                        console.log("We detected a change in the routes file. Reloading...");
                    }
                    ws.send({
                        type: "full-reload",
                    });
                } else if(path.endsWith('.php') && finalPluginOptions.verbose) {
                    console.log("No change in the routes file.");
                }
            });
        },


        /**
         * Injects the routes into the code.
         * @param code
         * @param id
         */
        async transform(code, id) {
            // Inject if shouldInject is true and the file is matched by the transformCheckFileTypes regex.
            if (shouldInject && defaultPluginOptions.transformCheckFileTypes.test(id)) {
                if (finalPluginOptions.verbose) {
                    console.log(`Injecting routes in ${id}...`);
                }
                return {
                    code: code.replace(
                        /import\s+\w+\s+from\s+(?:"(?:fos-router|symfony-ts-router)"|'(?:fos-router|symfony-ts-router)')\s*;?/,
                        `
            import Routing from "fos-router";
            import routes from ${JSON.stringify(finalTarget)};
            Routing.setRoutingData(routes);
            `,
                    ),
                    map: null,
                };
            }

            return {
                code,
                map: null,
            };
        },
    };
}
