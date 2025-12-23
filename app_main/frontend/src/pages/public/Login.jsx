import { useState } from "react";
import { useNavigate } from "react-router-dom";
import API_URL from "../../config";
import { setAuth } from "../../utils/auth";

export default function Login() {
  const navigate = useNavigate();
  const [email, setEmail] = useState("");
  const [password, setPassword] = useState("");
  const [error, setError] = useState("");

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
      body: JSON.stringify({ email, password }),
    });

    const data = await res.json();

    if (res.status === 200 && data.success) {
      setAuth({ email, password, role: data.role });
      // Login OK → redirection
      navigate(data.role === "admin" ? "/admin" : "/client");
    } else {
      // Erreur (user non trouvé / mot de passe incorrect / rôle incorrect)
      setError(data.error || "Email ou mot de passe incorrect");
    }
  } catch (err) {
    setError("Erreur réseau");
    console.error(err);
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
          Connectez-vous pour accéder à votre espace.
        </p>

        <form className="form" onSubmit={onSubmit}>
          <div className="field">
            <div className="label">Adresse email</div>
            <input
              className="control"
              value={email}
              onChange={(e) => setEmail(e.target.value)}
              placeholder="Ex: client@example.com"
              autoComplete="email"
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
              autoComplete="current-password"
            />
          </div>

          <div className="cta">
            <button className="btnCta">Continuer</button>
          </div>

         <div className="hint">
          Vous n'avez pas de compte ?{" "}
          <span
            style={{ color: "var(--primary)", cursor: "pointer" }}
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