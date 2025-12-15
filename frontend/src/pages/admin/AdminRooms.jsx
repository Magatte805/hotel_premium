import { rooms, hotels } from "../../data/staticData";

function cls(c) {
  const v = (c || "").toLowerCase();
  if (v.includes("suite")) return "suite";
  if (v.includes("deluxe")) return "deluxe";
  return "standard";
}

export default function AdminRooms() {
  return (
    <div>
      <header className="pageHeader">
        <div>
          <h1 className="pageTitle">Admin — Chambres</h1>
          <p className="subTitle">Toutes les chambres (statique).</p>
        </div>
      </header>

      <div className="card">
        <table className="table">
          <thead>
            <tr>
              <th>Num</th>
              <th>Hôtel</th>
              <th>Service</th>
              <th>Classement</th>
              <th>Prestations</th>
              <th>Prix/nuit</th>
            </tr>
          </thead>
          <tbody>
            {rooms.map((r) => {
              const hotel = hotels.find((h) => h.id === r.hotelId);
              return (
                <tr key={r.id}>
                  <td><b>{r.number}</b></td>
                  <td>{hotel?.name ?? "—"}</td>
                  <td>{r.service}</td>
                  <td>
                    <span className={`badgeMini ${cls(r.classement)}`}>
                      {r.classement}
                    </span>
                  </td>
                  <td className="muted">{(r.prestations || []).join(" • ")}</td>
                  <td className="price">{r.prix} €</td>
                </tr>
              );
            })}
          </tbody>
        </table>
      </div>
    </div>
  );
}
