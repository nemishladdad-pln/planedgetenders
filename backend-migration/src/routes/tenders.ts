import { Router, Request, Response } from "express";
import multer from "multer";
import path from "path";
import fs from "fs";
import dotenv from "dotenv";
import { requireRole } from "../middleware/roleMiddleware";
import * as store from "../services/store";
import { appendActivity, readActivities } from "../services/activityStore";
import laravelProxy from "../services/laravelProxy";

dotenv.config();
const router = Router();

const UPLOAD_DIR = process.env.UPLOAD_DIR || path.resolve(__dirname, "../../data/uploads");
const storage = multer.diskStorage({
  destination: (_req, _file, cb) => cb(null, UPLOAD_DIR),
  filename: (_req, file, cb) => cb(null, `${Date.now()}-${file.originalname}`)
});
const upload = multer({ storage });

// List tenders (local)
router.get("/", async (_req: Request, res: Response) => {
  try {
    const data = await store.listTenders();
    res.json({ total: data.length, data });
  } catch (err: any) {
    res.status(500).json({ error: err.message });
  }
});

// Create tender with category and subcategory
router.post("/", requireRole("Admin", "ProjectManager"), async (req: Request, res: Response) => {
  try {
    const created = await store.createTender(req.body);
    await appendActivity({ action: "create_tender", by: (req as any).userRole, meta: { id: created.id } });
    res.status(201).json(created);
  } catch (err: any) {
    res.status(500).json({ error: err.message });
  }
});

// Budget upload (lumpsum or file)
router.post("/:id/budget", requireRole("Admin", "ProjectManager"), upload.single("budgetFile"), async (req: Request, res: Response) => {
  try {
    const id = req.params.id;
    const type = (req.body.type as string) || (req.file ? "file" : "lumpsum");
    const amount = req.body.amount ? Number(req.body.amount) : undefined;
    const file = req.file ? `/uploads/${path.basename(req.file.path)}` : undefined;
    // if CSV uploaded, attempt to parse into items
    let items: any[] | undefined;
    if (file && req.file && req.file.originalname.toLowerCase().endsWith(".csv")) {
      try {
        const raw = await fs.promises.readFile(req.file.path, "utf8");
        const lines = raw.split(/\r?\n/).filter(Boolean);
        if (lines.length >= 1) {
          const headers = lines[0].split(",").map(h => h.trim());
          items = lines.slice(1).map(l => {
            const cols = l.split(",");
            const obj: any = {};
            headers.forEach((h, i) => obj[h] = cols[i] ? cols[i].trim() : "");
            return obj;
          });
        }
      } catch (e) {
        console.warn("CSV parse failed", e);
      }
    }
    const updated = await store.uploadTenderBudget(id, { type, amount, file, items });
    await appendActivity({ action: "upload_budget", by: (req as any).userRole, meta: { id, type, file } });
    res.json(updated);
  } catch (err: any) {
    res.status(500).json({ error: err.message });
  }
});

// Upload signed work order (Contractor or Admin)
router.post("/:id/upload-signed", requireRole("Contractor", "Admin"), upload.single("signedFile"), async (req: Request, res: Response) => {
  try {
    const id = req.params.id;
    if (!req.file) return res.status(400).json({ error: "signedFile required" });
    // store link in tender meta
    const t = await store.getTender(id);
    if (!t) return res.status(404).json({ error: "Tender not found" });
    const filePath = `/uploads/${path.basename(req.file.path)}`;
    const updated = await store.updateTender(id, { signedWorkOrder: filePath });
    await appendActivity({ action: "upload_signed", by: (req as any).userRole, meta: { id, file: filePath } });
    res.json({ message: "uploaded", file: updated.signedWorkOrder });
  } catch (err: any) {
    res.status(500).json({ error: err.message });
  }
});

// Vendors: partial registration before paywall
router.post("/vendors/partial", async (req: Request, res: Response) => {
  try {
    // accept only minimal fields for quick registration
    const { name, contact, email } = req.body;
    if (!name) return res.status(400).json({ error: "name required" });
    const v = await store.createVendor({ name, contact, email, partial: true, history: [{ event: "partial_registered" }] });
    await appendActivity({ action: "vendor_partial_registered", meta: { id: v.id, email } });
    res.status(201).json(v);
  } catch (err: any) {
    res.status(500).json({ error: err.message });
  }
});

// complete vendor registration (after payment)
router.post("/vendors/complete", requireRole("Admin", "ProjectManager"), async (req: Request, res: Response) => {
  try {
    const { id, more } = req.body;
    if (!id) return res.status(400).json({ error: "vendor id required" });
    await store.appendVendorHistory(id, { event: "completed_registration", data: more });
    const vendors = await store.listVendors();
    const v = vendors.find(x => x.id === id);
    if (!v) return res.status(404).json({ error: "Vendor not found" });
    v.partial = false;
    await appendActivity({ action: "vendor_complete", by: (req as any).userRole, meta: { id } });
    res.json(v);
  } catch (err: any) {
    res.status(500).json({ error: err.message });
  }
});

