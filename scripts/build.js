const esbuild = require("esbuild");
const path = require("path");

const isWatch = process.argv.includes("--watch");

const buildOptions = {
  entryPoints: {
    bookings: "src/js/user/bookings/bookingsApp.js",
    client: "src/js/user/clientApp.js",
    admin: "src/js/admin/adminApp.js",
    auth: "src/js/auth/authApp.js",
  },
  bundle: true,
  outdir: "public/js/build",
  format: "esm", // Changed to ESM for better code splitting support if needed
  splitting: true, // Enable code splitting to share common chunks
  sourcemap: true,
  minify: !isWatch, // Minify only for production builds (default non-watch)
  target: ["es2020"],
  loader: { ".js": "jsx" }, // Enable JSX in .js files
  jsxFactory: "h",
  jsxFragment: "Fragment",
  logLevel: "info",
  define: {
    "process.env.NODE_ENV": isWatch ? '"development"' : '"production"',
  },
};

if (isWatch) {
  esbuild
    .context(buildOptions)
    .then((ctx) => {
      ctx.watch();
      console.log("Watching for changes...");
    })
    .catch(() => process.exit(1));
} else {
  esbuild.build(buildOptions).catch(() => process.exit(1));
}
