"use client";

import PageHeader from "@/shared/layout/Headers/PageHeader";
import { Icon } from "@iconify/react";
import { useMemo, useState } from "react";
import TabAccount from "./tabs/TabAccount";

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
            {active === "notificacoes" && <TabNotificacoes />}
            {active === "seguranca" && <TabSeguranca />}
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

/* ---------------- Tabs ---------------- */

function Section({
  title,
  subtitle,
  children,
}: {
  title: string;
  subtitle?: string;
  children: React.ReactNode;
}) {
  return (
    <section className="card border border-gray-400/20">
      <div className="card-body">
        <div className="mb-4">
          <h2 className="text-base font-semibold text-dark">{title}</h2>
          {subtitle && <p className="text-sm text-slate-500">{subtitle}</p>}
        </div>

        {children}

        {/* Footer actions */}
        <div className="mt-6 flex items-center justify-end gap-3">
          <button type="button" className="btn-outline-primary">
            Cancelar
          </button>
          <button type="button" className="btn">
            Salvar
          </button>
        </div>
      </div>
    </section>
  );
}

function TabPerfil() {
  return (
    <div className="space-y-6">
      <Section
        title="Foto do Perfil"
        subtitle="Altere sua foto de perfil aqui."
      >
        <div className="flex flex-col gap-4 sm:flex-row sm:items-center">
          <div className="h-16 w-16 rounded-full bg-primary/10" />
          <div className="flex gap-3">
            <button type="button" className="btn">
              Upload
            </button>
            <button type="button" className="btn-outline-primary">
              Remover
            </button>
          </div>
        </div>
        <p className="mt-3 text-xs text-slate-500">
          PNG/JPG/GIF. Tamanho máximo recomendado: 800KB.
        </p>
      </Section>

      <Section
        title="Dados Pessoais"
        subtitle="Edite seus dados e salve as alterações."
      >
        <div className="grid grid-cols-1 gap-4 md:grid-cols-2">
          <Field label="Nome" placeholder="Seu nome" />
          <Field label="E-mail" placeholder="seu@email.com" type="email" />
          <Field label="Telefone" placeholder="(85) 9xxxx-xxxx" />
          <Field label="Cidade" placeholder="Fortaleza" />
          <div className="md:col-span-2">
            <Field label="Endereço" placeholder="Rua, número, bairro..." />
          </div>
        </div>
      </Section>
    </div>
  );
}

function TabNotificacoes() {
  return (
    <div className="space-y-6">
      <Section
        title="Preferências de Notificação"
        subtitle="Selecione quais notificações você deseja receber."
      >
        <div className="space-y-3">
          <ToggleRow
            title="Newsletter"
            description="Avisos sobre mudanças importantes e novidades."
          />
          <ToggleRow
            title="Alertas de Integração"
            description="Erros e avisos ao comunicar com APIs externas."
          />
          <ToggleRow
            title="Segurança"
            description="Login suspeito, troca de senha e atividades críticas."
          />
        </div>
      </Section>
    </div>
  );
}

function TabSeguranca() {
  return (
    <div className="space-y-6">
      <Section
        title="Alterar Senha"
        subtitle="Informe sua senha atual e a nova senha."
      >
        <div className="grid grid-cols-1 gap-4 md:grid-cols-3">
          <Field label="Senha atual" type="password" />
          <Field label="Nova senha" type="password" />
          <Field label="Confirmar nova senha" type="password" />
        </div>
      </Section>

      <Section
        title="Sessões e Dispositivos"
        subtitle="Gerencie sessões ativas e desconecte dispositivos."
      >
        <div className="space-y-3">
          <DeviceRow device="Chrome / Windows" meta="Fortaleza, agora" />
          <DeviceRow device="Safari / iPhone" meta="Fortaleza, ontem" />
          <div className="pt-2">
            <button type="button" className="btn-outline-primary">
              Sair de todos os dispositivos
            </button>
          </div>
        </div>
      </Section>
    </div>
  );
}

/* ---------------- UI bits ---------------- */

function Field({
  label,
  placeholder,
  type = "text",
}: {
  label: string;
  placeholder?: string;
  type?: string;
}) {
  return (
    <label className="block">
      <span className="mb-1 block text-sm font-medium text-dark">{label}</span>
      <input
        type={type}
        placeholder={placeholder}
        className="w-full rounded-md border border-gray-400/20 bg-white px-3 py-2 text-sm outline-none focus:border-primary"
      />
    </label>
  );
}

function ToggleRow({
  title,
  description,
}: {
  title: string;
  description: string;
}) {
  return (
    <div className="flex items-start justify-between gap-4 rounded-md border border-gray-400/20 p-4">
      <div>
        <p className="text-sm font-semibold text-dark">{title}</p>
        <p className="text-xs text-slate-500">{description}</p>
      </div>

      {/* Toggle simples (estático por enquanto) */}
      <button
        type="button"
        className="h-6 w-11 rounded-full bg-primary/20 relative"
        aria-label={`Alternar ${title}`}
      >
        <span className="absolute left-1 top-1 h-4 w-4 rounded-full bg-white shadow-sm" />
      </button>
    </div>
  );
}

function DeviceRow({ device, meta }: { device: string; meta: string }) {
  return (
    <div className="flex items-center justify-between rounded-md border border-gray-400/20 p-4">
      <div>
        <p className="text-sm font-semibold text-dark">{device}</p>
        <p className="text-xs text-slate-500">{meta}</p>
      </div>

      <button type="button" className="btn-outline-primary">
        Encerrar
      </button>
    </div>
  );
}
