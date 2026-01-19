export function toYmd(d: Date): string {
  const y   = d.getFullYear();
  const m   = String(d.getMonth() + 1).padStart(2, "0");
  const day = String(d.getDate()).padStart(2, "0");
  
  return `${y}-${m}-${day}`;
}

export function addDays(date: Date, days: number): Date {
  const d = new Date(date);
  d.setDate(d.getDate() + days);
  
  return d;
}

export function diffDaysInclusive(from: string, to: string): number {
  const a   = new Date(from + "T00:00:00");
  const b   = new Date(to + "T00:00:00");
  const ms  = b.getTime() - a.getTime();
  
  return Math.floor(ms / 86400000) + 1;
}

export function last7DaysRange(): { from: string; to: string } {
  const today = new Date();
  const to    = toYmd(today);
  const from  = toYmd(addDays(today, -6));
  
  return { from, to };
}
