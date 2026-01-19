"use client";

import { faHtml5 } from "@fortawesome/free-brands-svg-icons";
import { Icon } from "@iconify/react";
import { ReactNode, useEffect } from "react";

interface ModalProps {
  open: boolean;
  onClose: () => void;
  title?: string;
  children: ReactNode;
  widthClass?: string;
}

export default function Modal({
  open,
  onClose,
  title,
  children,
  widthClass = "max-w-lg",
}: ModalProps) {
  useEffect(() => {
    if (!open) return;

    function onKeyDown(e: KeyboardEvent) {
      if (e.key === "Escape") onClose();
    }

    document.addEventListener("keydown", onKeyDown);
    return () => document.removeEventListener("keydown", onKeyDown);
  }, [open, onClose]);

  if (!open) return null;

  return (

    <div className="size-full fixed top-0 start-0 z-[60] overflow-x-hidden overflow-y-auto bg-gray-900/50">
      {/* Overlay */}
      <div className="mt-7 duration-500 mt-0 ease-out transition-all sm:max-w-lg sm:w-full m-3 sm:mx-auto min-h-[calc(100%-3.5rem)] flex items-center">
        <div className="card ">
          {/* Modal */}
          <div className="flex justify-between items-center py-3 px-4 rounded-t-sm border-b-slate-950 bg-slate-200">
            <h3 className="font-bold text-base text-gray-800">{title}</h3>
            <button
              type="button"
              onClick={onClose}
              aria-label="Fechar modal"
              className="flex justify-center items-center size-7 text-sm font-semibold rounded-full border border-transparent text-gray-800 hover:bg-gray-100 disabled:opacity-50 disabled:pointer-events-none"
            >
              <Icon icon="tabler:x" className="text-xl text-slate-600" />
            </button>
          </div>
          <div className="p-4 overflow-y-auto">
            <div className="mt-1 text-base text-gray-800">{children}</div>
          </div>
        </div>
      </div>
    </div>
  );
}