const AUTH_KEY = "hp_auth_v1";

export function setAuth({ email, password, role }) {
  localStorage.setItem(AUTH_KEY, JSON.stringify({ email, password, role }));
}

export function getAuth() {
  try {
    const raw = localStorage.getItem(AUTH_KEY);
    if (!raw) return null;
    const parsed = JSON.parse(raw);
    if (!parsed?.email || !parsed?.password) return null;
    return parsed;
  } catch {
    return null;
  }
}

export function clearAuth() {
  localStorage.removeItem(AUTH_KEY);
}

export function getBasicAuthHeader() {
  const auth = getAuth();
  if (!auth) return null;
  const token = btoa(`${auth.email}:${auth.password}`);
  return `Basic ${token}`;
}




