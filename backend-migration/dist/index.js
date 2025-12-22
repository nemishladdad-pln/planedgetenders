"use strict";
var __importDefault = (this && this.__importDefault) || function (mod) {
    return (mod && mod.__esModule) ? mod : { "default": mod };
};
Object.defineProperty(exports, "__esModule", { value: true });
const express_1 = __importDefault(require("express"));
const cors_1 = __importDefault(require("cors"));
const dotenv_1 = __importDefault(require("dotenv"));
const path_1 = __importDefault(require("path"));
const fs_1 = __importDefault(require("fs"));
const tenders_1 = __importDefault(require("./routes/tenders"));
const auth_1 = __importDefault(require("./routes/auth"));
const whatsapp_1 = __importDefault(require("./routes/whatsapp"));
dotenv_1.default.config();
const app = (0, express_1.default)();
const PORT = process.env.PORT ? Number(process.env.PORT) : 4000;
const UPLOAD_DIR = process.env.UPLOAD_DIR || path_1.default.resolve(__dirname, "../data/uploads");
// ensure upload dir exists
fs_1.default.mkdirSync(UPLOAD_DIR, { recursive: true });
app.use((0, cors_1.default)());
app.use(express_1.default.json());
app.use(express_1.default.urlencoded({ extended: true }));
// serve uploads statically (ensure appropriate access controls in production)
app.use("/uploads", express_1.default.static(UPLOAD_DIR));
// Health
app.get("/health", (_req, res) => {
    res.json({ status: "ok", time: Date.now() });
});
// auth & mobile endpoints
app.use("/api/auth", auth_1.default);
// whatsapp gateway endpoints (placeholder)
app.use("/api/whatsapp", whatsapp_1.default);
// tenders, vendors, admin
app.use("/api/tenders", tenders_1.default);
// root
app.get("/", (_req, res) => {
    res.send("Planedge Gateway - self-host ready");
});
app.listen(PORT, () => {
    // eslint-disable-next-line no-console
    console.log(`Planedge gateway running on port ${PORT}`);
});