// Get vendor profile including history
router.get("/vendors/:id", async (req: Request, res: Response) => {
  try {
    const id = req.params.id;
    const v = await store.getVendor(id);
    if (!v) return res.status(404).json({ error: "Vendor not found" });
    res.json(v);
  } catch (err: any) {
    res.status(500).json({ error: err.message });
  }
});

// Vendor history (paginated)
router.get("/vendors/:id/history", async (req: Request, res: Response) => {
  try {
    const id = req.params.id;
    const offset = req.query.offset ? Number(req.query.offset) : 0;
    const limit = req.query.limit ? Number(req.query.limit) : 20;
    const hist = await store.getVendorHistory(id, offset, limit);
    res.json(hist);
  } catch (err: any) {
    res.status(500).json({ error: err.message });
  }
});

// Upcoming tenders (future dueDate, sorted)
router.get("/upcoming", async (_req: Request, res: Response) => {
  try {
    const tenders = await store.listTenders();
    const now = Date.now();
    const upcoming = (tenders || [])
      .filter((t: any) => t.dueDate && new Date(t.dueDate).getTime() > now)
      .sort((a: any, b: any) => new Date(a.dueDate).getTime() - new Date(b.dueDate).getTime())
      .map((t: any) => ({ id: t.id, title: t.title, dueDate: t.dueDate, status: t.status, category: t.category, subcategory: t.subcategory }));
    res.json({ total: upcoming.length, data: upcoming });
  } catch (err: any) {
    res.status(500).json({ error: err.message });
  }
});

// Calendar events (tender due & invoice due)
router.get("/calendar", async (_req: Request, res: Response) => {
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
  } catch (err: any) {
    res.status(500).json({ error: err.message });
  }
});

// Dashboard counts & reports (Planedge Admin, Client, Contractor)
router.get("/admin/dashboard", requireRole("Admin"), async (_req: Request, res: Response) => {
  try {
    const users = await store.countUsers();
    const tenders = await store.countTenders();
    const status = await store.countTenderStatus();
    const subscriptions = await store.listSubscriptions();
    res.json({ users, tenders, status, subscriptionsCount: subscriptions.length });
  } catch (err: any) {
    res.status(500).json({ error: err.message });
  }
});

// Rich dashboard summary (admin) includes monthly series and top vendors
router.get("/admin/dashboard/summary", requireRole("Admin"), async (_req: Request, res: Response) => {
  try {
    const users = await store.countUsers();
    const tenders = await store.countTenders();
    const status = await store.countTenderStatus();
    const monthly = await store.countTendersByMonth(12);
    const topVendors = await store.getTopVendors(10);
    const subs = await store.listSubscriptions();
    const subsByStatus = subs.reduce((acc: any, s: any) => { acc[s.status] = (acc[s.status] || 0) + 1; return acc; }, {});
    res.json({ users, tenders, status, monthly, topVendors, subscriptions: { total: subs.length, byStatus: subsByStatus } });
  } catch (err: any) {
    res.status(500).json({ error: err.message });
  }
});

// Invoices endpoints
router.post("/invoices", requireRole("Admin"), async (req: Request, res: Response) => {
  try {
    const inv = await store.createInvoice(req.body);
    await appendActivity({ action: "create_invoice", by: (req as any).userRole, meta: { id: inv.id } });
    res.status(201).json(inv);
  } catch (err: any) {
    res.status(500).json({ error: err.message });
  }
});

router.get("/invoices", requireRole("Admin"), async (_req: Request, res: Response) => {
  try {
    const inv = await store.listInvoices();
    res.json({ total: inv.length, data: inv });
  } catch (err: any) {
    res.status(500).json({ error: err.message });
  }
});

router.get("/invoices/:id", requireRole("Admin"), async (req: Request, res: Response) => {
  try {
    const id = req.params.id;
    const inv = await store.getInvoice(id);
    if (!inv) return res.status(404).json({ error: "Invoice not found" });
    res.json(inv);
  } catch (err: any) {
    res.status(500).json({ error: err.message });
  }
});

router.get("/invoices/user/:userId", requireRole("Admin"), async (req: Request, res: Response) => {
  try {
    const userId = req.params.userId;
    const inv = await store.listInvoicesByUser(userId);
    res.json({ total: inv.length, data: inv });
  } catch (err: any) {
    res.status(500).json({ error: err.message });
  }
});

// Activity: unchanged
router.get("/activity", requireRole("Admin"), async (_req: Request, res: Response) => {
  try {
    const items = await readActivities(500);
    res.json({ total: items.length, items });
  } catch (err: any) {
    res.status(500).json({ error: err.message });
  }
});

// Allow posting activities tied to an email (one email can register multiple activities)
router.post("/activities", async (req: Request, res: Response) => {
  try {
    const { email, action, meta } = req.body;
    if (!email || !action) return res.status(400).json({ error: "email and action required" });
    await appendActivity({ action, email, meta: meta || {} });
    res.status(201).json({ message: "recorded" });
  } catch (err: any) {
    res.status(500).json({ error: err.message });
  }
});

export default router;
