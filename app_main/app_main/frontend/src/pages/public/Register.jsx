import { useState } from "react";
import { useNavigate } from "react-router-dom";
import API_URL from "../../config";

export default function Register() {
  const navigate = useNavigate();
  const [mode, setMode] = useState("client"); 
  const [email, setEmail] = useState("");
  const [password, setPassword] = useState("");
  const [error, setError] = useState("");

  const onSubmit = async (e) => {
    e.preventDefault();
    setError("");

    try {
      const res = await fetch(`${API_URL}/register`, {
        method: "POST",
        headers: { "Content-Type": "application/json" },
        body: JSON.stringify({ email, password, role: mode }), 
      });

      if (res.status === 200) {
        navigate("/login"); 
      } else {
        const data = await res.json();
        setError(data.error || "Erreur serveur");
      }
    } catch (err) {
      setError("Erreur réseau");
      console.error(err);
    }
  };

  return (
    <div>
      <h1>Créer un compte</h1>

      <div style={{ marginBottom: "10px" }}>
        <button
          onClick={() => setMode("client")}
          style={{ fontWeight: mode === "client" ? "bold" : "normal" }}
        >
          Client
        </button>
        <button
          onClick={() => setMode("admin")}
          style={{ fontWeight: mode === "admin" ? "bold" : "normal" }}
        >
          Admin
        </button>
      </div>

      <form onSubmit={onSubmit}>
        <input
          placeholder="Email"
          value={email}
          onChange={(e) => setEmail(e.target.value)}
        />
        <input
          type="password"
          placeholder="Mot de passe"
          value={password}
          onChange={(e) => setPassword(e.target.value)}
        />
        <button>Créer compte</button>
      </form>

      {error && <div style={{ color: "red" }}>{error}</div>}
    </div>
  );
}