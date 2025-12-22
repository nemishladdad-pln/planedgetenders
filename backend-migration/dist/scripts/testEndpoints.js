"use strict";
var __importDefault = (this && this.__importDefault) || function (mod) {
    return (mod && mod.__esModule) ? mod : { "default": mod };
};
Object.defineProperty(exports, "__esModule", { value: true });
const axios_1 = __importDefault(require("axios"));
const dotenv_1 = __importDefault(require("dotenv"));
dotenv_1.default.config();
const BASE = `http://localhost:${process.env.PORT || 4000}`;
async function run() {
    try {
        console.log("Health ->", (await axios_1.default.get(`${BASE}/health`)).data);
        console.log("/api/tenders ->", (await axios_1.default.get(`${BASE}/api/tenders`)).status);
        console.log("/api/tenders/search?q=test ->", (await axios_1.default.get(`${BASE}/api/tenders/search?q=test`)).status);
        // test activity (should fail without Admin role)
        try {
            await axios_1.default.get(`${BASE}/api/tenders/activity`);
        }
        catch (err) {
            console.log("/api/tenders/activity (no role) ->", err.response?.status || err.message);
        }
        // request activity with header
        const act = await axios_1.default.get(`${BASE}/api/tenders/activity`, { headers: { "x-role": "Admin" } });
        console.log("/api/tenders/activity (Admin) ->", act.data?.total ?? 0);
    }
    catch (err) {
        console.error("Test failed:", err.message);
        process.exit(1);
    }
}
run();
