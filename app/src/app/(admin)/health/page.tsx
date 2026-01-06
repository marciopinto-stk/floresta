"use client"

import { api } from "@/lib/api";
import { useEffect, useState } from "react";

type HealthResponse = {
  status: string;
  timestamp?: string;
};

export default function HealthPage() {
  const [data, setData]   = useState<HealthResponse| null>(null);
  const [error, setError] = useState<string | null>(null);

  useEffect(() => {
    api.get<HealthResponse>("/health")
      .then(setData)
      .catch((err) => setError(err.message));
  }, []);

  return (
    <div className="p-6">
      <h1 className="text-2x1 font-bold mb-4">Health da API</h1>
      {error && <p className="text-red-500">Erro: {error}</p>}
      {data ? (
        <pre className="bg-slate-900 text-slate-100 p-4 rounded">
          {JSON.stringify(data, null, 2)}
        </pre>
      ) : !error ? (
        <p>Carregando</p>
      ) : null}
    </div>
  );
}