import { reservations, rooms, hotels } from "../../data/staticData";

export default function ClientReservations() {
  return (
    <div>
      <header className="pageHeader">
        <div>
          <h1 className="pageTitle">Mes réservations</h1>
          <p className="subTitle">Retrouve tes réservations, dates et détails.</p>
        </div>
      </header>

      <div className="grid">
        {reservations.map((r) => {
          const room = rooms.find((x) => x.id === r.roomId);
          const hotel = room ? hotels.find((h) => h.id === room.hotelId) : null;

          const badgeClass =
            r.status === "CONFIRMED" ? "badge ok" : "badge cancel";

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
                  <span>{r.startDate} → {r.endDate}</span>
                </div>
                <div className="row">
                  <span>Client</span>
                  <span>{r.client}</span>
                </div>
                <div className="row">
                  <span>Prix/nuit</span>
                  <span>{room?.prix ?? "—"} €</span>
                </div>
              </div>

              <div className="cta" style={{ marginTop: 12 }}>
                <button className="btn">Voir détails</button>
                <button className="btnPrimary">Annuler (statique)</button>
              </div>
            </div>
          );
        })}
      </div>
    </div>
  );
}
