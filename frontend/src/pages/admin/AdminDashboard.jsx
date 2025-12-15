import { hotels, rooms } from "../../data/staticData";

function badgeClass(classement) {
  const c = (classement || "").toLowerCase();
  if (c.includes("suite")) return "badgeMini suite";
  if (c.includes("deluxe")) return "badgeMini deluxe";
  return "badgeMini standard";
}

export default function AdminDashboard() {
  const totalHotels = hotels.length;
  const totalRooms = rooms.length;

  const avgPrice =
    totalRooms === 0
      ? 0
      : Math.round(rooms.reduce((s, r) => s + (r.prix || 0), 0) / totalRooms);

  const topCity =
    hotels.length === 0
      ? "—"
      : Object.entries(
          hotels.reduce((acc, h) => {
            acc[h.city] = (acc[h.city] || 0) + 1;
            return acc;
          }, {})
        ).sort((a, b) => b[1] - a[1])[0][0];

  return (
    <div>
      <header className="pageHeader">
        <div>
          <h1 className="pageTitle">Admin — Hôtels & Chambres</h1>
          <p className="subTitle">
            Vue globale du parc hôtelier : services, chambres, classement et prix.
          </p>
        </div>

        <div className="toolbar">
          <input className="input" placeholder="Recherche (statique)" />
          <button className="btn">Exporter</button>
          <button className="btnPrimary">+ Ajouter un hôtel</button>
        </div>
      </header>

      {/* KPIs */}
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

      {/* Liste hôtels */}
      {hotels.map((h) => {
        const hotelRooms = rooms.filter((r) => r.hotelId === h.id);

        return (
          <section className="hotelCard" key={h.id}>
            <div className="hotelHeader">
              <div>
                <h2>{h.name}</h2>
                <div className="meta">
                  {h.city} • {hotelRooms.length} chambre(s)
                </div>

                <div className="pills">
                  {(h.services || []).map((s, idx) => (
                    <span className="pill" key={idx}>{s}</span>
                  ))}
                </div>
              </div>

              <div className="actions">
                <button className="btn">Voir détails</button>
                <button className="btnPrimary">+ Ajouter chambre</button>
              </div>
            </div>

            <div className="tableWrap">
              <table className="table">
                <thead>
                  <tr>
                    <th style={{ width: 110 }}>Chambre</th>
                    <th style={{ width: 160 }}>Service</th>
                    <th style={{ width: 140 }}>Classement</th>
                    <th>Prestations</th>
                    <th style={{ width: 120 }}>Prix/nuit</th>
                    <th style={{ width: 160 }}>Actions</th>
                  </tr>
                </thead>
                <tbody>
                  {hotelRooms.map((r) => (
                    <tr key={r.id}>
                      <td><b>{r.number}</b></td>
                      <td>{r.service}</td>
                      <td>
                        <span className={badgeClass(r.classement)}>
                          {r.classement}
                        </span>
                      </td>
                      <td style={{ color: "var(--muted)" }}>
                        {(r.prestations || []).join(" • ")}
                      </td>
                      <td className="price">{r.prix} €</td>
                      <td>
                        <div className="smallActions">
                          <button className="iconBtn">✏️ Modifier</button>
                          <button className="iconBtn">🛠️ Travaux</button>
                        </div>
                      </td>
                    </tr>
                  ))}

                  {hotelRooms.length === 0 && (
                    <tr>
                      <td colSpan="6" style={{ color: "var(--muted)" }}>
                        Aucune chambre pour cet hôtel (donnée statique).
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
