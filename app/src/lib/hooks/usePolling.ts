"use client"

import { useEffect, useRef } from "react"

export function usePolling(fn: () => void | Promise<void>, intervalMs: number, enabled: boolean) {
  const fnRef = useRef(fn);
  fnRef.current = fn;

  useEffect(() => {
    if (!enabled) return;

    let timer: number | undefined;

    const tick = async () => {
      await fnRef.current();
      timer = window.setTimeout(tick, intervalMs);
    };

    tick();

    return () => {
      if (timer) window.clearTimeout(timer);
    };
  }, [intervalMs, enabled]);
}