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
