// src/components/TestAPI.jsx
import { useEffect, useState } from 'react';
import API_URL from '../config';

function TestAPI() {
  const [hotels, setHotels] = useState([]);
  const [rooms, setRooms] = useState([]);
  const [services, setServices] = useState([]);

  useEffect(() => {
    // Admin credentials pour Basic Auth
    const email = "admin@example.com";
    const password = "admin123";
    const headers = new Headers();
    headers.set('Authorization', 'Basic ' + btoa(`${email}:${password}`));
    headers.set('Content-Type', 'application/json');

    // 1️⃣ Hotels
    fetch(`${API_URL}/admin/hotels`, { headers })
      .then(res => res.json())
      .then(data => setHotels(data))
      .catch(err => console.error("Hotels error:", err));

    // 2️⃣ Rooms de l'hôtel 1
    fetch(`${API_URL}/admin/hotels/1/rooms`, { headers })
      .then(res => res.json())
      .then(data => setRooms(data))
      .catch(err => console.error("Rooms error:", err));

    // 3️⃣ Services
    fetch(`${API_URL}/admin/services`, { headers })
      .then(res => res.json())
      .then(data => setServices(data))
      .catch(err => console.error("Services error:", err));
  }, []);

  return (
    <div>
      <h2>Hotels</h2>
      <ul>{hotels.map(h => <li key={h.id}>{h.name} - {h.city}</li>)}</ul>

      <h2>Rooms (Hotel 1)</h2>
      <ul>{rooms.map(r => <li key={r.id}>Chambre {r.number} - {r.pricePerNight}€</li>)}</ul>

      <h2>Services</h2>
      <ul>{services.map(s => <li key={s.id}>{s.name}</li>)}</ul>
    </div>
  );
}

export default TestAPI;