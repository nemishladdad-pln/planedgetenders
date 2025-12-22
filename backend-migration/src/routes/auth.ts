import { Router, Request, Response } from "express";
import dotenv from "dotenv";
import crypto from "crypto";
import { sendWhatsAppMessage } from "../services/whatsapp";
import * as store from "../services/store";
import { requireRole } from "../middleware/roleMiddleware";
import { appendActivity } from "../services/activityStore";

dotenv.config();
const router = Router();

// Simple in-memory OTP store (expiry 5 mins)
const otps: Record<string, { code: string; expires: number }> = {};

// Request OTP for phone (sends to WhatsApp placeholder)
router.post("/request-otp", async (req: Request, res: Response) => {
  try {
    const phone = req.body.phone;
    if (!phone) return res.status(400).json({ error: "phone required" });
    const code = Math.floor(100000 + Math.random() * 900000).toString();
    otps[phone] = { code, expires: Date.now() + 5 * 60 * 1000 };
    // send via WhatsApp (placeholder)
    await sendWhatsAppMessage(phone, `Your Planedge OTP: ${code}`);
    await appendActivity({ action: "request_otp", meta: { phone } });
    res.json({ message: "otp_sent" });
  } catch (err: any) {
    res.status(500).json({ error: err.message });
  }
});

// Verify OTP
router.post("/verify-otp", async (req: Request, res: Response) => {
  try {
    const { phone, code } = req.body;
    const rec = otps[phone];
    if (!rec || rec.code !== code || rec.expires < Date.now()) return res.status(400).json({ error: "invalid_or_expired" });
    delete otps[phone];
    // Create or return user stub (in real app tie to auth)
    await appendActivity({ action: "verify_otp", meta: { phone } });
    res.json({ message: "verified", phone });
  } catch (err: any) {
    res.status(500).json({ error: err.message });
  }
});

// Mobile-friendly endpoints
router.get("/mobile/tenders", async (_req: Request, res: Response) => {
  try {
    const data = await store.listTenders();
    // return minimal fields for mobile
    const mobile = data.map(t => ({ id: t.id, title: t.title, status: t.status, dueDate: t.dueDate }));
    res.json({ total: mobile.length, data: mobile });
  } catch (err: any) {
    res.status(500).json({ error: err.message });
  }
});

// Mobile dashboard (minimal)
router.get("/mobile/dashboard", async (_req: Request, res: Response) => {
  try {
    const users = await store.countUsers();
    const tenders = await store.countTenders();
    const status = await store.countTenderStatus();
    res.json({ users, tenders, status });
  } catch (err: any) {
    res.status(500).json({ error: err.message });
  }
});

// Mobile vendor profile
router.get("/mobile/vendors/:id", async (req: Request, res: Response) => {
  try {
    const v = await store.getVendor(req.params.id);
    if (!v) return res.status(404).json({ error: "Vendor not found" });
    const minimal = { id: v.id, name: v.name, contact: v.contact, partial: v.partial };
    res.json(minimal);
  } catch (err: any) {
    res.status(500).json({ error: err.message });
  }
});

// Buyer registration (requires admin approval)
router.post("/buyers/register", async (req: Request, res: Response) => {
  try {
    const b = await store.createBuyer(req.body);
    await appendActivity({ action: "buyer_registered", meta: { id: b.id, email: req.body.email } });
    res.status(201).json({ message: "registered_pending", id: b.id });
  } catch (err: any) {
    res.status(500).json({ error: err.message });
  }
});

// Admin approve buyer
router.post("/buyers/:id/approve", requireRole("Admin"), async (req: Request, res: Response) => {
  try {
    const id = req.params.id;
    const b = await store.approveBuyer(id);
    await appendActivity({ action: "buyer_approved", by: (req as any).userRole, meta: { id } });
    res.json(b);
  } catch (err: any) {
    res.status(500).json({ error: err.message });
  }
});

// Subscription endpoints (yearly)
router.post("/subscribe", async (req: Request, res: Response) => {
  try {
    const sub = await store.createSubscription(req.body);
    await appendActivity({ action: "subscribe", meta: { subscriptionId: sub.id } });
    res.status(201).json(sub);
  } catch (err: any) {
    res.status(500).json({ error: err.message });
  }
});

// List subscriptions (admin)
router.get("/subscriptions", requireRole("Admin"), async (req: Request, res: Response) => {
  try {
    const page = req.query.page ? Number(req.query.page) : 1;
    const perPage = req.query.perPage ? Number(req.query.perPage) : 20;
    const list = await store.listSubscriptionsPaginated(page, perPage);
    res.json(list);
  } catch (err: any) {
    res.status(500).json({ error: err.message });
  }
});

// Update subscription (admin)
router.patch("/subscriptions/:id", requireRole("Admin"), async (req: Request, res: Response) => {
  try {
    const id = req.params.id;
    const updated = await store.updateSubscription(id, req.body);
    await appendActivity({ action: "update_subscription", by: (req as any).userRole, meta: { id } });
    res.json(updated);
  } catch (err: any) {
    res.status(500).json({ error: err.message });
  }
});

export default router;
