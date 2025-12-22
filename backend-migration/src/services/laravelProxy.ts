import axios from "axios";

const baseURL = process.env.LARAVEL_API_URL || "http://localhost:8000";
const client = axios.create({ baseURL, timeout: 15000 });

// Helper to include Authorization header when present
function authHeaders(authHeader?: string) {
  const headers: Record<string, string> = {};
  if (authHeader) headers["Authorization"] = authHeader;
  return headers;
}

export default {
  async getTenders(query: Record<string, any>, authHeader?: string) {
    return client.get("/api/tenders", {
      params: query,
      headers: authHeaders(authHeader),
    });
  },

  async createTender(payload: any, authHeader?: string) {
    return client.post("/api/tenders", payload, {
      headers: authHeaders(authHeader),
    });
  },

  // NEW: re-tender proxy (if Laravel exposes this endpoint)
  async postReTender(tenderId: string, payload: any, authHeader?: string) {
    return client.post(
      `/api/tenders/${encodeURIComponent(tenderId)}/re-tender`,
      payload,
      {
        headers: authHeaders(authHeader),
      }
    );
  },

  // NEW: vendor proxies
  async getVendors(query: Record<string, any>, authHeader?: string) {
    return client.get("/api/vendors", {
      params: query,
      headers: authHeaders(authHeader),
    });
  },

  async createVendor(payload: any, authHeader?: string) {
    return client.post("/api/vendors", payload, {
      headers: authHeaders(authHeader),
    });
  },

  // Add more proxies or new service methods here (activity logs, role checks, reports, etc.)
};
