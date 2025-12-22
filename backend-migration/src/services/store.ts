import fs from "fs";
import path from "path";
import dotenv from "dotenv";
dotenv.config();

const DB_FILE = process.env.DATA_STORE_FILE || path.resolve(__dirname, "../../data/db.json");

type Tender = {
  id: string;
  title: string;
  description?: string;
  category?: string;
  subcategory?: string; // new
  status?: string;
  budget?: { type?: string; amount?: number; file?: string }; // budget upload lumpsum or file
  createdAt?: string;
  updatedAt?: string;
  dueDate?: string;
  [k: string]: any;
};

type Vendor = {
  id: string;
  name: string;
  contact?: string;
  history?: any[]; // store registration/history events
  partial?: boolean; // partial registration before paywall
  createdAt?: string;
  [k: string]: any;
};

type Subscription = {
  id: string;
  userId: string;
  type: "yearly" | "monthly";
  start: string;
  end: string;
  status: "active" | "expired" | "pending";
};

type Invoice = {
  id: string;
  userId: string;
  amount: number;
  registrationDate: string;
  dueDate: string;
  status?: string;
  meta?: any;
};

async function ensureDb() {
  await fs.promises.mkdir(path.dirname(DB_FILE), { recursive: true });
  try {
    await fs.promises.access(DB_FILE);
  } catch {
    const initial = { tenders: [], vendors: [], subscriptions: [], invoices: [], buyers: [], users: [] };
    await fs.promises.writeFile(DB_FILE, JSON.stringify(initial, null, 2), "utf8");
  }
}

async function readDb(): Promise<any> {
  await ensureDb();
  const raw = await fs.promises.readFile(DB_FILE, "utf8");
  return JSON.parse(raw || "{}");
}

async function writeDb(db: any) {
  await fs.promises.writeFile(DB_FILE, JSON.stringify(db, null, 2), "utf8");
}

// Tenders
export async function listTenders(): Promise<Tender[]> {
  const db = await readDb();
  return db.tenders || [];
}

export async function createTender(payload: Partial<Tender>): Promise<Tender> {
  const db = await readDb();
  const id = payload.id || `t-${Date.now()}`;
  const now = new Date().toISOString();
  const t: Tender = { id, ...payload, title: payload.title || "Untitled Tender", createdAt: now, updatedAt: now, status: payload.status || "Active" };
  db.tenders = db.tenders || [];
  db.tenders.push(t);
  await writeDb(db);
  return t;
}

export async function getTender(id: string): Promise<Tender | null> {
  const db = await readDb();
  const found = (db.tenders || []).find((x: Tender) => x.id === id);
  return found || null;
}

export async function reTender(id: string, payload: Partial<Tender>): Promise<Tender> {
  const original = await getTender(id);
  const newTender = await createTender({ title: `${original?.title || "Re-tender"} (re)`, ...payload, originalId: id });
  return newTender;
}

export async function uploadTenderBudget(id: string, budget: { type?: string; amount?: number; file?: string }) {
  const db = await readDb();
  db.tenders = db.tenders || [];
  const idx = db.tenders.findIndex((t: Tender) => t.id === id);
  if (idx === -1) throw new Error("Tender not found");
  db.tenders[idx].budget = budget;
  db.tenders[idx].updatedAt = new Date().toISOString();
  await writeDb(db);
  return db.tenders[idx];
}

// Vendors
export async function listVendors(): Promise<Vendor[]> {
  const db = await readDb();
  return db.vendors || [];
}

export async function getVendor(id: string): Promise<Vendor | null> {
  const db = await readDb();
  const v = (db.vendors || []).find((x: Vendor) => x.id === id);
  return v || null;
}

export async function createVendor(payload: Partial<Vendor>): Promise<Vendor> {
  const db = await readDb();
  const id = payload.id || `v-${Date.now()}`;
  const now = new Date().toISOString();
  const v: Vendor = { id, name: payload.name || "Unknown", history: payload.history || [], partial: !!payload.partial, createdAt: now, ...payload };
  db.vendors = db.vendors || [];
  db.vendors.push(v);
  await writeDb(db);
  return v;
}

export async function appendVendorHistory(id: string, entry: any) {
  const db = await readDb();
  const v = (db.vendors || []).find((x: Vendor) => x.id === id);
  if (!v) throw new Error("Vendor not found");
  v.history = v.history || [];
  v.history.push({ ts: new Date().toISOString(), ...entry });
  await writeDb(db);
  return v;
}

// Subscriptions
export async function createSubscription(sub: Partial<Subscription>): Promise<Subscription> {
  const db = await readDb();
  const id = `s-${Date.now()}`;
  const start = sub.start || new Date().toISOString();
  const end = sub.end || new Date(new Date(start).setFullYear(new Date(start).getFullYear() + 1)).toISOString();
  const s: Subscription = { id, userId: sub.userId || "unknown", type: sub.type || "yearly", start, end, status: sub.status || "active" };
  db.subscriptions = db.subscriptions || [];
  db.subscriptions.push(s);
  await writeDb(db);
  return s;
}

export async function listSubscriptions(): Promise<Subscription[]> {
  const db = await readDb();
  return db.subscriptions || [];
}

export async function getSubscription(id: string): Promise<Subscription | null> {
  const db = await readDb();
  const s = (db.subscriptions || []).find((x: Subscription) => x.id === id);
  return s || null;
}

export async function updateSubscription(id: string, fields: Partial<Subscription>) {
  const db = await readDb();
  db.subscriptions = db.subscriptions || [];
  const idx = db.subscriptions.findIndex((s: Subscription) => s.id === id);
  if (idx === -1) throw new Error("Subscription not found");
  db.subscriptions[idx] = { ...db.subscriptions[idx], ...fields };
  await writeDb(db);
  return db.subscriptions[idx];
}

