// ===============================
// HÔTELS
// ===============================
export const hotels = [
  { id: 1, name: "Hôtel Royal Palace", city: "Paris", services: ["Étage 1", "Étage 2"] },
  { id: 2, name: "Hôtel Blue Ocean", city: "Nice", services: ["Bâtiment A"] },
  { id: 3, name: "Hôtel Green Garden", city: "Lille", services: ["Aile Nord", "Aile Sud"] },
  { id: 4, name: "Hôtel Skyline", city: "Lyon", services: ["Tour Principale"] },
  { id: 5, name: "Hôtel Mont Blanc", city: "Chamonix", services: ["Chalet 1", "Chalet 2"] },
  { id: 6, name: "Hôtel Sunset", city: "Marseille", services: ["Bloc Mer"] },
  { id: 7, name: "Hôtel City Center", city: "Bordeaux", services: ["Étage Unique"] },
  { id: 8, name: "Hôtel Atlantic View", city: "Biarritz", services: ["Vue Océan"] },
];

// ===============================
// CHAMBRES
// ===============================
export const rooms = [
  // Hôtel 1 – Paris
  { id: 101, hotelId: 1, number: "101", service: "Étage 1", classement: "Standard", prestations: ["WiFi", "TV"], prix: 120 },
  { id: 102, hotelId: 1, number: "102", service: "Étage 1", classement: "Deluxe", prestations: ["WiFi", "TV", "Mini-bar"], prix: 180 },
  { id: 103, hotelId: 1, number: "103", service: "Étage 2", classement: "Suite", prestations: ["WiFi", "TV", "Jacuzzi"], prix: 260 },

  // Hôtel 2 – Nice
  { id: 201, hotelId: 2, number: "201", service: "Bâtiment A", classement: "Standard", prestations: ["WiFi"], prix: 95 },
  { id: 202, hotelId: 2, number: "202", service: "Bâtiment A", classement: "Deluxe", prestations: ["WiFi", "TV", "Balcon"], prix: 160 },

  // Hôtel 3 – Lille
  { id: 301, hotelId: 3, number: "301", service: "Aile Nord", classement: "Standard", prestations: ["WiFi"], prix: 90 },
  { id: 302, hotelId: 3, number: "302", service: "Aile Sud", classement: "Deluxe", prestations: ["WiFi", "TV", "Bureau"], prix: 150 },
  { id: 303, hotelId: 3, number: "303", service: "Aile Sud", classement: "Suite", prestations: ["WiFi", "TV", "Jacuzzi", "Coffre-fort"], prix: 240 },

  // Hôtel 4 – Lyon
  { id: 401, hotelId: 4, number: "401", service: "Tour Principale", classement: "Standard", prestations: ["WiFi"], prix: 110 },
  { id: 402, hotelId: 4, number: "402", service: "Tour Principale", classement: "Suite", prestations: ["WiFi", "TV", "Vue ville"], prix: 230 },

  // Hôtel 5 – Chamonix
  { id: 501, hotelId: 5, number: "501", service: "Chalet 1", classement: "Deluxe", prestations: ["WiFi", "Cheminée"], prix: 210 },
  { id: 502, hotelId: 5, number: "502", service: "Chalet 2", classement: "Suite", prestations: ["WiFi", "Jacuzzi", "Vue montagne"], prix: 280 },

  // Hôtel 6 – Marseille
  { id: 601, hotelId: 6, number: "601", service: "Bloc Mer", classement: "Standard", prestations: ["WiFi", "Clim"], prix: 130 },
  { id: 602, hotelId: 6, number: "602", service: "Bloc Mer", classement: "Deluxe", prestations: ["WiFi", "TV", "Balcon"], prix: 190 },

  // Hôtel 7 – Bordeaux
  { id: 701, hotelId: 7, number: "701", service: "Étage Unique", classement: "Standard", prestations: ["WiFi"], prix: 100 },
  { id: 702, hotelId: 7, number: "702", service: "Étage Unique", classement: "Suite", prestations: ["WiFi", "TV", "Salon"], prix: 220 },

  // Hôtel 8 – Biarritz
  { id: 801, hotelId: 8, number: "801", service: "Vue Océan", classement: "Deluxe", prestations: ["WiFi", "Balcon", "Vue mer"], prix: 240 },
  { id: 802, hotelId: 8, number: "802", service: "Vue Océan", classement: "Suite", prestations: ["WiFi", "Jacuzzi", "Vue mer"], prix: 310 },
];

// ===============================
// RÉSERVATIONS
// ===============================
export const reservations = [
  { id: 1, roomId: 101, client: "Alice Martin", startDate: "2025-04-01", endDate: "2025-04-05", status: "CONFIRMED" },
  { id: 2, roomId: 103, client: "Nada", startDate: "2025-04-10", endDate: "2025-04-14", status: "CONFIRMED" },
  { id: 3, roomId: 201, client: "Iness", startDate: "2025-05-01", endDate: "2025-05-03", status: "CANCELLED" },
  { id: 4, roomId: 302, client: "Ziad", startDate: "2025-05-10", endDate: "2025-05-12", status: "CONFIRMED" },
  { id: 5, roomId: 402, client: "Yasmine", startDate: "2025-06-01", endDate: "2025-06-06", status: "CONFIRMED" },
  { id: 6, roomId: 502, client: "Karim", startDate: "2025-06-15", endDate: "2025-06-18", status: "CONFIRMED" },
  { id: 7, roomId: 602, client: "Sami", startDate: "2025-07-01", endDate: "2025-07-05", status: "CANCELLED" },
  { id: 8, roomId: 801, client: "Leila", startDate: "2025-07-10", endDate: "2025-07-15", status: "CONFIRMED" },
  { id: 9, roomId: 802, client: "Hugo", startDate: "2025-08-01", endDate: "2025-08-04", status: "CONFIRMED" },
];
