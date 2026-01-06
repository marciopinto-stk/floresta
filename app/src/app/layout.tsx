"use client"

import "./globals.css";

export default function RootLayout({
  children,
}: Readonly<{
  children: React.ReactNode;
}>) {
  return (
    <html lang="en">
      <head>
        <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700&display=swap" rel="stylesheet" />
      </head>
      <body className="bg-info/5">

        <main>
          <div id="main-wrapper" className="flex ">
            {children}
          </div>
        </main>
      </body>
    </html>
  );
}
