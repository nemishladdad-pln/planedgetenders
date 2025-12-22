import fs from "fs";
import path from "path";
import dotenv from "dotenv";
dotenv.config();

const LOG_FILE = process.env.ACTIVITY_LOG_FILE || path.resolve(__dirname, "../../data/activity.log");

export async function appendActivity(event: Record<string, any>) {
  const line = JSON.stringify({ ...event, ts: new Date().toISOString() }) + "\n";
  await fs.promises.mkdir(path.dirname(LOG_FILE), { recursive: true });
  await fs.promises.appendFile(LOG_FILE, line, "utf8");
}

export async function readActivities(limit = 200) {
  try {
    const data = await fs.promises.readFile(LOG_FILE, "utf8");
    const lines = data.trim().split("\n").filter(Boolean);
    const last = lines.slice(-limit).map(l => JSON.parse(l));
    return last;
  } catch (err) {
    return [];
  }
}
