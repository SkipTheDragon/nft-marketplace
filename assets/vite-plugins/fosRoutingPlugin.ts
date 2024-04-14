import path from "node:path";
import {execFileSync} from "node:child_process";
import fs from "node:fs";

/**
 * Transforms an object into a shell argument.
 * Example: {key: 'value'} => '--key=value'
 * Values don't need to be escaped because node already does that.
 * @param key
 * @param value
 */
function shellArg(key: string, value: any): string {
    key = kebabize(key);
    return typeof value === 'boolean' ? (value ? '--' + key : '') : '--' + key + '=' + value;
}

/**
 * Transforms a camelCase string into a kebab-case string.
 * @param str
 */
function kebabize(str: string): string {
    return str.split('').map((letter, idx) => {
        return letter.toUpperCase() === letter
            ? `${idx !== 0 ? '-' : ''}${letter.toLowerCase()}`
            : letter;
    }).join('');
}

interface VitePluginSymfonyFosRoutingOptions {
    args?: {
        target?: string
        format?: string | 'json' | 'js',
        locale?: string
        prettyPrint?: boolean
        domain?: string[]
        extraArgs?: object
    },
    transformCheckFileTypes?: RegExp
    php?: string
    output?: boolean
}

/**
 * Transforms an object into an array of shell arguments.
 * Example: {key: 'value'} => ['--key=value']
 * This function is recursive.
 * @param obj
 */
const objectToArg = (obj: object): string[] => {
    return Object.keys(obj).reduce((pass, key) => {
        const val = obj[key];
        if (!val) {
            return pass;
        }
        if (key === 'extraArgs' && typeof val === 'object') {
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
}

export function fosRoutingPlugin(pluginOptions?: VitePluginSymfonyFosRoutingOptions) {
    let prevContent = null;

    const defaultPluginOptions = {
        args: {
            target: 'var/cache/fosRoutes.json',
            format: 'json',
            locale: '',
            prettyPrint: false,
            domain: [],
            extraArgs: {}
        },
        transformCheckFileTypes: /\.(js|jsx|ts|tsx|vue)$/,
        output: false,
        php: 'php',
    }

    const finalPluginOptions: VitePluginSymfonyFosRoutingOptions = {
        ...defaultPluginOptions,
        ...pluginOptions
    }

    const finalTarget = path.resolve(process.cwd(), finalPluginOptions.args.target);
    finalPluginOptions.args.target = path.resolve(process.cwd(), finalPluginOptions.args.target.replace(/\.json$/, '.tmp.json'));

    if (finalPluginOptions.args.target === finalTarget) {
        finalPluginOptions.args.target += '.tmp';
    }

    const target = finalPluginOptions.args.target;

    /**
     * Runs the command to generate the fos routes.
     * Also checks if the routes have changed and saves them to a file.
     * Then sets shouldInject to true if the routes have changed.
     */
    async function runCmd(shouldLoad: boolean = false) {
        if (finalPluginOptions.output) {
            console.log('Generating fos routes...')
        }

        try {
            const args = objectToArg(finalPluginOptions.args);

            // Dump routes
            await execFileSync(finalPluginOptions.php, ['bin/console', 'fos:js-routing:dump', ...args], {
                stdio: finalPluginOptions.output ? 'inherit' : undefined
            });

            const content = await fs.readFileSync(target);
            if (fs.existsSync(target)) {
                await fs.rmSync(target); // Remove the temporary file
            }
            // Check if there are new routes
            if (!prevContent || content.compare(prevContent) !== 0) {
                fs.mkdirSync(path.dirname(finalTarget), {recursive: true});
                await fs.writeFileSync(finalTarget, content);
                prevContent = content;
                if (shouldLoad) {
                    this.load();
                }
            }

        } catch (err) {
            console.error(err);
        }
        return []
    }

    return {
        name: 'rollup-plugin-fos-routing',
        async buildStart() {
            await runCmd();
        },
        async handleHotUpdate() {
            await runCmd(true);
        },
        async transform(code, id) {
            /**
             * Injects the routes into the code.
             */
            // Inject if shouldInject is true and the file is a JavaScript file
            if (defaultPluginOptions.transformCheckFileTypes.test(id)) {
                return {
                    code: code.replace(
                        /import\s+Routing\s+from\s+"fos-router"\s*;/,
                        `
                        import Routing from "fos-router";
                        import routes from ${JSON.stringify(finalTarget)};
                        Routing.setRoutingData(routes);
                      `),
                    map: null
                };
            }

            return {
                code,
                map: null
            };
        },

    };
}
