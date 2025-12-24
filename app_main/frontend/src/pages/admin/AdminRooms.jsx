import { useEffect, useState } from "react";
import { apiFetch } from "../../utils/api";
import { useSearchParams } from "react-router-dom";

function cls(c) {
  const v = (c || "").toLowerCase();
  if (v.includes("suite")) return "suite";
  if (v.includes("deluxe")) return "deluxe";
  return "standard";
}

export default function AdminRooms() {
  const [searchParams] = useSearchParams();
  const [loading, setLoading] = useState(true);
  const [error, setError] = useState("");
  const [saving, setSaving] = useState(false);
  const [rooms, setRooms] = useState([]);
  const [hotels, setHotels] = useState([]);

  const [hotelId, setHotelId] = useState("");
  const [number, setNumber] = useState("");
  const [pricePerNight, setPricePerNight] = useState("");

  const load = async () => {
    setLoading(true);
    setError("");

    const hotelsRes = await apiFetch("/admin/hotels", { method: "GET" });
    if (!hotelsRes.res.ok) {
      setError(hotelsRes.data?.error || "Impossible de charger les hôtels (admin).");
      setLoading(false);
      return;
    }
    setHotels(Array.isArray(hotelsRes.data) ? hotelsRes.data : []);

    const roomsRes = await apiFetch("/admin/rooms", { method: "GET" });
    if (!roomsRes.res.ok) {
      setError(roomsRes.data?.error || "Impossible de charger les chambres (admin).");
      setLoading(false);
      return;
    }
    setRooms(Array.isArray(roomsRes.data) ? roomsRes.data : []);
    setLoading(false);
  };

  useEffect(() => {
    load();
  }, []);

  useEffect(() => {
    const q = searchParams.get("hotelId");
    if (q) setHotelId(String(q));
  }, [searchParams]);

  const addRoom = async (e) => {
    e.preventDefault();
    setError("");
    if (!hotelId || !number || !pricePerNight) {
      setError("Veuillez remplir hôtel, numéro et prix.");
      return;
    }
    if (saving) return;
    setSaving(true);

    const { res, data } = await apiFetch("/admin/rooms", {
      method: "POST",
      body: JSON.stringify({
        hotel_id: Number(hotelId),
        number: Number(number),
        pricePerNight: Number(pricePerNight),
      }),
    });
    setSaving(false);
    if (res.status === 201) {
      setHotelId("");
      setNumber("");
      setPricePerNight("");
      await load();
      return;
    }
    setError(data?.error || "Erreur lors de l’ajout de la chambre.");
  };

  return (
    <div>
      <header className="pageHeader">
        <div>
          <h1 className="pageTitle">Admin </h1>
          <p className="subTitle">Créer et consulter les chambres.</p>
        </div>
      </header>

      {error && <div style={{ color: "red", marginBottom: 10 }}>{error}</div>}
      {loading && <div className="muted">Chargement…</div>}

      <div className="card" style={{ marginBottom: 14 }}>
        <h3 className="cardTitle" style={{ marginBottom: 10 }}>
          Ajouter une chambre
        </h3>
        <form className="form" onSubmit={addRoom}>
          <div className="field">
            <div className="label">Hôtel</div>
            <select
              className="control"
              value={hotelId}
              onChange={(e) => setHotelId(e.target.value)}
            >
              <option value="">— Choisir —</option>
              {hotels.map((h) => (
                <option key={h.id} value={h.id}>
                  {h.name} — {h.city}
                </option>
              ))}
            </select>
          </div>

          <div className="field">
            <div className="label">Numéro</div>
            <input
              className="control"
              value={number}
              onChange={(e) => setNumber(e.target.value)}
              inputMode="numeric"
              placeholder="Ex: 101"
            />
          </div>

          <div className="field">
            <div className="label">Prix / nuit</div>
            <input
              className="control"
              value={pricePerNight}
              onChange={(e) => setPricePerNight(e.target.value)}
              inputMode="decimal"
              placeholder="Ex: 120"
            />
          </div>

          <div className="cta">
            <button className="btnPrimary" disabled={saving}>
              {saving && <span className="spinner sm white" />}
              {saving ? "Ajout…" : "Ajouter"}
            </button>
          </div>
        </form>
      </div>

      <div className="card">
        <table className="table">
          <thead>
            <tr>
              <th>Num</th>
              <th>Hôtel</th>
              <th>Prix/nuit</th>
            </tr>
          </thead>
          <tbody>
            {rooms.map((r) => {
              const hotel = r.hotel || hotels.find((h) => h.id === r.hotelId);
              return (
                <tr key={r.id}>
                  <td>
                    <b>{r.number}</b>
                  </td>
                  <td>{hotel?.name ?? "—"}</td>
                  <td className="price">{r.pricePerNight} €</td>
                </tr>
              );
            })}
          </tbody>
        </table>
      </div>
    </div>
  );
}
