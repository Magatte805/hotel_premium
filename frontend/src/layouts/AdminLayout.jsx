import { Outlet, Link, useNavigate } from "react-router-dom";

export default function AdminLayout() {
  const navigate = useNavigate();

  return (
    <div className="layout">
      <aside className="sidebar">
        <h3>Admin</h3>

        <nav className="nav">
          <Link to="/admin">Dashboard</Link>
          <Link to="/admin/hotels">Hôtels</Link>
          <Link to="/admin/rooms">Chambres</Link>
          <Link to="/admin/works">Travaux</Link>
          <Link to="/admin/occupancy">Occupation</Link>
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
