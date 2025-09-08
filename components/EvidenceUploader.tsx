'use client';

import { useState, useCallback, useEffect } from 'react';
import Image from 'next/image';
import { useDropzone } from 'react-dropzone';
import { saveAs } from 'file-saver';
import {
    Document, Packer, Paragraph, TextRun, ImageRun, AlignmentType,
    Table, TableRow, TableCell, SectionType, TabStopType, IImageOptions,
    Header, // Import Header untuk membuat header dokumen
} from 'docx';

// Tipe untuk file yang diunggah
interface UploadedFile {
    file: File;
    preview: string;
    caption: string;
}

// --- Helper Functions for DOCX Generation ---

// FUNGSI BARU: Khusus untuk membuat konten header (logo)
const createDocHeader = async () => {
    const fetchImage = (path: string) => fetch(path).then(r => r.arrayBuffer());
    const [leftLogo, rightLogo] = await Promise.all([
        fetchImage("/telkom-aks.png"),
        fetchImage("/telkom-indo.png"),
    ]);

    return new Header({
        children: [
            new Paragraph({
                tabStops: [{ type: TabStopType.LEFT, position: 800 }, { type: TabStopType.RIGHT, position: 9000 }],
                children: [
                    new ImageRun({ data: leftLogo, transformation: { width: 150, height: 50 } } as IImageOptions),
                    new TextRun({ text: "\t" }),
                    new ImageRun({ data: rightLogo, transformation: { width: 115, height: 70 } } as IImageOptions),
                ],
                spacing: { after: 525 }
            })
        ],
    });
};

// FUNGSI DIMODIFIKASI: Hanya untuk membuat konten di bawah header (judul, detail proyek)
const createPageContentHeader = (lokasi: string) => {
    const projectDetails = [
        "PROYEK\t: PENGADAAAN PEKERJAAN OUTSIDE PLANT FIBER TO THE HOME (OSP - FTTH)",
        "\tTAHUN 2025 TELKOM REGIONAL IV KALIMANTAN",
        "KONTRAK\t: ",
        "AREA\t: BANJARMASIN",
        "LOKASI\t: " + lokasi,
        "PELAKSANA\t: PT. TELKOM AKSES"
    ];

    return [
        new Paragraph({ // Title
            alignment: AlignmentType.CENTER,
            spacing: { after: 350 },
            children: [new TextRun({ text: "EVIDENCE PEKERJAAN", bold: true, size: 30 })]
        }),
        ...projectDetails.map((text, idx) => new Paragraph({ // Project Details
            children: [new TextRun({ text, size: 18, bold: true })],
            tabStops: [{ type: TabStopType.LEFT, position: 1900 + (idx === 1 ? 100 : 0) }],
            spacing: { after: idx === 5 ? 400 : 120 }
        }))
    ];
};

const tableCellGenerator = async (dataArray: UploadedFile[]): Promise<TableCell[]> => {
    return Promise.all(dataArray.map(async (data) => {
        const buffer = await data.file.arrayBuffer();
        const maxWidth = 220;
        const maxHeight = 240;

        return new TableCell({
            children: [new Paragraph({
                alignment: AlignmentType.CENTER,
                children: [
                    new ImageRun({
                        data: buffer,
                        transformation: { width: maxWidth, height: maxHeight }
                    } as IImageOptions)
                ]
            })],
            margins: { top: 200, bottom: 200 },
        });
    }));
};

const captionCellGenerator = (dataArray: UploadedFile[]): TableCell[] => {
    return dataArray.map(data => new TableCell({
        children: [new Paragraph({ alignment: AlignmentType.CENTER, children: [new TextRun({ text: data.caption, size: 18 })] })]
    }));
};


// --- Komponen Utama ---

