/**
 * Shim for process.env.NODE_ENV in browser environments.
 * Required for libraries like nanostores.
 */
const appEnvMeta = document.querySelector('meta[name="app-env"]');
const nodeEnv = appEnvMeta ? appEnvMeta.content : "development";

window.process = {
  env: {
    NODE_ENV: nodeEnv,
  },
};
