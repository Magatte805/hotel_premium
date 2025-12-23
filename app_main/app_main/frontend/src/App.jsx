import { BrowserRouter, Routes, Route } from "react-router-dom";
import Home from "./components/Home";
import Login from "./components/Login";

function App() {
  return (
    <BrowserRouter>
      <Routes>
       <Routes>
  <Route path="/" element={<Home />} />
  <Route path="/home" element={<Home />} />
  <Route path="/login" element={<Login />} />
</Routes>

      </Routes>
    </BrowserRouter>
  );
}

export default App;
