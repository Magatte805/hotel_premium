import { rooms, hotels } from "../../data/staticData";

const works = [
  { id: 1, roomId: 101, label: "Remplacement moquette", status: "IN_PROGRESS", start: "2025-05-02", end: "2025-05-06" },
  { id: 2, roomId: 202, label: "Peinture", status: "DONE", start: "2025-04-11", end: "2025-04-12" },
  { id: 3, roomId: 302, label: "Réparation clim", status: "PLANNED", start: "2025-06-01", end: "2025-06-02" },
];

function badgeFor(status) {
  if (status === "DONE") return "badge ok";
  if (status === "IN_PROGRESS") return "badge progress";
  return "badge";
}

function labelFor(status) {
  if (status === "DONE") return "Terminé";
  if (status === "IN_PROGRESS") return "En cours";
  return "Planifié";
}

export default function AdminWorks() {
  return (
    <div>
      <header className="pageHeader">
        <div>
          <h1 className="pageTitle">Admin — Travaux</h1>
          <p className="subTitle">Suivi des travaux (statique).</p>
        </div>
      </header>

      {works.map((w) => {
        const room = rooms.find((r) => r.id === w.roomId);
        const hotel = room ? hotels.find((h) => h.id === room.hotelId) : null;

        return (
          <div className="card" key={w.id}>
            <div className="cardTop">
              <div>
                <h3 className="cardTitle">
                  {hotel?.name ?? "Hôtel"} — Chambre {room?.number ?? "—"}
                </h3>
                <div className="muted">
                  {w.label} • {w.start} → {w.end}
                </div>
              </div>
              <span className={badgeFor(w.status)}>{labelFor(w.status)}</span>
            </div>

            <div style={{ display: "flex", gap: 10 }}>
              <button className="btn">Détails</button>
              <button className="btnPrimary">Marquer terminé</button>
            </div>
          </div>
        );
      })}
    </div>
  );
}
