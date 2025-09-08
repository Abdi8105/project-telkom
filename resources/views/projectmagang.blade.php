<!DOCTYPE html>
<html lang="id">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1" />
  <title>Evidence Pekerjaan</title>
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/dropzone/5.9.3/min/dropzone.min.css" />
  <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;600;700&display=swap" rel="stylesheet" />
  <style>
    * { box-sizing: border-box; margin: 0; padding: 0; }
    body {
      font-family: 'Poppins', sans-serif;
      background: linear-gradient(135deg, #0f172a, #1e1b4b, #0a0a0a);
      color: #e2e8f0;
      min-height: 100vh;
      display: flex;
      align-items: center;
      justify-content: center;
      padding: 30px;
    }
    h2 {
      font-size: 2.3rem;
      margin-bottom: 25px;
      text-align: center;
      font-weight: 700;
      background: linear-gradient(90deg, #38bdf8, #818cf8, #ec4899);
      -webkit-background-clip: text;
      -webkit-text-fill-color: transparent;
      animation: gradientMove 4s infinite alternate;
    }
    @keyframes gradientMove {
      from { background-position: 0%; }
      to { background-position: 100%; }
    }
    .container {
      background: rgba(255,255,255,0.06);
      border-radius: 24px;
      padding: 40px;
      width: 100%;
      max-width: 1000px;
      backdrop-filter: blur(20px);
      border: 1px solid rgba(255,255,255,0.1);
      box-shadow: 0 15px 50px rgba(0,0,0,0.6);
      animation: fadeIn 0.8s ease-out;
    }
    @keyframes fadeIn {
      from { opacity: 0; transform: translateY(20px); }
      to { opacity: 1; transform: translateY(0); }
    }
    .dropzone {
      border: 2px dashed rgba(255,255,255,0.25);
      background: rgba(255,255,255,0.03);
      border-radius: 18px;
      padding: 60px;
      text-align: center;
      transition: 0.4s;
      cursor: pointer;
    }
    .dropzone:hover {
      border-color: #38bdf8;
      background: rgba(255,255,255,0.06);
      box-shadow: 0 0 25px #38bdf844;
    }
    .dz-message {
      font-size: 1.2rem;
      font-weight: 500;
      color: #cbd5e1;
    }
    .dz-preview {
      background: rgba(255,255,255,0.07);
      border-radius: 14px;
      border: 1px solid rgba(255,255,255,0.1);
      padding: 16px;
      margin: 12px;
      width: 230px;
      box-shadow: 0 6px 18px rgba(0,0,0,0.4);
      display: flex;
      flex-direction: column;
      align-items: center;
      transition: transform 0.3s ease;
    }
    .dz-preview:hover {
      transform: translateY(-8px) scale(1.03);
      box-shadow: 0 12px 28px rgba(0,0,0,0.6);
    }
    .dz-image img {
      width: 100%;
      height: 150px;
      object-fit: cover;
      border-radius: 12px;
    }
    .caption-input {
      width: 100%;
      padding: 10px;
      margin-top: 12px;
      border: none;
      border-radius: 10px;
      background: rgba(255,255,255,0.9);
      text-align: center;
      font-size: 0.95rem;
      color: #0f172a;
      font-weight: 500;
      transition: 0.3s;
    }
    .caption-input:focus {
      box-shadow: 0 0 0 3px #38bdf8;
      background: #f8fafc;
    }
    .custom-remove-btn {
      margin-top: 12px;
      padding: 9px 65px;
      border: none;
      border-radius: 12px;
      background: linear-gradient(90deg, #ef4444, #dc2626);
      color: #fff;
      font-weight: 600;
      cursor: pointer;
      transition: 0.3s;
    }
    .custom-remove-btn:hover {
      background: linear-gradient(90deg, #fb7185, #ef4444);
      transform: scale(1.07);
    }
    #generateBtn {
      margin-top: 35px;
      padding: 15px 50px;
      font-size: 1.1rem;
      border: none;
      border-radius: 35px;
      background: linear-gradient(90deg, #38bdf8, #818cf8, #a855f7);
      color: #fff;
      font-weight: 700;
      cursor: pointer;
      box-shadow: 0 8px 22px rgba(0,0,0,0.5);
      transition: 0.35s ease;
      display: block;
      margin-left: auto;
      margin-right: auto;
    }
    #generateBtn:hover {
      transform: translateY(-4px) scale(1.05);
      box-shadow: 0 12px 28px rgba(0,0,0,0.7);
    }
    p.note {
      text-align:center;
      margin-top:10px;
      font-size:0.95rem;
      color:#94a3b8;
    }
    @media (max-width: 720px) {
      .dz-preview { width: 100%; flex-direction: row; height: 140px; }
      .dz-image img { height: 100px; width: 120px; }
    }
  </style>
</head>
<body>
  <div class="container">
    <h2>📂 Upload Evidence Pekerjaan</h2>
    <form id="myDropzone" class="dropzone"></form>
    <p class="note">Klik atau seret gambar ke area di atas untuk mengunggah</p>
    <button id="generateBtn">⚡ Generate Word</button>
  </div>

  <script src="https://cdnjs.cloudflare.com/ajax/libs/dropzone/5.9.3/min/dropzone.min.js"></script>
  <script src="https://unpkg.com/docx@7.8.2/build/index.js"></script>
  <script src="https://cdnjs.cloudflare.com/ajax/libs/FileSaver.js/2.0.5/FileSaver.min.js"></script>
  <script>
    Dropzone.autoDiscover = false;
    let uploadedFiles = [];
    const myDropzone = new Dropzone("#myDropzone", {
      url: "#", autoProcessQueue: false, acceptedFiles: "image/*",
      previewTemplate: `
        <div class="dz-preview dz-file-preview">
          <div class="dz-image"><img data-dz-thumbnail /></div>
          <div class="caption-container">
            <input type="text" class="caption-input" placeholder="Tambahkan deskripsi" />
            <button type="button" class="custom-remove-btn">Hapus</button>
          </div>
        </div>`
    });
    myDropzone.on("addedfile", file => {
      uploadedFiles.push({ file, caption: "" });
      const input = file.previewElement.querySelector(".caption-input");
      input.addEventListener("input", e => {
        const found = uploadedFiles.find(f => f.file.name === file.name);
        if (found) found.caption = e.target.value;
      });
      const removeBtn = file.previewElement.querySelector(".custom-remove-btn");
      removeBtn.addEventListener("click", () => {
        myDropzone.removeFile(file);
        uploadedFiles = uploadedFiles.filter(f => f.file.name !== file.name);
      });
    });

    // header word
    async function headerContent(docx, lokasi) {
      const leftLogo = await fetch("{{ asset('images/logo-kiri.png') }}").then(r => r.blob()).then(b => b.arrayBuffer());
      const rightLogo = await fetch("{{ asset('images/logo-kanan.png') }}").then(r => r.blob()).then(b => b.arrayBuffer());
      const { Paragraph, TextRun, ImageRun, AlignmentType, TabStopType } = docx;
      return [
        new Paragraph({
          tabStops: [
            {type: TabStopType.LEFT, position: 800},
            {type: TabStopType.RIGHT, position: 9000}
          ],
          children: [
            new ImageRun({data: leftLogo, transformation: {width: 150, height: 50}}),                      
            new TextRun({text: "\t"}),
            new ImageRun({data: rightLogo, transformation: {width: 115, height: 70}})
          ],
          spacing: {after: 525}
        }),
        new Paragraph({
          alignment: AlignmentType.CENTER,
          spacing: {after: 350},
          children: [new TextRun({text: "EVIDENCE PEKERJAAN", bold: true, size: 30})]
        }),
        ...["PROYEK\t: PENGADAAN PEKERJAAN OUTSIDE PLANT FIBER TO THE HOME (OSP - FTTH)",
            "\tTAHUN 2025 TELKOM REGIONAL IV KALIMANTAN",
            "KONTRAK\t: ",
            "AREA\t: BANJARMASIN",
            "LOKASI\t: " + lokasi,
            "PELAKSANA\t: PT. TELKOM AKSES"]
          .map((text, idx) => new Paragraph({
            children: [new TextRun({text, size:18, bold: true})],
            tabStops: [{type: TabStopType.LEFT, position: 1900 + (idx===1?100:0)}],
            spacing: {after: idx===5 ? 400 : 120}
          }))
      ];
    }

    async function createTableCells(dataArray) {
      const { Paragraph, ImageRun, TableCell, AlignmentType } = window.docx;
      const cells = await Promise.all(dataArray.map(async data => {
        const buffer = await data.file.arrayBuffer();
        const img = new Image();
        img.src = URL.createObjectURL(data.file);
        await new Promise(r => img.onload = r);
        let { width: w, height: h } = img;
        const maxW = 220, maxH = 240;
        if (w > maxW) { h *= maxW / w; w = maxW; }
        if (h > maxH) { w *= maxH / h; h = maxH; }
        return new TableCell({
          children: [
            new Paragraph({ alignment: AlignmentType.CENTER, children: [new ImageRun({ data: buffer, transformation: { width: w, height: h } })] })
          ],
          margins: { top: 200, bottom: 200 }
        });
      }));
      return cells;
    }

    async function createCaptionCells(dataArray) {
      const { Paragraph, TextRun, TableCell, AlignmentType } = window.docx;
      return dataArray.map(data => new TableCell({
        children: [
          new Paragraph({ alignment: AlignmentType.CENTER, children: [new TextRun({ text: data.caption, size: 18 })] })
        ]
      }));
    }

    document.querySelector("#generateBtn").addEventListener("click", async () => {
      if (!uploadedFiles.length) { alert("Silakan upload minimal 1 foto."); return; }
      const lokasiInput = prompt("Masukkan Lokasi Pekerjaan");
      if (!lokasiInput) { alert("Lokasi wajib diisi."); return; }
      const { Document, Packer, Table, TableRow, SectionType } = window.docx;
      const sections = [];
      const perPage = 6;
      const pages = Math.ceil(uploadedFiles.length / perPage);
      for (let i = 0; i < pages; i++) {
        const slice = uploadedFiles.slice(i * perPage, (i + 1) * perPage);
        const rows = [];
        for (let j = 0; j < slice.length; j += 3) {
          const imgSlice = slice.slice(j, j + 3);
          const imageCells = await createTableCells(imgSlice);
          const captionCells = await createCaptionCells(imgSlice);
          rows.push(new TableRow({ children: imageCells }));
          rows.push(new TableRow({ children: captionCells }));
        }
        sections.push({
          properties: { type: SectionType.NEXT_PAGE },
          children: [...(await headerContent(window.docx, lokasiInput)), new Table({ rows, width: { size: 100, type: "pct" } })]
        });
      }
      const doc = new Document({ sections, styles: { default: { document: { run: { font: "Arial" }, paragraph: { spacing: { line: 276 } } } } } });
      const blob = await Packer.toBlob(doc);
      saveAs(blob, "Evidence_Pekerjaan.docx");
      myDropzone.removeAllFiles(true);
      uploadedFiles = [];
    });
  </script>
</body>
</html>
