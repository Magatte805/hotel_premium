import { useEffect, useMemo, useState } from "react";
import { apiFetch } from "../../utils/api";
import { useNavigate } from "react-router-dom";

export default function AdminHotels() {
  const navigate = useNavigate();
  const [loading, setLoading] = useState(true);
  const [error, setError] = useState("");
  const [saving, setSaving] = useState(false);
  const [query, setQuery] = useState("");
  const [hotels, setHotels] = useState([]);

  const [showAdd, setShowAdd] = useState(false);
  const [name, setName] = useState("");
  const [city, setCity] = useState("");
  const [address, setAddress] = useState("");

  const load = async () => {
    setLoading(true);
    setError("");
    const { res, data } = await apiFetch("/admin/hotels", { method: "GET" });
    if (!res.ok) {
      setError(data?.error || "Impossible de charger les hôtels.");
      setLoading(false);
      return;
    }
    setHotels(Array.isArray(data) ? data : []);
    setLoading(false);
  };

  useEffect(() => {
    load();
  }, []);

  const filtered = useMemo(() => {
    const q = query.trim().toLowerCase();
    if (!q) return hotels;
    return hotels.filter(
      (h) =>
        (h.name || "").toLowerCase().includes(q) ||
        (h.city || "").toLowerCase().includes(q) ||
        (h.address || "").toLowerCase().includes(q)
    );
  }, [hotels, query]);

  const addHotel = async () => {
    setError("");
    if (!name || !city || !address) {
      setError("Veuillez remplir nom, ville et adresse.");
      return;
    }
    if (saving) return;
    setSaving(true);
    const { res, data } = await apiFetch("/admin/hotels", {
      method: "POST",
      body: JSON.stringify({ name, city, address }),
    });
    setSaving(false);
    if (res.status === 201) {
      setName("");
      setCity("");
      setAddress("");
      setShowAdd(false);
      await load();
      return;
    }
    setError(data?.error || "Erreur lors de l’ajout de l’hôtel.");
  };

  const editHotel = async (hotel) => {
    setError("");
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
      setError(data?.error || "Erreur lors de la modification.");
      return;
    }
    await load();
  };

  return (
    <div>
      <header className="pageHeader">
        <div>
          <h1 className="pageTitle">Hôtels</h1>
          <p className="subTitle">Créer et gérer les hôtels.</p>
        </div>
        <div className="toolbar">
          <input
            className="input"
            placeholder="Rechercher"
            value={query}
            onChange={(e) => setQuery(e.target.value)}
          />
          <button className="btnPrimary" onClick={() => setShowAdd((v) => !v)}>
            + Ajouter hôtel
          </button>
        </div>
      </header>

      {error && <div style={{ color: "red", marginBottom: 10 }}>{error}</div>}
      {loading && <div className="muted">Chargement…</div>}

      {showAdd && (
        <div className="card" style={{ marginBottom: 14 }}>
          <h3 className="cardTitle" style={{ marginBottom: 10 }}>
            Nouvel hôtel
          </h3>
          <div className="form">
            <div className="field">
              <div className="label">Nom</div>
              <input className="control" value={name} onChange={(e) => setName(e.target.value)} />
            </div>
            <div className="field">
              <div className="label">Ville</div>
              <input className="control" value={city} onChange={(e) => setCity(e.target.value)} />
            </div>
            <div className="field">
              <div className="label">Adresse</div>
              <input className="control" value={address} onChange={(e) => setAddress(e.target.value)} />
            </div>
            <div className="cta">
              <button className="btnPrimary" onClick={addHotel} disabled={saving}>
                {saving && <span className="spinner sm white" />}
                {saving ? "Enregistrement…" : "Enregistrer"}
              </button>
              <button className="btn" onClick={() => setShowAdd(false)}>
                Annuler
              </button>
            </div>
          </div>
        </div>
      )}

      {filtered.map((h) => (
        <div className="card" key={h.id}>
          <div className="cardTop">
            <div>
              <h3 className="cardTitle">{h.name}</h3>
              <div className="muted">{h.city} • {h.address}</div>
            </div>
            <span className="badge">ID #{h.id}</span>
          </div>

          <div style={{ display: "flex", gap: 10, marginTop: 10 }}>
            <button className="btn" onClick={() => navigate(`/admin/rooms?hotelId=${h.id}`)}>
              Voir / ajouter chambres
            </button>
            <button className="btn" onClick={() => editHotel(h)}>
              ✏️ Modifier
            </button>
          </div>
        </div>
      ))}
    </div>
  );
}
