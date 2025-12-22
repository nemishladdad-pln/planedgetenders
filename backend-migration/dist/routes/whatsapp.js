"use strict";
var __importDefault = (this && this.__importDefault) || function (mod) {
    return (mod && mod.__esModule) ? mod : { "default": mod };
};
Object.defineProperty(exports, "__esModule", { value: true });
const express_1 = require("express");
const dotenv_1 = __importDefault(require("dotenv"));
const whatsapp_1 = require("../services/whatsapp");
const activityStore_1 = require("../services/activityStore");
dotenv_1.default.config();
const router = (0, express_1.Router)();
// Proxy endpoint to send WhatsApp message via the configured provider
router.post("/send", async (req, res) => {
    try {
        const { to, message } = req.body;
        if (!to || !message)
            return res.status(400).json({ error: "to and message required" });
        await (0, whatsapp_1.sendWhatsAppMessage)(to, message);
        await (0, activityStore_1.appendActivity)({ action: "whatsapp_send", meta: { to } });
        res.json({ message: "queued" });
    }
    catch (err) {
        res.status(500).json({ error: err.message });
    }
});
// Webhook receiver placeholder for provider callbacks
router.post("/webhook", async (req, res) => {
    try {
        // Store the webhook payload as activity for later processing
        await (0, activityStore_1.appendActivity)({ action: "whatsapp_webhook", meta: req.body });
        res.json({ status: "ok" });
    }
    catch (err) {
        res.status(500).json({ error: err.message });
    }
});
exports.default = router;
