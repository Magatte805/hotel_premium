import { Link } from "react-router-dom";

export default function Home() {
  return (
    <div style={{ padding: 24 }}>
      <h1>Projet Hôtels Premium</h1>
      <p>Frontend React (Vite) – Marcq</p>

      <div style={{ display: "flex", gap: 12, marginTop: 16 }}>
        <Link to="/login">Se connecter</Link>
      </div>
    </div>
  );
}
