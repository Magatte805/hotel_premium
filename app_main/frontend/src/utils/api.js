import API_URL from "../config";
import { getBasicAuthHeader, clearAuth } from "./auth";

export async function apiFetch(path, options = {}) {
  const headers = new Headers(options.headers || {});
  headers.set("Content-Type", headers.get("Content-Type") || "application/json");

  const authHeader = getBasicAuthHeader();
  if (authHeader) headers.set("Authorization", authHeader);

  const res = await fetch(`${API_URL}${path}`, {
    ...options,
    headers,
  });

  if (res.status === 401) {
    // credentials invalid/expired
    clearAuth();
  }

  let data = null;
  const text = await res.text();
  try {
    data = text ? JSON.parse(text) : null;
  } catch {
    data = text || null;
  }

  return { res, data };
}






