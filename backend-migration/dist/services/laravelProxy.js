"use strict";
var __importDefault = (this && this.__importDefault) || function (mod) {
    return (mod && mod.__esModule) ? mod : { "default": mod };
};
Object.defineProperty(exports, "__esModule", { value: true });
const axios_1 = __importDefault(require("axios"));
const baseURL = process.env.LARAVEL_API_URL || "http://localhost:8000";
const client = axios_1.default.create({ baseURL, timeout: 15000 });
// Helper to include Authorization header when present
function authHeaders(authHeader) {
    const headers = {};
    if (authHeader)
        headers["Authorization"] = authHeader;
    return headers;
}
exports.default = {
    async getTenders(query, authHeader) {
        return client.get("/api/tenders", {
            params: query,
            headers: authHeaders(authHeader),
        });
    },
    async createTender(payload, authHeader) {
        return client.post("/api/tenders", payload, {
            headers: authHeaders(authHeader),
        });
    },
    // NEW: re-tender proxy (if Laravel exposes this endpoint)
    async postReTender(tenderId, payload, authHeader) {
        return client.post(`/api/tenders/${encodeURIComponent(tenderId)}/re-tender`, payload, {
            headers: authHeaders(authHeader),
        });
    },
    // NEW: vendor proxies
    async getVendors(query, authHeader) {
        return client.get("/api/vendors", {
            params: query,
            headers: authHeaders(authHeader),
        });
    },
    async createVendor(payload, authHeader) {
        return client.post("/api/vendors", payload, {
            headers: authHeaders(authHeader),
        });
    },
    // Add more proxies or new service methods here (activity logs, role checks, reports, etc.)
};
