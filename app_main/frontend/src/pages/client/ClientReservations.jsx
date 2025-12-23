import { useEffect, useState } from "react";
import { apiFetch } from "../../utils/api";

export default function ClientReservations() {
  const [loading, setLoading] = useState(true);
  const [error, setError] = useState("");
  const [reservations, setReservations] = useState([]);

  const load = async () => {
    setLoading(true);
    setError("");
    const { res, data } = await apiFetch("/client/reservations", { method: "GET" });
    if (!res.ok) {
      setError(data?.error || "Impossible de charger vos réservations.");
      setLoading(false);
      return;
    }
    setReservations(Array.isArray(data) ? data : []);
    setLoading(false);
  };

  useEffect(() => {
    load();
  }, []);

  const cancel = async (id) => {
    setError("");
    const ok = confirm("Annuler cette réservation ?");
    if (!ok) return;
    const { res, data } = await apiFetch(`/client/reservations/${id}`, { method: "DELETE" });
    if (res.status === 204) {
      await load();
      return;
    }
    setError(data?.error || "Impossible d’annuler.");
  };

  return (
    <div>
      <header className="pageHeader">
        <div>
          <h1 className="pageTitle">Mes réservations</h1>
          <p className="subTitle">Retrouve tes réservations, dates et détails.</p>
        </div>
      </header>

      {error && <div style={{ color: "red", marginBottom: 10 }}>{error}</div>}
      {loading && <div className="muted">Chargement…</div>}

      <div className="grid">
        {reservations.map((r) => {
          const room = r.room;
          const hotel = r.hotel;
          const badgeClass =
            (r.status || "").toLowerCase().includes("confirm")
              ? "badge ok"
              : "badge progress";

          return (
            <div className="card" key={r.id}>
              <div className="cardTop">
                <div>
                  <h3 className="cardTitle">{hotel?.name ?? "Hôtel"}</h3>
                  <div className="muted">
                    Chambre {room?.number ?? "—"} • {hotel?.city ?? "—"}
                  </div>
                </div>

                <span className={badgeClass}>{r.status}</span>
              </div>

              <div className="rows">
                <div className="row">
                  <span>Dates</span>
                  <span>
                    {r.startDate} → {r.endDate}
                  </span>
                </div>
                <div className="row">
                  <span>Hôtel</span>
                  <span>{hotel?.address ?? "—"}</span>
                </div>
                <div className="row">
                  <span>Prix/nuit</span>
                  <span>{room?.pricePerNight ?? "—"} €</span>
                </div>
              </div>

              <div className="cta" style={{ marginTop: 12 }}>
                <button className="btn" onClick={() => window.alert(JSON.stringify(r, null, 2))}>
                  Voir détails
                </button>
                <button className="btnPrimary" onClick={() => cancel(r.id)}>
                  Annuler
                </button>
              </div>
            </div>
          );
        })}
      </div>
    </div>
  );
}
