import { Outlet, NavLink, useNavigate } from "react-router-dom";
import { clearAuth } from "../utils/auth";

export default function AdminLayout() {
  const navigate = useNavigate();

  return (
    <div className="layout">
      <aside className="sidebar">
        <h3>Admin</h3>

        <nav className="nav">
          <NavLink to="/admin" end className={({ isActive }) => (isActive ? "active" : undefined)}>
            Dashboard
          </NavLink>
          <NavLink to="/admin/hotels" className={({ isActive }) => (isActive ? "active" : undefined)}>
            Hôtels
          </NavLink>
          <NavLink to="/admin/rooms" className={({ isActive }) => (isActive ? "active" : undefined)}>
            Chambres
          </NavLink>
          <NavLink to="/admin/works" className={({ isActive }) => (isActive ? "active" : undefined)}>
            Travaux
          </NavLink>
          <NavLink to="/admin/occupancy" className={({ isActive }) => (isActive ? "active" : undefined)}>
            Occupation
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
