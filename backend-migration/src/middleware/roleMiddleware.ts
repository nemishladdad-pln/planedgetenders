import { Request, Response, NextFunction } from "express";

// Simple role middleware that reads X-Role header. This is additive and safe for migration.
// Replace with JWT/session checks tied to Laravel auth when ready.
export function requireRole(...allowed: string[]) {
  return (req: Request, res: Response, next: NextFunction) => {
    const role = (req.header("x-role") || "").toString();
    if (!role) return res.status(401).json({ error: "Missing role header (x-role)" });
    if (allowed.length && !allowed.includes(role)) {
      return res.status(403).json({ error: "Insufficient role" });
    }
    // attach role for downstream usage
    (req as any).userRole = role;
    next();
  };
}
