import { hotels } from "../../data/staticData";

export default function AdminHotels() {
  return (
    <div>
      <header className="pageHeader">
        <div>
          <h1 className="pageTitle">Admin — Hôtels</h1>
          <p className="subTitle">Liste des hôtels (statique).</p>
        </div>
        <div className="toolbar">
          <input className="input" placeholder="Rechercher (statique)" />
          <button className="btnPrimary">+ Ajouter hôtel</button>
        </div>
      </header>

      {hotels.map((h) => (
        <div className="card" key={h.id}>
          <div className="cardTop">
            <div>
              <h3 className="cardTitle">{h.name}</h3>
              <div className="muted">{h.city}</div>
            </div>
            <span className="badge">ID #{h.id}</span>
          </div>

          <div className="pills">
            {(h.services || []).map((s, i) => (
              <span className="pill" key={i}>{s}</span>
            ))}
          </div>
        </div>
      ))}
    </div>
  );
}
