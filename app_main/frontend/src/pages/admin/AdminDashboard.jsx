import { useEffect, useMemo, useState } from "react";
import { useNavigate } from "react-router-dom";
import { apiFetch } from "../../utils/api";

function downloadJson(filename, data) {
  const blob = new Blob([JSON.stringify(data, null, 2)], { type: "application/json" });
  const url = URL.createObjectURL(blob);
  const a = document.createElement("a");
  a.href = url;
  a.download = filename;
  a.click();
  URL.revokeObjectURL(url);
}

export default function AdminDashboard() {
  const navigate = useNavigate();
  const [loading, setLoading] = useState(true);
  const [error, setError] = useState("");
  const [query, setQuery] = useState("");

  const [hotels, setHotels] = useState([]);
  const [rooms, setRooms] = useState([]);
  const [reservations, setReservations] = useState([]);

  const load = async () => {
    setLoading(true);
    setError("");

    const h = await apiFetch("/admin/hotels", { method: "GET" });
    if (!h.res.ok) {
      setError(h.data?.error || "Impossible de charger les hôtels (admin).");
      setLoading(false);
      return;
    }
    setHotels(Array.isArray(h.data) ? h.data : []);

    const r = await apiFetch("/admin/rooms", { method: "GET" });
    if (!r.res.ok) {
      setError(r.data?.error || "Impossible de charger les chambres (admin).");
      setLoading(false);
      return;
    }
    setRooms(Array.isArray(r.data) ? r.data : []);

    const resv = await apiFetch("/admin/reservations", { method: "GET" });
    if (resv.res.ok) {
      setReservations(Array.isArray(resv.data) ? resv.data : []);
    } else {
      setReservations([]);
    }

    setLoading(false);
  };

  useEffect(() => {
    load();
  }, []);

  const filteredHotels = useMemo(() => {
    const q = query.trim().toLowerCase();
    if (!q) return hotels;
    return hotels.filter(
      (h) =>
        (h.name || "").toLowerCase().includes(q) ||
        (h.city || "").toLowerCase().includes(q) ||
        (h.address || "").toLowerCase().includes(q)
    );
  }, [hotels, query]);

  const totalHotels = hotels.length;
  const totalRooms = rooms.length;
  const avgPrice =
    totalRooms === 0
      ? 0
      : Math.round(rooms.reduce((s, x) => s + (x.pricePerNight || 0), 0) / totalRooms);

  const topCity = useMemo(() => {
    if (!hotels.length) return "—";
    const counts = hotels.reduce((acc, h) => {
      acc[h.city] = (acc[h.city] || 0) + 1;
      return acc;
    }, {});
    return Object.entries(counts).sort((a, b) => b[1] - a[1])[0][0];
  }, [hotels]);

  const activeReservationByRoomId = useMemo(() => {
    const map = new Map();
    const now = new Date();
    for (const r of reservations) {
      const roomId = r?.room?.id;
      if (!roomId) continue;
      const status = String(r.status || "").toLowerCase();
      if (!status.includes("confirm")) continue;
      if (!r.startDate || !r.endDate) continue;
      const start = new Date(r.startDate);
      const end = new Date(r.endDate);
      if (start <= now && now <= end) {
        // keep first match (reservations are returned DESC)
        if (!map.has(roomId)) map.set(roomId, r);
      }
    }
    return map;
  }, [reservations]);

  const editRoomPrice = async (room) => {
    const val = prompt("Nouveau prix / nuit ?", String(room.pricePerNight ?? ""));
    if (val === null) return;
    const price = Number(val);
    if (!Number.isFinite(price) || price <= 0) {
      alert("Prix invalide.");
      return;
    }
    const { res, data } = await apiFetch(`/admin/rooms/${room.id}`, {
      method: "PATCH",
      body: JSON.stringify({ pricePerNight: price }),
    });
    if (!res.ok) {
      alert(data?.error || "Erreur lors de la modification.");
      return;
    }
    await load();
  };

  const editRoom = async (room) => {
    const nextNumberRaw = prompt("Nouveau numéro de chambre ?", String(room.number ?? ""));
    if (nextNumberRaw === null) return;
    const nextNumber = Number(nextNumberRaw);
    if (!Number.isFinite(nextNumber) || nextNumber <= 0) {
      alert("Numéro invalide.");
      return;
    }

    const nextPriceRaw = prompt("Nouveau prix / nuit ?", String(room.pricePerNight ?? ""));
    if (nextPriceRaw === null) return;
    const nextPrice = Number(nextPriceRaw);
    if (!Number.isFinite(nextPrice) || nextPrice <= 0) {
      alert("Prix invalide.");
      return;
    }

    const nextHotelRaw = prompt(
      "ID hôtel (laisser vide pour ne pas changer) ?",
      String(room.hotel?.id ?? "")
    );
    if (nextHotelRaw === null) return;

    const payload = { number: nextNumber, pricePerNight: nextPrice };
    if (String(nextHotelRaw).trim() !== "") payload.hotel_id = Number(nextHotelRaw);

    const { res, data } = await apiFetch(`/admin/rooms/${room.id}`, {
      method: "PATCH",
      body: JSON.stringify(payload),
    });
    if (!res.ok) {
      alert(data?.error || "Erreur lors de la modification.");
      return;
    }
    await load();
  };

  const editHotel = async (hotel) => {
    const nextName = prompt("Nom de l’hôtel ?", String(hotel.name ?? ""));
    if (nextName === null) return;
    const nextCity = prompt("Ville ?", String(hotel.city ?? ""));
    if (nextCity === null) return;
    const nextAddress = prompt("Adresse ?", String(hotel.address ?? ""));
    if (nextAddress === null) return;

    const { res, data } = await apiFetch(`/admin/hotels/${hotel.id}`, {
      method: "PATCH",
      body: JSON.stringify({ name: nextName, city: nextCity, address: nextAddress }),
    });
    if (!res.ok) {
      alert(data?.error || "Erreur lors de la modification.");
      return;
    }
    await load();
  };

  return (
    <div>
      <header className="pageHeader">
        <div>          <h1 className="pageTitle">Mon espace Admin </h1>
          <p className="subTitle">Vue globale (hôtels, chambres, occupation).</p>
        </div>

        <div className="toolbar">
          <input
            className="input"
            placeholder="Recherche"
            value={query}
            onChange={(e) => setQuery(e.target.value)}
          />
          <button
            className="btn"
            onClick={() => downloadJson("export-admin.json", { hotels, rooms, reservations })}
          >
            Exporter
          </button>
          <button className="btnPrimary" onClick={() => navigate("/admin/hotels")}>
            + Ajouter un hôtel
          </button>
        </div>
      </header>

      {error && <div style={{ color: "red", marginBottom: 10 }}>{error}</div>}
      {loading && <div className="muted">Chargement…</div>}

      <section className="kpis">
        <div className="kpi">
          <div className="label">Hôtels</div>
          <div className="value">{totalHotels}</div>
        </div>
        <div className="kpi">
          <div className="label">Chambres</div>
          <div className="value">{totalRooms}</div>
        </div>
        <div className="kpi">
          <div className="label">Prix moyen / nuit</div>
          <div className="value">{avgPrice} €</div>
        </div>
        <div className="kpi">
          <div className="label">Ville la plus présente</div>
          <div className="value">{topCity}</div>
        </div>
      </section>

      {filteredHotels.map((h) => {
        const hotelRooms = rooms.filter((r) => r.hotel?.id === h.id);
        return (
          <section className="hotelCard" key={h.id}>
            <div className="hotelHeader">
              <div>
                <h2>{h.name}</h2>
                <div className="meta">
                  {h.city} • {hotelRooms.length} chambre(s)
                </div>
                <div className="muted">{h.address}</div>
              </div>

              <div className="actions">
                <button className="btn" onClick={() => editHotel(h)}>
                  ✏️ Modifier hôtel
                </button>
                <button
                  className="btnPrimary"
                  onClick={() => navigate(`/admin/rooms?hotelId=${h.id}`)}
                >
                  + Ajouter chambre
                </button>
              </div>
            </div>

            <div className="tableWrap">
              <table className="table">
                <thead>
                  <tr>
                    <th style={{ width: 110 }}>Chambre</th>
                    <th style={{ width: 120 }}>Prix/nuit</th>
                    <th>Réservée par</th>
                    <th style={{ width: 180 }}>Actions</th>
                  </tr>
                </thead>
                <tbody>
                  {hotelRooms.map((r) => (
                    <tr key={r.id}>
                      <td>
                        <b>{r.number}</b>
                      </td>
                      <td className="price">{r.pricePerNight} €</td>
                      <td style={{ color: "var(--muted)" }}>
                        {(() => {
                          const resv = activeReservationByRoomId.get(r.id);
                          if (!resv?.user) return "Libre";
                          const u = resv.user;
                          const fullName = [u.firstName, u.lastName].filter(Boolean).join(" ").trim();
                          return `${fullName || u.email || "Client"} • ${u.phone || "—"}`;
                        })()}
                      </td>
                      <td>
                        <div className="smallActions">
                          <button className="iconBtn" onClick={() => editRoom(r)}>
                            ✏️ Modifier
                          </button>
                          <button
                            className="iconBtn"
                            onClick={() => navigate(`/admin/works?roomId=${r.id}`)}
                          >
                            🛠️ Travaux
                          </button>
                        </div>
                      </td>
                    </tr>
                  ))}

                  {hotelRooms.length === 0 && (
                    <tr>
                      <td colSpan="4" style={{ color: "var(--muted)" }}>
                        Aucune chambre pour cet hôtel.
                      </td>
                    </tr>
                  )}
                </tbody>
              </table>
            </div>
          </section>
        );
      })}
    </div>
  );
}
