import { useEffect, useMemo, useState } from "react";
import { useSearchParams } from "react-router-dom";
import { apiFetch } from "../../utils/api";

function badgeFor(status) {
  const s = (status || "").toLowerCase();
  if (s.includes("termin")) return "badge ok";
  if (s.includes("cours")) return "badge progress";
  return "badge";
}

function labelFor(status) {
  const s = (status || "").toLowerCase();
  if (s.includes("termin")) return "Terminé";
  if (s.includes("cours")) return "En cours";
  return "Prévu";
}

export default function AdminWorks() {
  const [searchParams] = useSearchParams();
  const [loading, setLoading] = useState(true);
  const [error, setError] = useState("");
  const [rooms, setRooms] = useState([]);
  const [roomId, setRoomId] = useState("");
  const [items, setItems] = useState([]);
  const [expandedId, setExpandedId] = useState(null);

  const [description, setDescription] = useState("");
  const [startDate, setStartDate] = useState("");
  const [endDatePlanned, setEndDatePlanned] = useState("");

  const selectedRoom = useMemo(
    () => rooms.find((r) => String(r.id) === String(roomId)),
    [rooms, roomId]
  );

  const loadRooms = async () => {
    const rr = await apiFetch("/admin/rooms", { method: "GET" });
    if (!rr.res.ok) {
      setError(rr.data?.error || "Impossible de charger les chambres.");
      return [];
    }
    const list = Array.isArray(rr.data) ? rr.data : [];
    setRooms(list);
    return list;
  };

  const loadMaintenances = async (rid) => {
    if (!rid) return;
    const { res, data } = await apiFetch(`/admin/maintenance/rooms/${rid}`, {
      method: "GET",
    });
    if (!res.ok) {
      setError(data?.error || "Impossible de charger les travaux.");
      return;
    }
    setItems(Array.isArray(data) ? data : []);
  };

  const load = async () => {
    setLoading(true);
    setError("");
    const list = await loadRooms();
    const qp = searchParams.get("roomId");
    const initial = qp || (list[0]?.id ? String(list[0].id) : "");
    setRoomId(initial);
    await loadMaintenances(initial);
    setLoading(false);
  };

  useEffect(() => {
    load();
  }, []);

  useEffect(() => {
    const qp = searchParams.get("roomId");
    if (qp) {
      setRoomId(String(qp));
      loadMaintenances(String(qp));
    }
  }, [searchParams]);

  const create = async (e) => {
    e.preventDefault();
    setError("");
    if (!roomId || !description || !startDate || !endDatePlanned) {
      setError("Veuillez remplir chambre, description, dates.");
      return;
    }
    const { res, data } = await apiFetch("/admin/maintenance", {
      method: "POST",
      body: JSON.stringify({
        room_id: Number(roomId),
        description,
        startDate,
        endDatePlanned,
      }),
    });
    if (res.status === 201) {
      setDescription("");
      setStartDate("");
      setEndDatePlanned("");
      await loadMaintenances(roomId);
      return;
    }
    setError(data?.error || "Erreur lors de la création.");
  };

  const markDone = async (id) => {
    setError("");
    const now = new Date();
    const iso =
      now.getFullYear() +
      "-" +
      String(now.getMonth() + 1).padStart(2, "0") +
      "-" +
      String(now.getDate()).padStart(2, "0") +
      " " +
      String(now.getHours()).padStart(2, "0") +
      ":" +
      String(now.getMinutes()).padStart(2, "0") +
      ":00";

    const { res, data } = await apiFetch(`/admin/maintenance/${id}`, {
      method: "PATCH",
      body: JSON.stringify({ endDateReal: iso }),
    });
    if (!res.ok) {
      setError(data?.error || "Impossible de marquer terminé.");
      return;
    }
    await loadMaintenances(roomId);
  };

  return (
    <div>
      <header className="pageHeader">
        <div>
          <h1 className="pageTitle">Admin</h1>
          <p className="subTitle">Créer et suivre les maintenances par chambre.</p>
        </div>

        <div className="toolbar">
          <select
            className="input"
            value={roomId}
            onChange={(e) => {
              setRoomId(e.target.value);
              loadMaintenances(e.target.value);
            }}
          >
            <option value="">— Chambre —</option>
            {rooms.map((r) => (
              <option key={r.id} value={r.id}>
                {r.hotel?.name ? `${r.hotel.name} — ` : ""}Ch {r.number}
              </option>
            ))}
          </select>
        </div>
      </header>

      {error && <div style={{ color: "red", marginBottom: 10 }}>{error}</div>}
      {loading && <div className="muted">Chargement…</div>}

      <div className="card" style={{ marginBottom: 14 }}>
        <h3 className="cardTitle" style={{ marginBottom: 10 }}>
          Ajouter un travail {selectedRoom ? `— Chambre ${selectedRoom.number}` : ""}
        </h3>
        <form className="form" onSubmit={create}>
          <div className="field">
            <div className="label">Description</div>
            <input
              className="control"
              value={description}
              onChange={(e) => setDescription(e.target.value)}
              placeholder="Ex: Peinture / Clim / Plomberie…"
            />
          </div>
          <div className="field">
            <div className="label">Début</div>
            <input className="control" type="datetime-local" value={startDate} onChange={(e) => setStartDate(e.target.value)} />
          </div>
          <div className="field">
            <div className="label">Fin prévue</div>
            <input className="control" type="datetime-local" value={endDatePlanned} onChange={(e) => setEndDatePlanned(e.target.value)} />
          </div>
          <div className="cta">
            <button className="btnPrimary">Créer</button>
          </div>
        </form>
      </div>

      {items.map((w) => (
        <div className="card" key={w.id}>
          <div className="cardTop">
            <div>
              <h3 className="cardTitle">
                {selectedRoom?.hotel?.name ?? "Hôtel"} — Chambre {selectedRoom?.number ?? "—"}
              </h3>
              <div className="muted">
                {w.description}
              </div>
            </div>
            <span className={badgeFor(w.status)}>{labelFor(w.status)}</span>
          </div>

          {expandedId === w.id && (
            <div className="rows">
              <div className="row">
                <span>Début</span>
                <span>{w.startDate ?? "—"}</span>
              </div>
              <div className="row">
                <span>Fin prévue</span>
                <span>{w.endDatePlanned ?? "—"}</span>
              </div>
              <div className="row">
                <span>Fin réelle</span>
                <span>{w.endDateReal ?? "—"}</span>
              </div>
            </div>
          )}

          <div style={{ display: "flex", gap: 10, marginTop: 10 }}>
            <button className="btn" onClick={() => setExpandedId(expandedId === w.id ? null : w.id)}>
              {expandedId === w.id ? "Masquer détails" : "Détails"}
            </button>
            <button className="btnPrimary" onClick={() => markDone(w.id)}>
              Marquer terminé
            </button>
          </div>
        </div>
      ))}
    </div>
  );
}
