import { useState } from "react";
import { useNavigate } from "react-router-dom";
import API_URL from "../../config";

export default function Register() {
  const navigate = useNavigate();
  const [firstName, setFirstName] = useState("");
  const [lastName, setLastName] = useState("");
  const [phone, setPhone] = useState("");
  const [email, setEmail] = useState("");
  const [password, setPassword] = useState("");
  const [confirmPassword, setConfirmPassword] = useState("");
  const [error, setError] = useState("");

  const onSubmit = async (e) => {
    e.preventDefault();
    setError("");

    if (
      !firstName ||
      !lastName ||
      !phone ||
      !email ||
      !password ||
      !confirmPassword
    ) {
      setError("Veuillez remplir tous les champs.");
      return;
    }

    if (password !== confirmPassword) {
      setError("Les mots de passe ne correspondent pas.");
      return;
    }

    try {
      const res = await fetch(`${API_URL}/register`, {
        method: "POST",
        headers: { "Content-Type": "application/json" },
        body: JSON.stringify({
          firstName,
          lastName,
          phone,
          email,
          password,
        }),
      });

      let data = null;
      try {
        data = await res.json();
      } catch {
        data = null;
      }

      if (res.ok) {
        navigate("/login"); 
      } else {
        setError(data?.error || "Erreur serveur");
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

        <h1 className="authTitle">Inscription</h1>
        <p className="authText">
          Créez votre compte client pour réserver selon la disponibilité des hôtels.
        </p>

        <form className="form" onSubmit={onSubmit}>
          <div className="field">
            <div className="label">Nom</div>
            <input
              className="control"
              value={lastName}
              onChange={(e) => setLastName(e.target.value)}
              placeholder="Ex: Ben Ali"
              autoComplete="family-name"
            />
          </div>

          <div className="field">
            <div className="label">Prénom</div>
            <input
              className="control"
              value={firstName}
              onChange={(e) => setFirstName(e.target.value)}
              placeholder="Ex: Sami"
              autoComplete="given-name"
            />
          </div>

          <div className="field">
            <div className="label">Téléphone</div>
            <input
              className="control"
              value={phone}
              onChange={(e) => setPhone(e.target.value)}
              placeholder="Ex: +216 12 345 678"
              autoComplete="tel"
              inputMode="tel"
            />
          </div>

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
            <div className="label">Mot de passe</div>
            <input
              className="control"
              type="password"
              value={password}
              onChange={(e) => setPassword(e.target.value)}
              placeholder="••••••••"
              autoComplete="new-password"
            />
          </div>

          <div className="field">
            <div className="label">Confirmer le mot de passe</div>
            <input
              className="control"
              type="password"
              value={confirmPassword}
              onChange={(e) => setConfirmPassword(e.target.value)}
              placeholder="••••••••"
              autoComplete="new-password"
            />
          </div>

          <div className="cta">
            <button className="btnCta">Créer mon compte</button>
            <button
              type="button"
              className="btnGhost"
              onClick={() => navigate("/login")}
            >
              J’ai déjà un compte
            </button>
          </div>

          <div className="hint">
            Inscription rapide, comme une vraie appli de réservation.
          </div>

          {error && <div style={{ color: "red" }}>{error}</div>}
        </form>
      </div>
    </div>
  );
}