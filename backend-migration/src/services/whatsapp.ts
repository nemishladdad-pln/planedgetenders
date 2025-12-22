import dotenv from "dotenv";
import axios from "axios";
dotenv.config();

export async function sendWhatsAppMessage(to: string, message: string) {
  // Placeholder: if WHATSAPP_API_URL is configured call provider; otherwise log
  const url = process.env.WHATSAPP_API_URL;
  if (!url) {
    // eslint-disable-next-line no-console
    console.log(`[WHATSAPP placeholder] to=${to} msg=${message}`);
    return;
  }
  try {
    await axios.post(url, { to, message }, { headers: { "x-api-key": process.env.WHATSAPP_API_KEY || "" }, timeout: 8000 });
  } catch (err) {
    // eslint-disable-next-line no-console
    console.error("WhatsApp send failed", (err as any)?.message ?? err);
  }
}
