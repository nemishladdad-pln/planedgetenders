"use strict";
var __importDefault = (this && this.__importDefault) || function (mod) {
    return (mod && mod.__esModule) ? mod : { "default": mod };
};
Object.defineProperty(exports, "__esModule", { value: true });
exports.appendActivity = appendActivity;
exports.readActivities = readActivities;
const fs_1 = __importDefault(require("fs"));
const path_1 = __importDefault(require("path"));
const dotenv_1 = __importDefault(require("dotenv"));
dotenv_1.default.config();
const LOG_FILE = process.env.ACTIVITY_LOG_FILE || path_1.default.resolve(__dirname, "../../data/activity.log");
async function appendActivity(event) {
    const line = JSON.stringify({ ...event, ts: new Date().toISOString() }) + "\n";
    await fs_1.default.promises.mkdir(path_1.default.dirname(LOG_FILE), { recursive: true });
    await fs_1.default.promises.appendFile(LOG_FILE, line, "utf8");
}
async function readActivities(limit = 200) {
    try {
        const data = await fs_1.default.promises.readFile(LOG_FILE, "utf8");
        const lines = data.trim().split("\n").filter(Boolean);
        const last = lines.slice(-limit).map(l => JSON.parse(l));
        return last;
    }
    catch (err) {
        return [];
    }
}
