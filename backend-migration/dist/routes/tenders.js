"use strict";
var __createBinding = (this && this.__createBinding) || (Object.create ? (function(o, m, k, k2) {
    if (k2 === undefined) k2 = k;
    var desc = Object.getOwnPropertyDescriptor(m, k);
    if (!desc || ("get" in desc ? !m.__esModule : desc.writable || desc.configurable)) {
      desc = { enumerable: true, get: function() { return m[k]; } };
    }
    Object.defineProperty(o, k2, desc);
}) : (function(o, m, k, k2) {
    if (k2 === undefined) k2 = k;
    o[k2] = m[k];
}));
var __setModuleDefault = (this && this.__setModuleDefault) || (Object.create ? (function(o, v) {
    Object.defineProperty(o, "default", { enumerable: true, value: v });
}) : function(o, v) {
    o["default"] = v;
});
var __importStar = (this && this.__importStar) || (function () {
    var ownKeys = function(o) {
        ownKeys = Object.getOwnPropertyNames || function (o) {
            var ar = [];
            for (var k in o) if (Object.prototype.hasOwnProperty.call(o, k)) ar[ar.length] = k;
            return ar;
        };
        return ownKeys(o);
    };
    return function (mod) {
        if (mod && mod.__esModule) return mod;
        var result = {};
        if (mod != null) for (var k = ownKeys(mod), i = 0; i < k.length; i++) if (k[i] !== "default") __createBinding(result, mod, k[i]);
        __setModuleDefault(result, mod);
        return result;
    };
})();
var __importDefault = (this && this.__importDefault) || function (mod) {
    return (mod && mod.__esModule) ? mod : { "default": mod };
};
Object.defineProperty(exports, "__esModule", { value: true });
const express_1 = require("express");
const multer_1 = __importDefault(require("multer"));
const path_1 = __importDefault(require("path"));
const fs_1 = __importDefault(require("fs"));
const dotenv_1 = __importDefault(require("dotenv"));
const roleMiddleware_1 = require("../middleware/roleMiddleware");
const store = __importStar(require("../services/store"));
const activityStore_1 = require("../services/activityStore");
dotenv_1.default.config();
const router = (0, express_1.Router)();
const UPLOAD_DIR = process.env.UPLOAD_DIR || path_1.default.resolve(__dirname, "../../data/uploads");
const storage = multer_1.default.diskStorage({
    destination: (_req, _file, cb) => cb(null, UPLOAD_DIR),
    filename: (_req, file, cb) => cb(null, `${Date.now()}-${file.originalname}`)
});
const upload = (0, multer_1.default)({ storage });
// List tenders (local)
router.get("/", async (_req, res) => {
    try {
        const data = await store.listTenders();
        res.json({ total: data.length, data });
    }
    catch (err) {
        res.status(500).json({ error: err.message });
    }
});
// Create tender with category and subcategory
router.post("/", (0, roleMiddleware_1.requireRole)("Admin", "ProjectManager"), async (req, res) => {
    try {
        const created = await store.createTender(req.body);
        await (0, activityStore_1.appendActivity)({ action: "create_tender", by: req.userRole, meta: { id: created.id } });
        res.status(201).json(created);
    }
    catch (err) {
        res.status(500).json({ error: err.message });
    }
});
// Budget upload (lumpsum or file)
router.post("/:id/budget", (0, roleMiddleware_1.requireRole)("Admin", "ProjectManager"), upload.single("budgetFile"), async (req, res) => {
    try {
        const id = req.params.id;
        const type = req.body.type || (req.file ? "file" : "lumpsum");
        const amount = req.body.amount ? Number(req.body.amount) : undefined;
        const file = req.file ? `/uploads/${path_1.default.basename(req.file.path)}` : undefined;
        // if CSV uploaded, attempt to parse into items
        let items;
        if (file && req.file && req.file.originalname.toLowerCase().endsWith(".csv")) {
            try {
                const raw = await fs_1.default.promises.readFile(req.file.path, "utf8");
                const lines = raw.split(/\r?\n/).filter(Boolean);
                if (lines.length >= 1) {
                    const headers = lines[0].split(",").map(h => h.trim());
                    items = lines.slice(1).map(l => {
                        const cols = l.split(",");
                        const obj = {};
                        headers.forEach((h, i) => obj[h] = cols[i] ? cols[i].trim() : "");
                        return obj;
                    });
                }
            }
            catch (e) {
                console.warn("CSV parse failed", e);
            }
        }
        const updated = await store.uploadTenderBudget(id, { type, amount, file, items });
        await (0, activityStore_1.appendActivity)({ action: "upload_budget", by: req.userRole, meta: { id, type, file } });
        res.json(updated);
    }
    catch (err) {
        res.status(500).json({ error: err.message });
    }
});
// Upload signed work order (Contractor or Admin)
router.post("/:id/upload-signed", (0, roleMiddleware_1.requireRole)("Contractor", "Admin"), upload.single("signedFile"), async (req, res) => {
    try {
        const id = req.params.id;
        if (!req.file)
            return res.status(400).json({ error: "signedFile required" });
        // store link in tender meta
        const t = await store.getTender(id);
        if (!t)
            return res.status(404).json({ error: "Tender not found" });
        const filePath = `/uploads/${path_1.default.basename(req.file.path)}`;
        const updated = await store.updateTender(id, { signedWorkOrder: filePath });
        await (0, activityStore_1.appendActivity)({ action: "upload_signed", by: req.userRole, meta: { id, file: filePath } });
        res.json({ message: "uploaded", file: updated.signedWorkOrder });
    }
    catch (err) {
        res.status(500).json({ error: err.message });
    }
});
// Vendors: partial registration before paywall
router.post("/vendors/partial", async (req, res) => {
    try {
        // accept only minimal fields for quick registration
        const { name, contact, email } = req.body;
        if (!name)
            return res.status(400).json({ error: "name required" });
        const v = await store.createVendor({ name, contact, email, partial: true, history: [{ event: "partial_registered" }] });
        await (0, activityStore_1.appendActivity)({ action: "vendor_partial_registered", meta: { id: v.id, email } });
        res.status(201).json(v);
    }
    catch (err) {
        res.status(500).json({ error: err.message });
    }
});
// complete vendor registration (after payment)
router.post("/vendors/complete", (0, roleMiddleware_1.requireRole)("Admin", "ProjectManager"), async (req, res) => {
    try {
        const { id, more } = req.body;
        if (!id)
            return res.status(400).json({ error: "vendor id required" });
        await store.appendVendorHistory(id, { event: "completed_registration", data: more });
        const vendors = await store.listVendors();
        const v = vendors.find(x => x.id === id);
        if (!v)
            return res.status(404).json({ error: "Vendor not found" });
        v.partial = false;
        await (0, activityStore_1.appendActivity)({ action: "vendor_complete", by: req.userRole, meta: { id } });
        res.json(v);
    }
    catch (err) {
        res.status(500).json({ error: err.message });
    }
});
// Get vendor profile including history
router.get("/vendors/:id", async (req, res) => {
    try {
        const id = req.params.id;
        const v = await store.getVendor(id);
        if (!v)
            return res.status(404).json({ error: "Vendor not found" });
        res.json(v);
    }
    catch (err) {
        res.status(500).json({ error: err.message });
    }
});
// Vendor history (paginated)
router.get("/vendors/:id/history", async (req, res) => {
    try {
        const id = req.params.id;
        const offset = req.query.offset ? Number(req.query.offset) : 0;
        const limit = req.query.limit ? Number(req.query.limit) : 20;
        const hist = await store.getVendorHistory(id, offset, limit);
        res.json(hist);
    }
    catch (err) {
        res.status(500).json({ error: err.message });
    }
});
// Upcoming tenders (future dueDate, sorted)
router.get("/upcoming", async (_req, res) => {
    try {
        const tenders = await store.listTenders();
        const now = Date.now();
        const upcoming = (tenders || [])
            .filter((t) => t.dueDate && new Date(t.dueDate).getTime() > now)
            .sort((a, b) => new Date(a.dueDate).getTime() - new Date(b.dueDate).getTime())
            .map((t) => ({ id: t.id, title: t.title, dueDate: t.dueDate, status: t.status, category: t.category, subcategory: t.subcategory }));
        res.json({ total: upcoming.length, data: upcoming });
    }
    catch (err) {
        res.status(500).json({ error: err.message });
    }
});
// Calendar events (tender due & invoice due)
router.get("/calendar", async (_req, res) => {
    try {
        const tenders = await store.listTenders();
        const invoices = await store.listInvoices();
        const events = [
            ...tenders.map(t => ({ id: t.id, title: t.title, date: t.dueDate || t.workDue || null, type: "tender", status: t.status, workDue: t.workDue || null })),
            ...invoices.map(i => ({ id: i.id, title: `Invoice ${i.id}`, date: i.dueDate, type: "invoice", status: i.status }))
        ].filter(e => e.date);
        // add conditional flag for due soon (within 7 days)
        const now = Date.now();
        const withFlags = events.map(e => {
            const ts = e.date ? new Date(e.date).getTime() : Date.now();
            const diff = ts - now;
            return { ...e, dueSoon: diff <= 7 * 24 * 3600 * 1000 && diff >= 0, overdue: diff < 0 };
        });
        res.json(withFlags);
    }
    catch (err) {
        res.status(500).json({ error: err.message });
    }
});
// Dashboard counts & reports (Planedge Admin, Client, Contractor)
router.get("/admin/dashboard", (0, roleMiddleware_1.requireRole)("Admin"), async (_req, res) => {
    try {
        const users = await store.countUsers();
        const tenders = await store.countTenders();
        const status = await store.countTenderStatus();
        const subscriptions = await store.listSubscriptions();
        res.json({ users, tenders, status, subscriptionsCount: subscriptions.length });
    }
    catch (err) {
        res.status(500).json({ error: err.message });
    }
});
// Rich dashboard summary (admin) includes monthly series and top vendors
router.get("/admin/dashboard/summary", (0, roleMiddleware_1.requireRole)("Admin"), async (_req, res) => {
    try {
        const users = await store.countUsers();
        const tenders = await store.countTenders();
        const status = await store.countTenderStatus();
        const monthly = await store.countTendersByMonth(12);
        const topVendors = await store.getTopVendors(10);
        const subs = await store.listSubscriptions();
        const subsByStatus = subs.reduce((acc, s) => { acc[s.status] = (acc[s.status] || 0) + 1; return acc; }, {});
        res.json({ users, tenders, status, monthly, topVendors, subscriptions: { total: subs.length, byStatus: subsByStatus } });
    }
    catch (err) {
        res.status(500).json({ error: err.message });
    }
});
// Invoices endpoints
router.post("/invoices", (0, roleMiddleware_1.requireRole)("Admin"), async (req, res) => {
    try {
        const inv = await store.createInvoice(req.body);
        await (0, activityStore_1.appendActivity)({ action: "create_invoice", by: req.userRole, meta: { id: inv.id } });
        res.status(201).json(inv);
    }
    catch (err) {
        res.status(500).json({ error: err.message });
    }
});
router.get("/invoices", (0, roleMiddleware_1.requireRole)("Admin"), async (_req, res) => {
    try {
        const inv = await store.listInvoices();
        res.json({ total: inv.length, data: inv });
    }
    catch (err) {
        res.status(500).json({ error: err.message });
    }
});
router.get("/invoices/:id", (0, roleMiddleware_1.requireRole)("Admin"), async (req, res) => {
    try {
        const id = req.params.id;
        const inv = await store.getInvoice(id);
        if (!inv)
            return res.status(404).json({ error: "Invoice not found" });
        res.json(inv);
    }
    catch (err) {
        res.status(500).json({ error: err.message });
    }
});
router.get("/invoices/user/:userId", (0, roleMiddleware_1.requireRole)("Admin"), async (req, res) => {
    try {
        const userId = req.params.userId;
        const inv = await store.listInvoicesByUser(userId);
        res.json({ total: inv.length, data: inv });
    }
    catch (err) {
        res.status(500).json({ error: err.message });
    }
});
// Activity: unchanged
router.get("/activity", (0, roleMiddleware_1.requireRole)("Admin"), async (_req, res) => {
    try {
        const items = await (0, activityStore_1.readActivities)(500);
        res.json({ total: items.length, items });
    }
    catch (err) {
        res.status(500).json({ error: err.message });
    }
});
// Allow posting activities tied to an email (one email can register multiple activities)
router.post("/activities", async (req, res) => {
    try {
        const { email, action, meta } = req.body;
        if (!email || !action)
            return res.status(400).json({ error: "email and action required" });
        await (0, activityStore_1.appendActivity)({ action, email, meta: meta || {} });
        res.status(201).json({ message: "recorded" });
    }
    catch (err) {
        res.status(500).json({ error: err.message });
    }
});
exports.default = router;
