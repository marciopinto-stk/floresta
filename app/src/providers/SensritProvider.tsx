"use client";

import { api } from "@/lib/api";
import { usePolling } from "@/lib/hooks/usePolling";
import { createContext, ReactNode, useContext, useMemo, useState } from "react";

type SensritStatus = "unknown" | "checking" | "valid" | "invalid" | "error";

type TokenValidateResponse = {
  hasToken: boolean;
  isValid: boolean;
  message?: string;
};

type SensritRefreshResult = {
  status: SensritStatus;
  message: string;
};

type SensritContextValue = {
  status: SensritStatus;
  message: string;
  lastCheckedAt: Date | null;
  refresh: () => Promise<SensritRefreshResult>;
};

const SensritContext = createContext<SensritContextValue | null>(null);

export function SensritProvider({
  children,
  intervalMs = 60_000,
  enabled = true,
}: {
  children: ReactNode;
  intervalMs: number;
  enabled?: boolean;
}) {
  const [status, setStatus] = useState<SensritStatus>("unknown");
  const [message, setMessage] = useState("");
  const [lastCheckedAt, setLastCheckedAt] = useState<Date | null>(null);

  const refresh = async (): Promise<SensritRefreshResult> => {
    setStatus("checking");

    try {
      const data = await api.get<TokenValidateResponse>(
        "/sensrit/token/validate",
      );

      const nextStatus: SensritStatus = data.hasToken
        ? data.isValid
          ? "valid"
          : "invalid"
        : "unknown";

      const nextMessage =
        nextStatus === "valid" || nextStatus === "unknown"
          ? ""
          : data.message || "Token inválido";

      setStatus(nextStatus);
      setMessage(nextMessage);
      setLastCheckedAt(new Date());

      return { status: nextStatus, message: nextMessage };
    } catch {
      const nextStatus: SensritStatus = "error";
      const nextMessage = "Não foi possível verificar o token agora.";

      setStatus(nextStatus);
      setMessage(nextMessage);
      setLastCheckedAt(new Date());

      return { status: nextStatus, message: nextMessage };
    }
  };

  usePolling<SensritRefreshResult>(refresh, intervalMs, enabled);

  const value = useMemo(
    () => ({ status, message, lastCheckedAt, refresh }),
    [status, message, lastCheckedAt],
  );

  return (
    <SensritContext.Provider value={value}>{children}</SensritContext.Provider>
  );
}

export function useSensritStatus() {
  const ctx = useContext(SensritContext);
  if (!ctx)
    throw new Error("useSensritStatus must be used within SensritProvider");
  return ctx;
}
