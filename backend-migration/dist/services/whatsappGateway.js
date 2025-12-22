"use strict";
var __importDefault = (this && this.__importDefault) || function (mod) {
    return (mod && mod.__esModule) ? mod : { "default": mod };
};
Object.defineProperty(exports, "__esModule", { value: true });
exports.proxySend = proxySend;
const axios_1 = __importDefault(require("axios"));
const dotenv_1 = __importDefault(require("dotenv"));
dotenv_1.default.config();
const PROVIDER_URL = process.env.WHATSAPP_API_URL || "";
async function proxySend(to, message) {
    if (!PROVIDER_URL) {
        // no provider configured; placeholder
        console.log("[whatsappGateway] no provider configured, skipping send", { to, message: message.slice(0, 60) });
        return;
    }
    // provider specific: post to configured URL
    await axios_1.default.post(PROVIDER_URL, { to, message }, { headers: { "x-api-key": process.env.WHATSAPP_API_KEY || "" } });
}
exports.default = { proxySend };
