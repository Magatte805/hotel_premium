import { useState } from "react";
import { useNavigate } from "react-router-dom";
import API_URL from "../../config";

export default function Login() {
  const navigate = useNavigate();
  const [mode, setMode] = useState("client");
  const [email, setEmail] = useState("");
  const [password, setPassword] = useState("");
  const [error, setError] = useState("");

  const placeholder =
    mode === "admin" ? "admin@example.com" : "client1@example.com";

  const onSubmit = async (e) => {
  e.preventDefault();
  setError("");

  if (!email || !password) {
    setError("Veuillez remplir tous les champs.");
    return;
  }

  try {
    const res = await fetch(`${API_URL}/login`, {
      method: "POST",
      headers: { "Content-Type": "application/json" },
      body: JSON.stringify({ email, password, mode }),
    });

    const data = await res.json();

    if (res.status === 200 && data.success) {
      // Login OK → redirection
      navigate(mode === "admin" ? "/admin" : "/client");
    } else {
      // Erreur (user non trouvé / mot de passe incorrect / rôle incorrect)
      setError(data.error || "Email ou mot de passe incorrect");
    }
  } catch (err) {
    setError("Erreur réseau");
    console.error(err);
  }
};


  const fillDemo = () => {
    if (mode === "admin") {
      setEmail("admin@example.com");
      setPassword("admin123"); // mot de passe en clair de ta BDD
    } else {
      setEmail("client1@example.com");
      setPassword("client123"); // mot de passe en clair de ta BDD
    }
  };

  return (
    <div className="authWrap">
      <div className="authCard">
        <div className="brand">
          <span className="logoDot" />
          <div className="brandName">Hôtels Premium</div>
        </div>

        <h1 className="authTitle">Connexion</h1>
        <p className="authText">
          Accède à l’espace <b>{mode === "admin" ? "Admin" : "Client"}</b>
        </p>

        <div className="tabs">
          <button
            className={`tab ${mode === "admin" ? "active" : ""}`}
            onClick={() => setMode("admin")}
          >
            Admin
          </button>
          <button
            className={`tab ${mode === "client" ? "active" : ""}`}
            onClick={() => setMode("client")}
          >
            Client
          </button>
        </div>

        <form className="form" onSubmit={onSubmit}>
          <div className="field">
            <div className="label">Adresse email</div>
            <input
              className="control"
              value={email}
              onChange={(e) => setEmail(e.target.value)}
              placeholder={placeholder}
            />
          </div>

          <div className="field">
            <div className="rowBetween">
              <div className="label">Mot de passe</div>
            </div>
            <input
              className="control"
              type="password"
              value={password}
              onChange={(e) => setPassword(e.target.value)}
              placeholder="••••••••"
            />
          </div>

          <div className="cta">
            <button className="btnCta">Continuer</button>
            <button type="button" className="btnGhost" onClick={fillDemo}>
              Remplir démo
            </button>
          </div>

         <div className="hint">
          Vous n'avez pas de compte ?{" "}
          <span
            style={{ color: "blue", cursor: "pointer" }}
            onClick={() => navigate("/register")}
          >
            Créer un compte
          </span>
      </div>
          {error && <div style={{ color: "red" }}>{error}</div>}
        </form>
      </div>
    </div>
  );

}