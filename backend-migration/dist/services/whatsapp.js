"use strict";
var __importDefault = (this && this.__importDefault) || function (mod) {
    return (mod && mod.__esModule) ? mod : { "default": mod };
};
Object.defineProperty(exports, "__esModule", { value: true });
exports.sendWhatsAppMessage = sendWhatsAppMessage;
const dotenv_1 = __importDefault(require("dotenv"));
const axios_1 = __importDefault(require("axios"));
dotenv_1.default.config();
async function sendWhatsAppMessage(to, message) {
    // Placeholder: if WHATSAPP_API_URL is configured call provider; otherwise log
    const url = process.env.WHATSAPP_API_URL;
    if (!url) {
        // eslint-disable-next-line no-console
        console.log(`[WHATSAPP placeholder] to=${to} msg=${message}`);
        return;
    }
    try {
        await axios_1.default.post(url, { to, message }, { headers: { "x-api-key": process.env.WHATSAPP_API_KEY || "" }, timeout: 8000 });
    }
    catch (err) {
        // eslint-disable-next-line no-console
        console.error("WhatsApp send failed", err?.message ?? err);
    }
}
