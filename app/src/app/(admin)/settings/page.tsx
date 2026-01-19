"use client";

import PageHeader from "@/shared/layout/Headers/PageHeader";
import { Icon } from "@iconify/react";
import { useMemo, useState } from "react";
import TabAccount from "./tabs/TabAccount";
import TabSecurity from "./tabs/TabSecurity";
import TabNotfications from "./tabs/TabNotifications";

type TabKey = "perfil" | "notificacoes" | "seguranca";

export default function AccountSettingsPage() {
  const tabs = useMemo(
    () =>
      [
        { key: "perfil" as const, label: "Perfil" },
        { key: "notificacoes" as const, label: "Notificações" },
        { key: "seguranca" as const, label: "Segurança" },
      ] satisfies Array<{ key: TabKey; label: string }>,
    []
  );

  const [active, setActive] = useState<TabKey>("perfil");

  return (
    <div className="w-full items-stretch">
      {/* Page header */}
      <PageHeader 
        title="Configurações"
        subtitle="Gerencie preferências do seu perfil, notificações e segurança."
      />

      {/* Tabs container */}
      <div className="card h-full">
        <nav className="flex gap-8">
          {tabs.map((t) => {
            const isActive = active === t.key;

            return (
              <button
                key={t.key}
                type="button"
                onClick={() => setActive(t.key)}
                className={[
                  "flex items-center gap-2 py-5 px-4 text-sm ",
                  "border-b-2 transition-colors ",
                  isActive
                    ? "border-primary text-primary"
                    : "border-transparent text-dark hover:text-primary",
                ].join(" ")}
              >
                <TabIcon tab={t.key} />
                <span className="md:flex hidden">{t.label}</span>
              </button>
            );
          })}
        </nav>
        
        <div className="p-8">
          {/* Tabs */}
          <div className="mt-6 space-y-6">
            {active === "perfil" && <TabAccount />}
            {active === "notificacoes" && <TabNotfications />}
            {active === "seguranca" && <TabSecurity />}
          </div>
        </div>
      </div>
    </div>
  );
}

// Icones das Tabs
function TabIcon({ tab }: { tab: TabKey }) {
  switch (tab) {
    case "perfil":
      return <Icon icon="solar:user-linear" className="text-lg" />;
    case "notificacoes":
      return <Icon icon="solar:bell-linear" className="text-lg" />;
    case "seguranca":
      return <Icon icon="solar:shield-check-linear" className="text-lg" />;
    default:
      return null;
  }
}
