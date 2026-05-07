// Bunny Edge Script entrypoint for Shifa
// - Exports a `fetch` handler required by Bunny Edge Scripting
// - Optionally proxy requests to a backend by setting an `ORIGIN` binding

export default {
  async fetch(request, env, ctx) {
    try {
      // If you set an `ORIGIN` environment binding in Bunny (e.g. https://api.example.com),
      // the Edge script will proxy requests to that origin.
      if (env && env.ORIGIN) {
        const incoming = new URL(request.url);
        const target = new URL(env.ORIGIN);
        target.pathname = incoming.pathname;
        target.search = incoming.search;

        const resp = await fetch(target.toString(), {
          method: request.method,
          headers: request.headers,
          body: ["GET", "HEAD"].includes(request.method) ? undefined : request.body,
          redirect: 'manual',
        });

        const headers = new Headers(resp.headers);
        headers.delete('connection');
        headers.delete('transfer-encoding');

        return new Response(resp.body, { status: resp.status, headers });
      }
    } catch (err) {
      // If proxying fails, fall through to a simple response so the script still deploys.
      // Note: Bunny's logging/console may vary; use the dashboard for logs.
    }

    return new Response('Hello from Shifa Edge Script — deployed!', {
      headers: { 'Content-Type': 'text/plain; charset=utf-8' },
    });
  },
};