export default function EvidenceUploader() {
    const [uploadedFiles, setUploadedFiles] = useState<UploadedFile[]>([]);
    const [isLoading, setIsLoading] = useState(false);

    const onDrop = useCallback((acceptedFiles: File[]) => {
        const newFiles = acceptedFiles.map(file => ({
            file,
            preview: URL.createObjectURL(file),
            caption: '',
        }));
        setUploadedFiles(prev => [...prev, ...newFiles]);
    }, []);

    useEffect(() => {
        return () => uploadedFiles.forEach(file => URL.revokeObjectURL(file.preview));
    }, [uploadedFiles]);

    const updateCaption = (fileName: string, caption: string) => {
        setUploadedFiles(prev =>
            prev.map(f => (f.file.name === fileName ? { ...f, caption } : f))
        );
    };

    const removeFile = (fileName: string) => {
        setUploadedFiles(prev => prev.filter(f => f.file.name !== fileName));
    };

    const { getRootProps, getInputProps, isDragActive } = useDropzone({
        onDrop,
        accept: { 'image/*': [] },
    });

    const generateWord = async () => {
        if (uploadedFiles.length === 0) {
            alert("Silakan unggah minimal 1 foto.");
            return;
        }
        const lokasiInput = prompt("Masukkan Lokasi Pekerjaan:");
        if (!lokasiInput) {
            alert("Lokasi wajib diisi.");
            return;
        }
        setIsLoading(true);

        try {
            // PERUBAHAN UTAMA: Membuat header sekali saja
            const docHeader = await createDocHeader();

            const sections = [];
            const perPage = 6;
            for (let i = 0; i < Math.ceil(uploadedFiles.length / perPage); i++) {
                const pageFiles = uploadedFiles.slice(i * perPage, (i + 1) * perPage);
                const rows = [];
                for (let j = 0; j < pageFiles.length; j += 3) {
                    const rowFiles = pageFiles.slice(j, j + 3);

                    const imageCells = await tableCellGenerator(rowFiles);
                    const captionCells = captionCellGenerator(rowFiles);

                    rows.push(new TableRow({ children: imageCells }));
                    rows.push(new TableRow({ children: captionCells }));
                }

                // PERUBAHAN UTAMA: Menambahkan konten dan tabel ke dalam children section
                sections.push({
                    // Header akan diterapkan ke section ini
                    properties: { type: SectionType.NEXT_PAGE },
                    children: [
                        ...createPageContentHeader(lokasiInput), // Detail proyek
                        new Table({ rows, width: { size: 100, type: "pct" } }), // Tabel gambar
                    ],
                });
            }

            const doc = new Document({
                // PERUBAHAN UTAMA: Mendefinisikan header untuk semua section
                sections: sections.map(section => ({
                    ...section,
                    headers: {
                        default: docHeader,
                    }
                })),
                styles: { default: { document: { run: { font: "Arial" }, paragraph: { spacing: { line: 276 } } } } }
            });

            const blob = await Packer.toBlob(doc);
            saveAs(blob, `Evidence_Pekerjaan_${lokasiInput.replace(/\s/g, '_')}.docx`);

            setUploadedFiles([]);
        } catch (error) {
            console.error("Gagal membuat dokumen:", error);
            alert("Terjadi kesalahan saat membuat dokumen Word.");
        } finally {
            setIsLoading(false);
        }
    };

    return (
        <div className="bg-white/5 rounded-2xl p-6 sm:p-10 w-full max-w-5xl shadow-2xl shadow-black/60 border border-white/10 transition-transform duration-300 hover:-translate-y-1">
            <h2 className="text-3xl sm:text-4xl mb-6 text-center font-bold tracking-wider bg-gradient-to-r from-sky-400 via-purple-500 to-pink-500 bg-clip-text text-transparent">
                Upload Evidence Pekerjaan
            </h2>

            <div
                {...getRootProps()}
                className={`border-2 border-dashed rounded-2xl p-12 text-center transition-all duration-300 cursor-pointer 
          ${isDragActive ? 'border-sky-400 bg-white/10' : 'border-white/25 bg-white/5 hover:bg-white/10 hover:border-sky-400'}`}
            >
                <input {...getInputProps()} />
                <p className="font-medium text-slate-300">Klik atau seret gambar ke area ini untuk mengunggah</p>
            </div>

            {uploadedFiles.length > 0 && (
                <aside className="mt-8 grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-6">
                    {uploadedFiles.map((item) => (
                        <div key={item.file.name} className="bg-white/10 rounded-xl border border-white/10 p-4 shadow-lg shadow-black/40 flex flex-col items-center transition-transform duration-200 hover:-translate-y-1.5">
                            <div className="relative w-full h-40 rounded-lg shadow-md shadow-black/30 overflow-hidden">
                                <Image
                                    src={item.preview}
                                    alt={item.file.name}
                                    fill
                                    style={{ objectFit: 'cover' }}
                                    sizes="(max-width: 640px) 100vw, (max-width: 1024px) 50vw, 33vw"
                                />
                            </div>
                            <input
                                type="text"
                                placeholder="Tambahkan deskripsi"
                                value={item.caption}
                                onChange={(e) => updateCaption(item.file.name, e.target.value)}
                                className="w-full p-2.5 mt-3 border-none rounded-lg bg-white/90 text-center text-sm text-slate-900 font-medium outline-none transition-all duration-300 focus:ring-2 focus:ring-sky-400 focus:bg-white"
                            />
                            <button
                                type="button"
                                onClick={() => removeFile(item.file.name)}
                                className="w-full mt-2.5 py-2 px-4 border-none rounded-lg bg-gradient-to-r from-cyan-400 to-blue-500 text-white font-semibold cursor-pointer transition-all duration-300 hover:bg-gradient-to-r hover:from-red-500 hover:to-red-800 hover:scale-105"
                            >
                                Hapus
                            </button>
                        </div>
                    ))}
                </aside>
            )}

            <div className="text-center">
                <button
                    id="generateBtn"
                    onClick={generateWord}
                    disabled={isLoading || uploadedFiles.length === 0}
                    className="mt-8 py-3.5 px-12 text-base border-none rounded-full bg-gradient-to-r from-sky-400 via-indigo-400 to-purple-500 text-white font-bold cursor-pointer shadow-lg shadow-black/40 transition-all duration-300 block mx-auto hover:-translate-y-0.5 hover:scale-105 hover:shadow-xl hover:shadow-black/60 disabled:opacity-50 disabled:cursor-not-allowed disabled:transform-none"
                >
                    {isLoading ? 'Generating...' : 'Generate Word'}
                </button>
            </div>
        </div>
    );
}