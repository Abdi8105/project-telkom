import type { Metadata } from "next";
import { Poppins } from "next/font/google";
import "./globals.css";

// Konfigurasi font Poppins sesuai dengan yang digunakan di CSS asli
const poppins = Poppins({
  subsets: ["latin"],
  weight: ["400", "500", "600", "700"],
});

export const metadata: Metadata = {
  title: "Evidence Pekerjaan",
  description: "Aplikasi untuk upload bukti pekerjaan dan generate dokumen Word.",
};

export default function RootLayout({
  children,
}: Readonly<{
  children: React.ReactNode;
}>) {
  return (
    <html lang="id">
      <body className={poppins.className}>{children}</body>
    </html>
  );
}