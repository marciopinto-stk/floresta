"use client"

import Image from "next/image";
import { useEffect, useRef, useState } from "react";

export default function TabAccount()
{
  const avatars = [
    "/images/avatar/bear.png",
    "/images/avatar/cat.png",
    "/images/avatar/elephant.png",
    "/images/avatar/fox.png",
    "/images/avatar/horse.png",
    "/images/avatar/lion.png",
    "/images/avatar/rabbit.png",
    "/images/avatar/tiger.png",
    "/images/avatar/unicorn.png",
  ];

  const [avatarOpen, setAvatarOpen]         = useState(false);
  const [selectedAvatar, setSelectedAvatar] = useState<string>(avatars[0]);
  const menuRef                             = useRef<HTMLDivElement | null>(null);

  const [currentPassword, setCurrentPassword] = useState("");
  const [newPassword, setNewPassword]         = useState("");
  const [confirmPassword, setConfirmPassword] = useState("");

  useEffect(() => {
    function onMouseDown(e: MouseEvent) {
      if (!avatarOpen) return;
      if (menuRef.current && !menuRef.current.contains(e.target as Node)) {
        setAvatarOpen(false);
      }
    }

    function onKeyDown(e: KeyboardEvent) {
      if (!avatarOpen) return;
      if (e.key === "Escape") setAvatarOpen(false);
    }

    document.addEventListener("mousedown", onMouseDown);
    document.addEventListener("keydown", onKeyDown);

    return () => {
      document.removeEventListener("mousedown", onMouseDown);
      document.removeEventListener("keydown", onKeyDown);
    };
  }, [avatarOpen]);

  function handleSelectAvatar(src: string) {
    setSelectedAvatar(src);
    setAvatarOpen(false);
  }

  return (
    <div id="tab-account">
      <div className="grid grid-cols-12 gap-6 items-stretch">
        <div className="lg:col-span-6 col-span-12">
          <div className="card h-full">
            <div className="p-8">
              <h5 className="card-title">Perfil</h5>
              <p className="card-subtitle">Altere sua imagem de perfil aqui!</p>
              <div className="text-center">
                <div className="rounded-full h-[150px] w-[150px] overflow-hidden bg-[#D0DBE1] mx-auto mt-6 ">
                  <Image
                    className=" mx-auto mt-6 "
                    src={selectedAvatar}
                    alt="Avatar"
                    width={120}
                    height={120}
                    priority
                  />
                </div>
                <div className="relative flex gap-3 justify-center my-6 mb-20">
                  <button
                    type="button"
                    className="btn btn-primary"
                    aria-haspopup="menu"
                    aria-expanded={avatarOpen}
                    onClick={() => setAvatarOpen((v) => !v)}
                  >
                    Alterar
                  </button>

                  <p className="card-subtitle absolute top-full mt-3">Selecione aqui seu avatar</p>

                  <div
                    ref={menuRef}
                    role="menu"
                    className={[
                      "absolute top-full z-50 w-[80%] border border-black/10 bg-white shadow-[var(--shadow-md)] mt-2",
                      avatarOpen ? "block" : "hidden",
                    ].join(" ")}
                  >
                    <ul className="grid md:grid-cols-6 grid-cols-3 gap-3 p-4 ">
                      {avatars.map((src) => {
                          const isActive = src === selectedAvatar;

                          return (
                            <li key={src}>
                              <button
                                type="button"
                                role="menuitem"
                                onClick={() => handleSelectAvatar(src)}
                                className={[
                                  "rounded-full h-[52px] w-[52px] overflow-hidden bg-[#D0DBE1] flex items-center justify-center",
                                  "outline-none ring-offset-2 focus:ring-2",
                                  isActive
                                    ? "ring-2 ring-[var(--color-primary)]"
                                    : "hover:ring-2 hover:ring-black/10",
                                ].join(" ")}
                                aria-label="Selecionar avatar"
                              >
                                <Image
                                  src={src}
                                  alt="Avatar opção"
                                  width={44}
                                  height={44}
                                  className="h-[44px] w-[44px] object-contain"
                                />
                              </button>
                            </li>
                          );
                        })}
                    </ul>
                  </div>
                </div>
              </div>
            </div>
          </div>
        </div>

        <div className="lg:col-span-6 col-span-12">
          <div className="card h-full">
            <div className="p-8">
              <h5 className="card-title">Alterar senha</h5>
              
              <p className="card-subtitle">Para alterar sua senha confirme aqui</p>

              <form action="#" className="mt-6">
                <div className="flex flex-col gap-4">
                  <div>
                    <label className="text-dark dark:text-darklink font-semibold mb-2 block ">Senha atual</label>
                    <input type="password" className="form-control py-2" value={currentPassword} onChange={(e) => setCurrentPassword(e.target.value)} />
                  </div>
                  <div>
                    <label className="text-dark dark:text-darklink font-semibold mb-2 block ">Nova senha</label>
                    <input type="password" className="form-control py-2" value={newPassword} onChange={(e) => setNewPassword(e.target.value)} />
                  </div>
                  <div>
                    <label className="text-dark dark:text-darklink font-semibold mb-2 block ">Confirma senha</label>
                    <input type="password" className="form-control py-2" value={confirmPassword} onChange={(e) => setConfirmPassword(e.target.value)} />
                  </div>
                </div>
              </form>
            </div>
          </div>
        </div>
      </div>
    </div>
  );
}