import { useMemo, useState } from "react";
import { useNavigate } from "react-router-dom";

export default function Login() {
  const navigate = useNavigate();
  const [mode, setMode] = useState("client");
  const [email, setEmail] = useState("");
  const [password, setPassword] = useState("");

  const placeholder = useMemo(
    () =>
      mode === "admin"
        ? "admin@hotel-premium.fr"
        : "client@hotel-premium.fr",
    [mode]
  );

  const onSubmit = (e) => {
    e.preventDefault();
    navigate(mode === "admin" ? "/admin" : "/client");
  };

  const fillDemo = () => {
    if (mode === "admin") {
      setEmail("admin@test.com");
      setPassword("admin");
    } else {
      setEmail("client@test.com");
      setPassword("client");
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
          Accède à l’espace <b>{mode === "admin" ? "Admin" : "Client"}</b> pour
          consulter les données du projet.
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
              <span className="smallLink">Mot de passe oublié ?</span>
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
            <b>Mode statique :</b> le backend n’est pas encore branché.
            Ce login sert uniquement à naviguer et tester l’UI.
          </div>
        </form>

      </div>
    </div>
  );
}
