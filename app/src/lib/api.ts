const API_URL = process.env.NEXT_PUBLIC_API_URL || "http://localhost:9000";

async function request<T>(path: string, options: RequestInit = {}): Promise<T> {
  const res = await fetch(`${API_URL}${path}`, {
    ...options,
    headers: {
       "Accept": "application/json",
      "Content-Type": "application/json",
      ...(options.headers ?? {})
    },
  });

  const contentType = res.headers.get("content-type") ?? "";
  const payload     = contentType.includes("application/json") ? await res.json().catch(() => null) : null;

  if (!res.ok) {
    const message = 
      payload?.message ||
      (typeof payload === "string" ? payload : null) ||
      `API error ${res.status}`;

      const err = new Error(message) as Error & { status?: number; data?: unknown };
      err.status = res.status;
      err.data = payload;
      throw err;
  }

  if (res.status === 204) return null as T;

  return (payload ?? (await res.json())) as T;
}

async function requestForm<T>(path:string, options: RequestInit = {}): Promise<T> {
  const res = await fetch(`${API_URL}${path}`, {
    ...options,
    headers: {
       "Accept": "application/json",
      ...(options.headers ?? {}),
    },
  });

  const contentType = res.headers.get("content-type") ?? "";
  const payload = contentType.includes("application/json") ? await res.json().catch(() => null) : null;

  if (!res.ok) {
    const message = 
      payload?.message ||
      (typeof payload === "string" ? payload : null) ||
      `API error ${res.status}`;

      const err = new Error(message) as Error & { status?: number; data?: unknown };
      err.status = res.status;
      err.data = payload;
      throw err;
  }

  if (res.status === 204) return null as T;
  return (payload ?? (await res.json())) as T;
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

  delete: <T>(path: string) => request<T>(path, {method: "DELETE"}),

  postForm: <T>(path: string, formData: FormData) =>
    requestForm<T>(path, {
      method: "POST",
      body: formData,
    }),
};