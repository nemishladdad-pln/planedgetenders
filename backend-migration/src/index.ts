import express from "express";
import cors from "cors";
import dotenv from "dotenv";
import path from "path";
import fs from "fs";
import tendersRouter from "./routes/tenders";
import authRouter from "./routes/auth";
import whatsappRouter from "./routes/whatsapp";

dotenv.config();

const app = express();
const PORT = process.env.PORT ? Number(process.env.PORT) : 4000;
const UPLOAD_DIR = process.env.UPLOAD_DIR || path.resolve(__dirname, "../data/uploads");

// ensure upload dir exists
fs.mkdirSync(UPLOAD_DIR, { recursive: true });

app.use(cors());
app.use(express.json());
app.use(express.urlencoded({ extended: true }));

// serve uploads statically (ensure appropriate access controls in production)
app.use("/uploads", express.static(UPLOAD_DIR));

// Health
app.get("/health", (_req, res) => {
  res.json({ status: "ok", time: Date.now() });
});

// auth & mobile endpoints
app.use("/api/auth", authRouter);

// whatsapp gateway endpoints (placeholder)
app.use("/api/whatsapp", whatsappRouter);

// tenders, vendors, admin
app.use("/api/tenders", tendersRouter);

// root
app.get("/", (_req, res) => {
  res.send("Planedge Gateway - self-host ready");
});

app.listen(PORT, () => {
  // eslint-disable-next-line no-console
  console.log(`Planedge gateway running on port ${PORT}`);
});
