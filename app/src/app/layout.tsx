import { Metadata } from "next";
import "./globals.css";

export const metadata: Metadata = {
  title: "Floresta",
  description: "Floresta Dashboard"
}

export default function RootLayout({
  children,
}: Readonly<{
  children: React.ReactNode;
}>) {
  return (
    <html lang="pt-BR">
      <head>
        <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700&display=swap" rel="stylesheet" />
      </head>
      <body className="min-h-dvh bg-[color:var(--color-info)]/5 font-sans">

        <main>
          <div id="main-wrapper" className="min-h-dvh w-full">
            {children}
          </div>
        </main>
      </body>
    </html>
  );
}
