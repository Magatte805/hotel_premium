import { BrowserRouter, Routes, Route, Navigate } from "react-router-dom";

import Home from "../pages/public/Home";
import Login from "../pages/public/Login";

import AdminLayout from "../layouts/AdminLayout";
import ClientLayout from "../layouts/ClientLayout";

import AdminDashboard from "../pages/admin/AdminDashboard";
import AdminHotels from "../pages/admin/AdminHotels";
import AdminRooms from "../pages/admin/AdminRooms";
import AdminWorks from "../pages/admin/AdminWorks";
import AdminOccupancy from "../pages/admin/AdminOccupancy";

import ClientDashboard from "../pages/client/ClientDashboard";
import ClientReservations from "../pages/client/ClientReservations";
import ClientAvailability from "../pages/client/ClientAvailability";

export default function AppRouter() {
  return (
    <BrowserRouter>
      <Routes>
        {/* Public */}
        <Route path="/" element={<Home />} />
        <Route path="/login" element={<Login />} />

        {/* Admin */}
        <Route path="/admin" element={<AdminLayout />}>
          <Route index element={<AdminDashboard />} />
          <Route path="hotels" element={<AdminHotels />} />
          <Route path="rooms" element={<AdminRooms />} />
          <Route path="works" element={<AdminWorks />} />
          <Route path="occupancy" element={<AdminOccupancy />} />
        </Route>

        {/* Client */}
        <Route path="/client" element={<ClientLayout />}>
          <Route index element={<ClientDashboard />} />
          <Route path="reservations" element={<ClientReservations />} />
          <Route path="availability" element={<ClientAvailability />} />
        </Route>

        {/* fallback */}
        <Route path="*" element={<Navigate to="/" replace />} />
      </Routes>
    </BrowserRouter>
  );
}
