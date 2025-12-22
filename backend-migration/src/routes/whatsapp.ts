import { Router, Request, Response } from "express";
import dotenv from "dotenv";
import { sendWhatsAppMessage } from "../services/whatsapp";
import { appendActivity } from "../services/activityStore";

dotenv.config();
const router = Router();

// Proxy endpoint to send WhatsApp message via the configured provider
router.post("/send", async (req: Request, res: Response) => {
  try {
    const { to, message } = req.body;
    if (!to || !message) return res.status(400).json({ error: "to and message required" });
    await sendWhatsAppMessage(to, message);
    await appendActivity({ action: "whatsapp_send", meta: { to } });
    res.json({ message: "queued" });
  } catch (err: any) {
    res.status(500).json({ error: err.message });
  }
});

// Webhook receiver placeholder for provider callbacks
router.post("/webhook", async (req: Request, res: Response) => {
  try {
    // Store the webhook payload as activity for later processing
    await appendActivity({ action: "whatsapp_webhook", meta: req.body });
    res.json({ status: "ok" });
  } catch (err: any) {
    res.status(500).json({ error: err.message });
  }
});

export default router;
