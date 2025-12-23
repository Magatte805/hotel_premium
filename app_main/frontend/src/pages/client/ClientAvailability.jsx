import { useEffect, useMemo, useState } from "react";
import { apiFetch } from "../../utils/api";

export default function ClientAvailability() {
  const [loading, setLoading] = useState(true);
  const [error, setError] = useState("");
  const [hotels, setHotels] = useState([]);
  const [roomsByHotel, setRoomsByHotel] = useState({});
  const [startDate, setStartDate] = useState("");
  const [endDate, setEndDate] = useState("");
  const [cityQuery, setCityQuery] = useState("");

  useEffect(() => {
    let mounted = true;
    (async () => {
      setLoading(true);
      setError("");
      const { res, data } = await apiFetch("/client/hotels", { method: "GET" });
      if (!mounted) return;
      if (!res.ok) {
        setError(data?.error || "Impossible de charger les hôtels (connectez-vous).");
        setLoading(false);
        return;
      }
      setHotels(Array.isArray(data) ? data : []);
      setLoading(false);
    })();
    return () => {
      mounted = false;
    };
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
      return;
    }
    setError(data?.error || "Erreur lors de la réservation.");
  };

  return (
    <div>
      <header className="pageHeader">
        <div>
          <h1 className="pageTitle">Disponibilités</h1>
          <p className="subTitle">Choisissez des dates puis réservez une chambre.</p>
        </div>

        <div className="toolbar">
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
      </header>

      {error && <div style={{ color: "red", marginBottom: 10 }}>{error}</div>}
      {loading && <div className="muted">Chargement…</div>}

      <div className="grid">
        {filteredHotels.map((h) => {
          const rooms = roomsByHotel[h.id] || [];
          return (
            <div className="card" key={h.id}>
              <div className="cardTop">
                <div>
                  <h3 className="cardTitle">
                    {h.name} — {h.city}
                  </h3>
                  <div className="muted">{h.address}</div>
                </div>
                <button className="btn" onClick={() => ensureRoomsLoaded(h.id)}>
                  Voir chambres
                </button>
              </div>

              {rooms.length === 0 ? (
                <div className="muted">Aucune chambre chargée.</div>
              ) : (
                <div className="rows">
                  {rooms.map((r) => (
                    <div className="row" key={r.id} style={{ alignItems: "center" }}>
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
    </div>
  );
}
