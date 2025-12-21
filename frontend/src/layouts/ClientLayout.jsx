import { Outlet, Link, useNavigate } from "react-router-dom";

export default function ClientLayout() {
  const navigate = useNavigate();

  return (
    <div className="layout">
      <aside className="sidebar">
        <h3>Client</h3>

        <nav className="nav">
          <Link to="/client">Dashboard</Link>
          <Link to="/client/reservations">Réservations</Link>
          <Link to="/client/availability">Disponibilités</Link>
          <Link to="/login">Retour Login</Link>
        </nav>

        <button className="btn" onClick={() => navigate("/login")}>
          Se déconnecter (statique)
        </button>
      </aside>

      <main className="main">
        <Outlet />
      </main>
    </div>
  );
}
