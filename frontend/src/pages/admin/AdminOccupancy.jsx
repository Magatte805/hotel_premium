import { reservations, rooms, hotels } from "../../data/staticData";

function isActive(res) {
  // “actif” statique : on prend CONFIRMED comme occupée
  return res.status === "CONFIRMED";
}

export default function AdminOccupancy() {
  const active = reservations.filter(isActive);

  return (
    <div>
      <header className="pageHeader">
        <div>
          <h1 className="pageTitle">Admin — Occupation</h1>
          <p className="subTitle">Vue occupation basée sur les réservations confirmées.</p>
        </div>
      </header>

      {active.map((r) => {
        const room = rooms.find((x) => x.id === r.roomId);
        const hotel = room ? hotels.find((h) => h.id === room.hotelId) : null;

        return (
          <div className="card" key={r.id}>
            <div className="cardTop">
              <div>
                <h3 className="cardTitle">
                  {hotel?.name ?? "Hôtel"} — Chambre {room?.number ?? "—"}
                </h3>
                <div className="muted">{r.startDate} → {r.endDate}</div>
              </div>
              <span className="badge ok">Occupée</span>
            </div>
          </div>
        );
      })}

      {active.length === 0 && (
        <div className="card">
          <p className="muted">Aucune chambre occupée (statique).</p>
        </div>
      )}
    </div>
  );
}
