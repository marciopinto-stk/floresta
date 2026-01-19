"use client"

import Image from "next/image";
import { useEffect, useMemo, useState } from "react";

interface Props {
  title: string;
  subtitle?: string;
}

const headerImages = [
  "/images/animals/fox.png",
  "/images/animals/dog.png",
  "/images/animals/rhino.png",
  "/images/animals/cat.png",
  "/images/animals/monkey.png",
  "/images/animals/bear.png",
]

export default function PageHeader({ title, subtitle }: Props) {
  const [image, setImage] = useState<string | null>("/images/animals/dog.png");

  useEffect(() => {
    const index = Math.floor(Math.random() * headerImages.length);
    setImage(headerImages[index]);
  }, []);

  return(
    <div className="card bg-[var(--color-lightinfo)] position-relative  mb-6">
      <div className="card-body p-8 md:py-3 py-5">
        <div className="items-center grid grid-cols-12 gap-6">
          <div className="col-span-9">
            <h4 className="font-semibold text-xl mb-3">{title}</h4>
            <p className="text-sm text-slate-500">
              {subtitle}
            </p>
          </div>
          <div className="hidden md:block col-span-3">
            <div className="flex justify-center">
              <Image 
                src={image}
                alt="header animal"
                width={120}
                height={120}
                priority
                className=" h-full"
              />
            </div>
          </div>     
        </div>
      </div>
  </div>
  );
}
