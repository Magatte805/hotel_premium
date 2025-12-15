import { reservations, rooms, hotels } from "../../data/staticData";

function nightsBetween(start, end) {
  // format YYYY-MM-DD
  const a = new Date(start);
  const b = new Date(end);
  const diff = (b - a) / (1000 * 60 * 60 * 24);
  return Math.max(1, Math.round(diff));
}

export default function ClientDashboard() {
  return (
    <div>
      <header className="pageHeader">
        <div>
          <h1 className="pageTitle">Mes réservations</h1>
          <p className="subTitle">
            Retrouve tes réservations, dates et détails de chambre.
          </p>
        </div>

        <div className="toolbar">
          <input className="input" placeholder="Rechercher (statique)" />
          <button className="btn">Filtrer</button>
        </div>
      </header>

      <section className="grid">
        {reservations.map((res) => {
          const room = rooms.find((r) => r.id === res.roomId);
          const hotel = room ? hotels.find((h) => h.id === room.hotelId) : null;

          const nights = nightsBetween(res.startDate, res.endDate);
          const total = room ? nights * room.prix : 0;

          const badgeClass =
            res.status === "CONFIRMED" ? "badge ok" : "badge cancel";

          const badgeLabel =
            res.status === "CONFIRMED" ? "Confirmée" : "Annulée";

          return (
            <article className="card" key={res.id}>
              <div className="cardTop">
                <div>
                  <h3 className="cardTitle">
                    {hotel ? hotel.name : "Hôtel —"}
                  </h3>
                  <div className="muted">
                    Chambre {room?.number ?? "—"} • {hotel?.city ?? "—"}
                  </div>
                </div>

                <span className={badgeClass}>{badgeLabel}</span>
              </div>

              <div className="rows">
                <div className="row">
                  <span>Dates</span>
                  <span>{res.startDate} → {res.endDate}</span>
                </div>

                <div className="row">
                  <span>Nuits</span>
                  <span>{nights}</span>
                </div>

                <div className="row">
                  <span>Prix/nuit</span>
                  <span>{room?.prix ?? "—"} €</span>
                </div>

                <div className="row">
                  <span>Total</span>
                  <span><b>{total} €</b></span>
                </div>

                <div className="row">
                  <span>Prestations</span>
                  <span>{room?.prestations?.join(", ") ?? "—"}</span>
                </div>
              </div>

              <div style={{ marginTop: 14, display: "flex", gap: 10 }}>
                <button className="btn">Voir détails</button>
                <button className="btn">Annuler (statique)</button>
              </div>
            </article>
          );
        })}
      </section>
    </div>
  );
}
