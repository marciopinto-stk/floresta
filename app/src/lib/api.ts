const API_URL = process.env.NEXT_PUBLIC_API_URL || "http://localhost:9000";

async function request<T>(path: string, options: RequestInit = {}): Promise<T> {
  const res = await fetch(`${API_URL}${path}`, {
    ...options,
    headers: {
      "Content-Type": "application/json",
      ...(options.headers ?? {})
    },
  });

  if (!res.ok) {
    throw new Error(`API error ${res.status}`);
  }

  return res.json() as Promise<T>;
}

export const api = {
  get: <T>(path: string) => request<T>(path),

  post: <T>(path:string, body: unknown) => request<T>(path, {
    method: "POST",
    body: JSON.stringify(body),
  }),

  put: <T>(path: string, body: unknown) => request<T>(path, {
    method: "PUT",
    body: JSON.stringify(body),
  }),

  delete: <T>(path: string) => request<T>(path, {method: "DELETE"})
};