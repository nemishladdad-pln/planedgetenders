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
const dotenv_1 = __importDefault(require("dotenv"));
const whatsapp_1 = require("../services/whatsapp");
const store = __importStar(require("../services/store"));
const roleMiddleware_1 = require("../middleware/roleMiddleware");
const activityStore_1 = require("../services/activityStore");
dotenv_1.default.config();
const router = (0, express_1.Router)();
// Simple in-memory OTP store (expiry 5 mins)
const otps = {};
// Request OTP for phone (sends to WhatsApp placeholder)
router.post("/request-otp", async (req, res) => {
    try {
        const phone = req.body.phone;
        if (!phone)
            return res.status(400).json({ error: "phone required" });
        const code = Math.floor(100000 + Math.random() * 900000).toString();
        otps[phone] = { code, expires: Date.now() + 5 * 60 * 1000 };
        // send via WhatsApp (placeholder)
        await (0, whatsapp_1.sendWhatsAppMessage)(phone, `Your Planedge OTP: ${code}`);
        await (0, activityStore_1.appendActivity)({ action: "request_otp", meta: { phone } });
        res.json({ message: "otp_sent" });
    }
    catch (err) {
        res.status(500).json({ error: err.message });
    }
});
// Verify OTP
router.post("/verify-otp", async (req, res) => {
    try {
        const { phone, code } = req.body;
        const rec = otps[phone];
        if (!rec || rec.code !== code || rec.expires < Date.now())
            return res.status(400).json({ error: "invalid_or_expired" });
        delete otps[phone];
        // Create or return user stub (in real app tie to auth)
        await (0, activityStore_1.appendActivity)({ action: "verify_otp", meta: { phone } });
        res.json({ message: "verified", phone });
    }
    catch (err) {
        res.status(500).json({ error: err.message });
    }
});
// Mobile-friendly endpoints
router.get("/mobile/tenders", async (_req, res) => {
    try {
        const data = await store.listTenders();
        // return minimal fields for mobile
        const mobile = data.map(t => ({ id: t.id, title: t.title, status: t.status, dueDate: t.dueDate }));
        res.json({ total: mobile.length, data: mobile });
    }
    catch (err) {
        res.status(500).json({ error: err.message });
    }
});
// Mobile dashboard (minimal)
router.get("/mobile/dashboard", async (_req, res) => {
    try {
        const users = await store.countUsers();
        const tenders = await store.countTenders();
        const status = await store.countTenderStatus();
        res.json({ users, tenders, status });
    }
    catch (err) {
        res.status(500).json({ error: err.message });
    }
});
// Mobile vendor profile
router.get("/mobile/vendors/:id", async (req, res) => {
    try {
        const v = await store.getVendor(req.params.id);
        if (!v)
            return res.status(404).json({ error: "Vendor not found" });
        const minimal = { id: v.id, name: v.name, contact: v.contact, partial: v.partial };
        res.json(minimal);
    }
    catch (err) {
        res.status(500).json({ error: err.message });
    }
});
// Buyer registration (requires admin approval)
router.post("/buyers/register", async (req, res) => {
    try {
        const b = await store.createBuyer(req.body);
        await (0, activityStore_1.appendActivity)({ action: "buyer_registered", meta: { id: b.id, email: req.body.email } });
        res.status(201).json({ message: "registered_pending", id: b.id });
    }
    catch (err) {
        res.status(500).json({ error: err.message });
    }
});
// Admin approve buyer
router.post("/buyers/:id/approve", (0, roleMiddleware_1.requireRole)("Admin"), async (req, res) => {
    try {
        const id = req.params.id;
        const b = await store.approveBuyer(id);
        await (0, activityStore_1.appendActivity)({ action: "buyer_approved", by: req.userRole, meta: { id } });
        res.json(b);
    }
    catch (err) {
        res.status(500).json({ error: err.message });
    }
});
// Subscription endpoints (yearly)
router.post("/subscribe", async (req, res) => {
    try {
        const sub = await store.createSubscription(req.body);
        await (0, activityStore_1.appendActivity)({ action: "subscribe", meta: { subscriptionId: sub.id } });
        res.status(201).json(sub);
    }
    catch (err) {
        res.status(500).json({ error: err.message });
    }
});
// List subscriptions (admin)
router.get("/subscriptions", (0, roleMiddleware_1.requireRole)("Admin"), async (req, res) => {
    try {
        const page = req.query.page ? Number(req.query.page) : 1;
        const perPage = req.query.perPage ? Number(req.query.perPage) : 20;
        const list = await store.listSubscriptionsPaginated(page, perPage);
        res.json(list);
    }
    catch (err) {
        res.status(500).json({ error: err.message });
    }
});
// Update subscription (admin)
router.patch("/subscriptions/:id", (0, roleMiddleware_1.requireRole)("Admin"), async (req, res) => {
    try {
        const id = req.params.id;
        const updated = await store.updateSubscription(id, req.body);
        await (0, activityStore_1.appendActivity)({ action: "update_subscription", by: req.userRole, meta: { id } });
        res.json(updated);
    }
    catch (err) {
        res.status(500).json({ error: err.message });
    }
});
exports.default = router;
