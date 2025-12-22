import axios from "axios";
import dotenv from "dotenv";
dotenv.config();

const PROVIDER_URL = process.env.WHATSAPP_API_URL || "";

export async function proxySend(to: string, message: string) {
  if (!PROVIDER_URL) {
    // no provider configured; placeholder
    console.log("[whatsappGateway] no provider configured, skipping send", { to, message: message.slice(0, 60) });
    return;
  }
  // provider specific: post to configured URL
  await axios.post(PROVIDER_URL, { to, message }, { headers: { "x-api-key": process.env.WHATSAPP_API_KEY || "" } });
}

export default { proxySend };
