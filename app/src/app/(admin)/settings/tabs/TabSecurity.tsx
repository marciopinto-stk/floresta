"use client";

import { api } from "@/lib/api";
import { useSensritStatus } from "@/providers/SensritProvider";
import { Modal } from "@/shared/layout/Modals";
import { Icon } from "@iconify/react";
import { useState } from "react";

type TokenUpdateResponse = {
  success: boolean;
  message?: string;
};

const PLUG_CLASSES_BY_STATUS: Record<string, string> = {
  valid: "text-blue-600",
  invalid: "text-red-600",
  error: "text-red-600",
  checking: "text-yellow-600",
  default: "text-gray-600",
};

export default function TabSecurity() {
  const { status, refresh } = useSensritStatus();
  const [modalOpen, setModalOpen] = useState(false);
  const [token, setToken] = useState("");
  const [localError, setLocalError] = useState<string>("");

  function openModal() {
    setLocalError("");
    setToken("");
    setModalOpen(true);
  }

  async function handleSaveToken() {
    const cleaned = token.trim();

    if (!cleaned) {
      setLocalError("Informe o token.");
      return;
    }

    setLocalError("");

    try {
      const saved = await api.post<TokenUpdateResponse>("/sensrit/token", {
        token: cleaned,
      });

      if (saved?.success === false) {
        setLocalError(saved.message || "Não foi possível salvar o token.");
        return;
      }

      const { status: newStatus } = await refresh();

      if (newStatus == "valid") {
        setModalOpen(false);
      }
    } catch (e) {
      setLocalError("Erro ao enviar/validar token. Tente novamente.");
    }
  }

  const plugColorClass =
    PLUG_CLASSES_BY_STATUS[status] || PLUG_CLASSES_BY_STATUS["default"];

  return (
    <div id="tab-security">
      <div className="grid grid-cols-12 gap-6 items-stretch">
        <div className="lg:col-span-8 col-span-12">
          <div className="card h-full">
            <div className="p-8">
              <h5 className="card-title">Integrações</h5>
              <div className="sm:flex gap-4 mt-3 mb-7">
                <p className="sm:mb-0 mb-3">
                  Autenticação com sistemas terceiros
                </p>
              </div>
              <ul className="mt-4 flex flex-col">
                <li className="flex items-center justify-between border-t border-border dark:border-darkborder border-gray-300 py-4">
                  <div>
                    <h5 className="text-base">Token Sensrit</h5>
                    <p>
                      Token para integração com Sensrit. Necessário para
                      integração com os tickets de chamados
                    </p>
                  </div>

                  {status === "invalid" && localError && (
                    <p className="mt-2 text-sm text-red-600">{localError}</p>
                  )}

                  <button
                    type="button"
                    className="btn btn-primary w-fit cursor-pointer"
                    onClick={openModal}
                  >
                    Setup
                  </button>
                </li>
              </ul>
            </div>
          </div>
        </div>

        <div className="lg:col-span-4 col-span-12">
          <div className="card h-full">
            <div className="p-8">
              <div className="flex items-center gap-4">
                <div className="h-12 w-12 min-w-12 rounded-md bg-[var(--color-lightgray)] dark:bg-[var(--color-lightgray)] flex items-center justify-center">
                  <Icon
                    icon={"tabler:http-connect"}
                    className="text-primary text-2xl"
                  />
                </div>
                <h5 className="card-title m-0">Status</h5>
              </div>

              <div className="flex items-center justify-between mt-8">
                <div className="flex items-center gap-4">
                  <Icon
                    icon={"tabler:plug-connected"}
                    className={`text-2xl ${plugColorClass}`}
                  />
                  <div>
                    <h5 className="text-base font-semibold text-[var(--color-dark)]">
                      Sensrit
                    </h5>
                    <p>
                      {status === "valid"
                        ? "Conectado"
                        : status === "invalid"
                          ? "Token inválido"
                          : status === "checking"
                            ? "Validando..."
                            : status === "error"
                              ? "Falha ao verificar"
                              : "Não configurado"}
                    </p>
                  </div>
                </div>
              </div>
              <hr className="border-t border-gray-300 dark:border-darkborder my-3" />
            </div>
          </div>
        </div>
      </div>

      {/* Modal */}
      <Modal
        open={modalOpen}
        onClose={() => setModalOpen(false)}
        title="Setup Token Sensrit"
        widthClass="max-w-md"
      >
        <p className="card-subtitle mb-4">
          Cole seu token abaixo. Vamos validar no servidor antes de salvar.
        </p>

        <label className="font-semibold mb-2 block">Token</label>

        <input
          type="text"
          className="form-control"
          value={token}
          onChange={(e) => setToken(e.target.value)}
          placeholder="ex: sk_live_..."
        />

        {localError && (
          <p className="mt-3 text-sm text-red-600">{localError}</p>
        )}

        <div className="mt-6 flex justify-end gap-3">
          <button className="btn" onClick={() => setModalOpen(false)}>
            Cancelar
          </button>

          <button
            className="btn btn-primary cursor-pointer disabled:cursor-not-allowed disabled:opacity-50"
            onClick={handleSaveToken}
            disabled={status === "checking"}
          >
            {status === "checking" ? "Validando..." : "Salvar"}
          </button>
        </div>
      </Modal>
    </div>
  );
}
