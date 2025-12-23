import { Outlet, NavLink, useNavigate } from "react-router-dom";
import { clearAuth } from "../utils/auth";

export default function ClientLayout() {
  const navigate = useNavigate();

  return (
    <div className="layout">
      <aside className="sidebar">
        <h3>Client</h3>

        <nav className="nav">
          <NavLink to="/client" end className={({ isActive }) => (isActive ? "active" : undefined)}>
            Dashboard
          </NavLink>
          <NavLink to="/client/reservations" className={({ isActive }) => (isActive ? "active" : undefined)}>
            Réservations
          </NavLink>
          <NavLink to="/client/availability" className={({ isActive }) => (isActive ? "active" : undefined)}>
            Disponibilités
          </NavLink>
        </nav>

        <button
          className="btn"
          onClick={() => {
            clearAuth();
            navigate("/login");
          }}
        >
          Se déconnecter
        </button>
      </aside>

      <main className="main">
        <Outlet />
      </main>
    </div>
  );
}
