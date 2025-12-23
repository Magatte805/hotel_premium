import { useEffect, useMemo, useState } from "react";
import { apiFetch } from "../../utils/api";

function nightsBetween(start, end) {
  const a = new Date(start);
  const b = new Date(end);
  const diff = (b - a) / (1000 * 60 * 60 * 24);
  return Math.max(1, Math.round(diff));
}

export default function ClientDashboard() {
  const [loading, setLoading] = useState(true);
  const [error, setError] = useState("");
  const [reservations, setReservations] = useState([]);
  const [query, setQuery] = useState("");
  const [appliedQuery, setAppliedQuery] = useState("");
  const [expandedId, setExpandedId] = useState(null);

  // Rooms/hotels (quick booking widget on dashboard)
  const [hotelsLoading, setHotelsLoading] = useState(true);
  const [hotels, setHotels] = useState([]);
  const [roomsByHotel, setRoomsByHotel] = useState({});
  const [cityQuery, setCityQuery] = useState("");
  const [startDate, setStartDate] = useState("");
  const [endDate, setEndDate] = useState("");

  const loadReservations = async () => {
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
    loadReservations();
  }, []);

  const loadHotels = async () => {
    setHotelsLoading(true);
    const { res, data } = await apiFetch("/client/hotels", { method: "GET" });
    if (res.ok) {
      setHotels(Array.isArray(data) ? data : []);
    }
    setHotelsLoading(false);
  };

  useEffect(() => {
    loadHotels();
  }, []);

  const filteredHotels = useMemo(() => {
    const q = cityQuery.trim().toLowerCase();
    if (!q) return hotels;
    return hotels.filter(
      (h) =>
        (h.city || "").toLowerCase().includes(q) ||
        (h.name || "").toLowerCase().includes(q)
    );
  }, [hotels, cityQuery]);

  const ensureRoomsLoaded = async (hotelId) => {
    if (roomsByHotel[hotelId]) return;
    const { res, data } = await apiFetch(`/client/hotels/${hotelId}/rooms`, {
      method: "GET",
    });
    if (!res.ok) {
      setError(data?.error || "Impossible de charger les chambres.");
      return;
    }
    setRoomsByHotel((prev) => ({ ...prev, [hotelId]: Array.isArray(data) ? data : [] }));
  };

  const reserve = async (roomId) => {
    setError("");
    if (!startDate || !endDate) {
      setError("Veuillez choisir une date de début et une date de fin.");
      return;
    }
    const { res, data } = await apiFetch("/client/reservations", {
      method: "POST",
      body: JSON.stringify({ room_id: roomId, startDate, endDate }),
    });
    if (res.status === 201) {
      setError("Réservation créée !");
      await loadReservations();
      return;
    }
    setError(data?.error || "Erreur lors de la réservation.");
  };

  const filtered = useMemo(() => {
    const q = appliedQuery.trim().toLowerCase();
    if (!q) return reservations;
    return reservations.filter((r) => {
      const hotel = r.hotel || {};
      const room = r.room || {};
      return (
        String(room.number || "").toLowerCase().includes(q) ||
        String(hotel.name || "").toLowerCase().includes(q) ||
        String(hotel.city || "").toLowerCase().includes(q) ||
        String(r.status || "").toLowerCase().includes(q)
      );
    });
  }, [reservations, appliedQuery]);

  const cancel = async (id) => {
    setError("");
    const ok = confirm("Annuler cette réservation ?");
    if (!ok) return;
    const { res, data } = await apiFetch(`/client/reservations/${id}`, { method: "DELETE" });
    if (res.status === 204) {
      await loadReservations();
      return;
    }
    setError(data?.error || "Impossible d’annuler.");
  };

  return (
    <div>
      <header className="pageHeader">
        <div>
          <h1 className="pageTitle">Mon espace client</h1>
          <p className="subTitle">Recherchez, consultez et annulez vos réservations.</p>
        </div>

        <div className="toolbar">
          <input
            className="input"
            placeholder="Rechercher"
            value={query}
            onChange={(e) => setQuery(e.target.value)}
            onKeyDown={(e) => {
              if (e.key === "Enter") {
                e.preventDefault();
                setAppliedQuery(query);
              }
              if (e.key === "Escape") {
                e.preventDefault();
                setQuery("");
                setAppliedQuery("");
              }
            }}
          />
        </div>
      </header>

      {error && <div style={{ color: "red", marginBottom: 10 }}>{error}</div>}
      {loading && <div className="muted">Chargement…</div>}

      {/* Quick booking section (rooms directly on dashboard) */}
      <div className="card" style={{ marginBottom: 14 }}>
        <div className="cardTop">
          <div>
            <h3 className="cardTitle">Réserver une chambre</h3>
            <div className="muted">Choisissez des dates puis réservez directement.</div>
          </div>
          
        </div>

        <div className="toolbar" style={{ marginBottom: 10 }}>
          <input
            className="input"
            placeholder="Ville / hôtel"
            value={cityQuery}
            onChange={(e) => setCityQuery(e.target.value)}
          />
          <input
            className="input"
            type="date"
            value={startDate}
            onChange={(e) => setStartDate(e.target.value)}
            title="Date de début"
          />
          <input
            className="input"
            type="date"
            value={endDate}
            onChange={(e) => setEndDate(e.target.value)}
            title="Date de fin"
          />
        </div>

        {hotelsLoading ? (
          <div className="muted">Chargement des hôtels…</div>
        ) : (
          <div className="rows">
            {filteredHotels.slice(0, 6).map((h) => {
              const list = roomsByHotel[h.id] || [];
              return (
                <div key={h.id} className="row" style={{ alignItems: "center" }}>
                  <span>
                    <b>{h.name}</b> — {h.city}
                  </span>
                  <span>
                    <button className="btn" onClick={() => ensureRoomsLoaded(h.id)}>
                      Chambres
                    </button>
                  </span>
                  {list.length > 0 && (
                    <div style={{ width: "100%", marginTop: 8 }}>
                      {list.slice(0, 4).map((r) => (
                        <div
                          key={r.id}
                          className="row"
                          style={{ alignItems: "center", marginTop: 6 }}
                        >
                          <span>
                            Chambre <b>{r.number}</b>
                          </span>
                          <span>
                            <b>{r.pricePerNight} €</b> / nuit{" "}
                            <button
                              className="btnPrimary"
                              style={{ marginLeft: 10 }}
                              onClick={() => reserve(r.id)}
                            >
                              Réserver
                            </button>
                          </span>
                        </div>
                      ))}
                    </div>
                  )}
                </div>
              );
            })}
          </div>
        )}
      </div>

      <section className="grid">
        {filtered.map((res) => {
          const room = res.room;
          const hotel = res.hotel;
          const nights = nightsBetween(res.startDate, res.endDate);
          const total = room ? nights * (room.pricePerNight || 0) : 0;

          const badgeClass = (res.status || "").toLowerCase().includes("confirm")
            ? "badge ok"
            : "badge progress";

          return (
            <article className="card" key={res.id}>
              <div className="cardTop">
                <div>
                  <h3 className="cardTitle">{hotel?.name ?? "Hôtel"}</h3>
                  <div className="muted">
                    Chambre {room?.number ?? "—"} • {hotel?.city ?? "—"}
                  </div>
                </div>

                <span className={badgeClass}>{res.status}</span>
              </div>

              <div className="rows">
                <div className="row">
                  <span>Dates</span>
                  <span>
                    {res.startDate} → {res.endDate}
                  </span>
                </div>

                <div className="row">
                  <span>Nuits</span>
                  <span>{nights}</span>
                </div>

                <div className="row">
                  <span>Prix/nuit</span>
                  <span>{room?.pricePerNight ?? "—"} €</span>
                </div>

                <div className="row">
                  <span>Total</span>
                  <span>
                    <b>{total} €</b>
                  </span>
                </div>
              </div>

              {expandedId === res.id && (
                <div className="rows" style={{ marginTop: 10 }}>
                  <div className="row">
                    <span>Adresse</span>
                    <span>{hotel?.address ?? "—"}</span>
                  </div>
                </div>
              )}

              <div style={{ marginTop: 14, display: "flex", gap: 10 }}>
                <button className="btn" onClick={() => setExpandedId(expandedId === res.id ? null : res.id)}>
                  {expandedId === res.id ? "Masquer détails" : "Voir détails"}
                </button>
                <button className="btnPrimary" onClick={() => cancel(res.id)}>
                  Annuler
                </button>
              </div>
            </article>
          );
        })}
      </section>
    </div>
  );
}