export async function listSubscriptionsPaginated(page = 1, perPage = 20) {
  const db = await readDb();
  const all = db.subscriptions || [];
  const start = (page - 1) * perPage;
  const data = all.slice(start, start + perPage);
  return { total: all.length, page, perPage, data };
}

export async function getVendorHistory(id: string, offset = 0, limit = 20) {
  const db = await readDb();
  const v = (db.vendors || []).find((x: Vendor) => x.id === id);
  if (!v) throw new Error("Vendor not found");
  const history = v.history || [];
  const sliced = history.slice(offset, offset + limit);
  return { total: history.length, offset, limit, data: sliced };
}

export async function countTendersByMonth(months = 12) {
  const db = await readDb();
  const now = new Date();
  const map: Record<string, number> = {};
  for (let i = 0; i < months; i++) {
    const d = new Date(now.getFullYear(), now.getMonth() - i, 1);
    const key = `${d.getFullYear()}-${String(d.getMonth() + 1).padStart(2, "0")}`;
    map[key] = 0;
  }
  (db.tenders || []).forEach((t: Tender) => {
    const c = t.createdAt ? new Date(t.createdAt) : null;
    if (!c) return;
    const key = `${c.getFullYear()}-${String(c.getMonth() + 1).padStart(2, "0")}`;
    if (map[key] !== undefined) map[key] = (map[key] || 0) + 1;
  });
  // return as ordered array newest-first
  const out = Object.keys(map).sort().map(k => ({ month: k, count: map[k] }));
  return out;
}

export async function getTopVendors(limit = 10) {
  const db = await readDb();
  const vendors = db.vendors || [];
  const ranked = vendors
    .map((v: Vendor) => ({ id: v.id, name: v.name, historyCount: (v.history || []).length, createdAt: v.createdAt }))
    .sort((a: any, b: any) => (b.historyCount - a.historyCount) || (new Date(b.createdAt).getTime() - new Date(a.createdAt).getTime()))
    .slice(0, limit);
  return ranked;
}

// Invoices
export async function createInvoice(payload: Partial<Invoice>): Promise<Invoice> {
  const db = await readDb();
  const id = `inv-${Date.now()}`;
  const registrationDate = payload.registrationDate || new Date().toISOString();
  const dueDate = payload.dueDate || new Date(new Date(registrationDate).getTime() + 14 * 24 * 3600 * 1000).toISOString();
  const inv: Invoice = { id, userId: payload.userId || "unknown", amount: payload.amount || 0, registrationDate, dueDate, status: payload.status || "pending", meta: payload.meta || {} };
  db.invoices = db.invoices || [];
  db.invoices.push(inv);
  await writeDb(db);
  return inv;
}

export async function listInvoices(): Promise<Invoice[]> {
  const db = await readDb();
  return db.invoices || [];
}

export async function getInvoice(id: string): Promise<Invoice | null> {
  const db = await readDb();
  const inv = (db.invoices || []).find((x: Invoice) => x.id === id);
  return inv || null;
}

export async function listInvoicesByUser(userId: string): Promise<Invoice[]> {
  const db = await readDb();
  return (db.invoices || []).filter((i: Invoice) => i.userId === userId);
}

// Buyers (registration requiring admin approval)
export async function createBuyer(payload: any): Promise<any> {
  const db = await readDb();
  const id = `b-${Date.now()}`;
  const buyer = { id, status: "pending", createdAt: new Date().toISOString(), ...payload };
  db.buyers = db.buyers || [];
  db.buyers.push(buyer);
  await writeDb(db);
  return buyer;
}

export async function approveBuyer(id: string): Promise<any> {
  const db = await readDb();
  const b = (db.buyers || []).find((x: any) => x.id === id);
  if (!b) throw new Error("Buyer not found");
  b.status = "approved";
  b.approvedAt = new Date().toISOString();
  await writeDb(db);
  return b;
}

// Users count helper
export async function countUsers(): Promise<number> {
  const db = await readDb();
  return (db.users || []).length;
}

// Tenders count helper
export async function countTenders(): Promise<number> {
  const db = await readDb();
  return (db.tenders || []).length;
}

// Status counts
export async function countTenderStatus(): Promise<Record<string, number>> {
  const db = await readDb();
  const tally: Record<string, number> = {};
  (db.tenders || []).forEach((t: Tender) => {
    const s = t.status || "unknown";
    tally[s] = (tally[s] || 0) + 1;
  });
  return tally;
}

// Categories and subcategories derived from tenders
export async function listCategories() {
  const db = await readDb();
  const items: Record<string, Set<string>> = {};
  (db.tenders || []).forEach((t: Tender) => {
    const cat = t.category || "Uncategorized";
    const sub = t.subcategory || "";
    items[cat] = items[cat] || new Set<string>();
    if (sub) items[cat].add(sub);
  });
  const out = Object.keys(items).map(cat => ({ category: cat, subcategories: Array.from(items[cat]) }));
  return out;
}

// Update a tender with arbitrary fields and persist
export async function updateTender(id: string, fields: Partial<Tender>) {
  const db = await readDb();
  db.tenders = db.tenders || [];
  const idx = db.tenders.findIndex((t: Tender) => t.id === id);
  if (idx === -1) throw new Error("Tender not found");
  db.tenders[idx] = { ...db.tenders[idx], ...fields, updatedAt: new Date().toISOString() };
  await writeDb(db);
  return db.tenders[idx];
}
