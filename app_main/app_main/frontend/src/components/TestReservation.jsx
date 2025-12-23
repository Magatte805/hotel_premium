import { useState } from 'react';
import API_URL from '../config';

function TestReservation() {
  const [message, setMessage] = useState('');

  const createReservation = async () => {
    const email = "client1@example.com"; // email client
    const password = "client123";        // mot de passe client en clair
    const headers = new Headers();
    headers.set('Authorization', 'Basic ' + btoa(`${email}:${password}`));
    headers.set('Content-Type', 'application/json');

    const body = JSON.stringify({
      startDate: "2025-12-20",
      endDate: "2025-12-25",
      room: 2
    });

    try {
      const res = await fetch(`${API_URL}/client/reservations`, {
        method: 'POST',
        headers,
        body
      });

      if (!res.ok) throw new Error("HTTP " + res.status);

      const data = await res.json();
      setMessage("Réservation créée avec succès : ID " + data.id);
    } catch (err) {
      setMessage("Erreur : " + err.message);
      console.error(err);
    }
  };

  return (
    <div>
      <button onClick={createReservation}>Créer réservation</button>
      <p>{message}</p>
    </div>
  );
}

export default TestReservation;