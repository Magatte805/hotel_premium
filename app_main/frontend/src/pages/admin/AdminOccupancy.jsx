import { useEffect, useMemo, useState } from "react";
import { apiFetch } from "../../utils/api";

function parseYmdToLocalStart(ymd) {
  // Backend returns YYYY-MM-DD; Date.parse treats it as UTC which breaks "today" comparisons.
  if (!ymd || typeof ymd !== "string") return null;
  const m = /^(\d{4})-(\d{2})-(\d{2})$/.exec(ymd.trim());
  if (!m) return null;
  const y = Number(m[1]);
  const mo = Number(m[2]) - 1;
  const d = Number(m[3]);
  if (!Number.isFinite(y) || !Number.isFinite(mo) || !Number.isFinite(d)) return null;
  return new Date(y, mo, d, 0, 0, 0, 0);
}

function parseYmdToLocalEnd(ymd) {
  const start = parseYmdToLocalStart(ymd);
  if (!start) return null;
  return new Date(start.getFullYear(), start.getMonth(), start.getDate(), 23, 59, 59, 999);
}

function isActiveToday(res) {
  const status = (res.status || "").toLowerCase();
  if (!status.includes("confirm")) return false;
  if (!res.startDate || !res.endDate) return false;
  const now = new Date();
  const start = parseYmdToLocalStart(res.startDate);
  const end = parseYmdToLocalEnd(res.endDate);
  if (!start || !end) return false;
  return start <= now && now <= end;
}

export default function AdminOccupancy() {
  const [loading, setLoading] = useState(true);
  const [error, setError] = useState("");
  const [reservations, setReservations] = useState([]);

  const load = async () => {
    setLoading(true);
    setError("");
    const { res, data } = await apiFetch("/admin/reservations", { method: "GET" });
    if (!res.ok) {
      setError(data?.error || "Impossible de charger les réservations (admin).");
      setLoading(false);
      return;
    }
    setReservations(Array.isArray(data) ? data : []);
    setLoading(false);
  };

  useEffect(() => {
    load();
  }, []);

  const active = useMemo(() => reservations.filter(isActiveToday), [reservations]);

  return (
    <div>
      <header className="pageHeader">
        <div>
          <h1 className="pageTitle">Admin</h1>
          <p className="subTitle">Occupation (réservations confirmées en cours).</p>
        </div>
        <div className="toolbar">
          
        </div>
      </header>

      {error && <div style={{ color: "red", marginBottom: 10 }}>{error}</div>}
      {loading && <div className="muted">Chargement…</div>}

      {active.map((r) => (
        <div className="card" key={r.id}>
          <div className="cardTop">
            <div>
              <h3 className="cardTitle">
                {r.hotel?.name ?? "Hôtel"} — Chambre {r.room?.number ?? "—"}
              </h3>
              <div className="muted">
                {r.hotel?.city ?? "—"} • {r.startDate} → {r.endDate}
              </div>
              <div className="muted" style={{ marginTop: 6 }}>
                Client:{" "}
                <b>
                  {[r.user?.firstName, r.user?.lastName].filter(Boolean).join(" ") ||
                    r.user?.email ||
                    "—"}
                </b>
                {r.user?.phone ? ` • ${r.user.phone}` : ""}
              </div>
            </div>
            <span className="badge ok">Occupée</span>
          </div>
        </div>
      ))}

      {!loading && active.length === 0 && (
        <div className="card">
          <p className="muted">Aucune chambre occupée aujourd’hui.</p>
        </div>
      )}
    </div>
  );
}
