"use strict";
var __importDefault = (this && this.__importDefault) || function (mod) {
    return (mod && mod.__esModule) ? mod : { "default": mod };
};
Object.defineProperty(exports, "__esModule", { value: true });
exports.listTenders = listTenders;
exports.createTender = createTender;
exports.getTender = getTender;
exports.reTender = reTender;
exports.uploadTenderBudget = uploadTenderBudget;
exports.listVendors = listVendors;
exports.createVendor = createVendor;
exports.appendVendorHistory = appendVendorHistory;
exports.createSubscription = createSubscription;
exports.listSubscriptions = listSubscriptions;
exports.createInvoice = createInvoice;
exports.listInvoices = listInvoices;
exports.createBuyer = createBuyer;
exports.approveBuyer = approveBuyer;
exports.countUsers = countUsers;
exports.countTenders = countTenders;
exports.countTenderStatus = countTenderStatus;
const fs_1 = __importDefault(require("fs"));
const path_1 = __importDefault(require("path"));
const dotenv_1 = __importDefault(require("dotenv"));
dotenv_1.default.config();
const DB_FILE = process.env.DATA_STORE_FILE || path_1.default.resolve(__dirname, "../../data/db.json");
async function ensureDb() {
    await fs_1.default.promises.mkdir(path_1.default.dirname(DB_FILE), { recursive: true });
    try {
        await fs_1.default.promises.access(DB_FILE);
    }
    catch {
        const initial = { tenders: [], vendors: [], subscriptions: [], invoices: [], buyers: [], users: [] };
        await fs_1.default.promises.writeFile(DB_FILE, JSON.stringify(initial, null, 2), "utf8");
    }
}
async function readDb() {
    await ensureDb();
    const raw = await fs_1.default.promises.readFile(DB_FILE, "utf8");
    return JSON.parse(raw || "{}");
}
async function writeDb(db) {
    await fs_1.default.promises.writeFile(DB_FILE, JSON.stringify(db, null, 2), "utf8");
}
// Tenders
async function listTenders() {
    const db = await readDb();
    return db.tenders || [];
}
async function createTender(payload) {
    const db = await readDb();
    const id = payload.id || `t-${Date.now()}`;
    const now = new Date().toISOString();
    const t = { id, ...payload, title: payload.title || "Untitled Tender", createdAt: now, updatedAt: now, status: payload.status || "Active" };
    db.tenders = db.tenders || [];
    db.tenders.push(t);
    await writeDb(db);
    return t;
}
async function getTender(id) {
    const db = await readDb();
    const found = (db.tenders || []).find((x) => x.id === id);
    return found || null;
}
async function reTender(id, payload) {
    const original = await getTender(id);
    const newTender = await createTender({ title: `${original?.title || "Re-tender"} (re)`, ...payload, originalId: id });
    return newTender;
}
async function uploadTenderBudget(id, budget) {
    const db = await readDb();
    db.tenders = db.tenders || [];
    const idx = db.tenders.findIndex((t) => t.id === id);
    if (idx === -1)
        throw new Error("Tender not found");
    db.tenders[idx].budget = budget;
    db.tenders[idx].updatedAt = new Date().toISOString();
    await writeDb(db);
    return db.tenders[idx];
}
// Vendors
async function listVendors() {
    const db = await readDb();
    return db.vendors || [];
}
async function createVendor(payload) {
    const db = await readDb();
    const id = payload.id || `v-${Date.now()}`;
    const now = new Date().toISOString();
    const v = { id, name: payload.name || "Unknown", history: payload.history || [], partial: !!payload.partial, createdAt: now, ...payload };
    db.vendors = db.vendors || [];
    db.vendors.push(v);
    await writeDb(db);
    return v;
}
async function appendVendorHistory(id, entry) {
    const db = await readDb();
    const v = (db.vendors || []).find((x) => x.id === id);
    if (!v)
        throw new Error("Vendor not found");
    v.history = v.history || [];
    v.history.push({ ts: new Date().toISOString(), ...entry });
    await writeDb(db);
    return v;
}
// Subscriptions
async function createSubscription(sub) {
    const db = await readDb();
    const id = `s-${Date.now()}`;
    const start = sub.start || new Date().toISOString();
    const end = sub.end || new Date(new Date(start).setFullYear(new Date(start).getFullYear() + 1)).toISOString();
    const s = { id, userId: sub.userId || "unknown", type: sub.type || "yearly", start, end, status: sub.status || "active" };
    db.subscriptions = db.subscriptions || [];
    db.subscriptions.push(s);
    await writeDb(db);
    return s;
}
async function listSubscriptions() {
    const db = await readDb();
    return db.subscriptions || [];
}
// Invoices
async function createInvoice(payload) {
    const db = await readDb();
    const id = `inv-${Date.now()}`;
    const registrationDate = payload.registrationDate || new Date().toISOString();
    const dueDate = payload.dueDate || new Date(new Date(registrationDate).getTime() + 14 * 24 * 3600 * 1000).toISOString();
    const inv = { id, userId: payload.userId || "unknown", amount: payload.amount || 0, registrationDate, dueDate, status: payload.status || "pending", meta: payload.meta || {} };
    db.invoices = db.invoices || [];
    db.invoices.push(inv);
    await writeDb(db);
    return inv;
}
async function listInvoices() {
    const db = await readDb();
    return db.invoices || [];
}
// Buyers (registration requiring admin approval)
async function createBuyer(payload) {
    const db = await readDb();
    const id = `b-${Date.now()}`;
    const buyer = { id, status: "pending", createdAt: new Date().toISOString(), ...payload };
    db.buyers = db.buyers || [];
    db.buyers.push(buyer);
    await writeDb(db);
    return buyer;
}
async function approveBuyer(id) {
    const db = await readDb();
    const b = (db.buyers || []).find((x) => x.id === id);
    if (!b)
        throw new Error("Buyer not found");
    b.status = "approved";
    b.approvedAt = new Date().toISOString();
    await writeDb(db);
    return b;
}
// Users count helper
async function countUsers() {
    const db = await readDb();
    return (db.users || []).length;
}
// Tenders count helper
async function countTenders() {
    const db = await readDb();
    return (db.tenders || []).length;
}
// Status counts
async function countTenderStatus() {
    const db = await readDb();
    const tally = {};
    (db.tenders || []).forEach((t) => {
        const s = t.status || "unknown";
        tally[s] = (tally[s] || 0) + 1;
    });
    return tally;
}
