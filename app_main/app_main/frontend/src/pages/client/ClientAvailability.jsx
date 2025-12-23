import { rooms, hotels } from "../../data/staticData";

export default function ClientAvailability() {
  return (
    <div>
      <header className="pageHeader">
        <div>
          <h1 className="pageTitle">Disponibilités</h1>
          <p className="subTitle">Liste des chambres disponibles (statique).</p>
        </div>

        <div className="toolbar">
          <input className="input" placeholder="Ville / hôtel (statique)" />
          <button className="btn">Filtrer</button>
        </div>
      </header>

      <div className="grid">
        {rooms.map((r) => {
          const hotel = hotels.find((h) => h.id === r.hotelId);

          return (
            <div className="card" key={r.id}>
              <div className="cardTop">
                <div>
                  <h3 className="cardTitle">
                    {hotel?.name ?? "Hôtel"} — {hotel?.city ?? "—"}
                  </h3>
                  <div className="muted">
                    Chambre <b>{r.number}</b> • {r.service} • {r.classement}
                  </div>
                </div>

                <span className="badge ok">Disponible</span>
              </div>

              <div className="muted">
                {(r.prestations || []).join(" • ")}
              </div>

              <div style={{ marginTop: 12, display: "flex", justifyContent: "space-between", alignItems: "center" }}>
                <div className="price">{r.prix} € / nuit</div>
                <button className="btnPrimary">Réserver</button>
              </div>
            </div>
          );
        })}
      </div>
    </div>
  );
}
