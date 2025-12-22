import axios from "axios";
import dotenv from "dotenv";
dotenv.config();

const BASE = `http://localhost:${process.env.PORT || 4000}`;

async function run() {
  try {
    console.log("Health ->", (await axios.get(`${BASE}/health`)).data);
    console.log("/api/tenders ->", (await axios.get(`${BASE}/api/tenders`)).status);
    console.log("/api/tenders/search?q=test ->", (await axios.get(`${BASE}/api/tenders/search?q=test`)).status);

    // test activity (should fail without Admin role)
    try {
      await axios.get(`${BASE}/api/tenders/activity`);
    } catch (err: any) {
      console.log("/api/tenders/activity (no role) ->", err.response?.status || err.message);
    }

    // request activity with header
    const act = await axios.get(`${BASE}/api/tenders/activity`, { headers: { "x-role": "Admin" } });
    console.log("/api/tenders/activity (Admin) ->", act.data?.total ?? 0);
  } catch (err: any) {
    console.error("Test failed:", err.message);
    process.exit(1);
  }
}

run();
