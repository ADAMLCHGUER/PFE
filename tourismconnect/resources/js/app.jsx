import React from 'react';
import ReactDOM from 'react-dom/client';
import { BrowserRouter, Routes, Route, Navigate } from 'react-router-dom';
import axios from 'axios';

// Configuration globale d'Axios
axios.defaults.headers.common['X-Requested-With'] = 'XMLHttpRequest';
axios.defaults.withCredentials = true;

// Context pour l'authentification
import { AuthProvider } from './contexts/AuthContext';

// Composants de mise en page
import MainLayout from './layouts/MainLayout';
import AdminLayout from './layouts/AdminLayout';
import PrestataireLayout from './layouts/PrestataireLayout';

// Pages publiques
import Home from './pages/Home';
import ServiceDetails from './pages/ServiceDetails';
import ServiceList from './pages/ServiceList';
import Register from './pages/auth/Register';
import Login from './pages/auth/Login';
import PrestataireRegister from './pages/auth/PrestataireRegister';
import PendingApproval from './pages/PendingApproval';

// Importez vos autres composants de pages ici

// Route protégée
const ProtectedRoute = ({ requiredRole, children }) => {
    const { user, loading, hasRole } = useAuth();
    
    if (loading) {
        return <div>Chargement...</div>;
    }
    
    if (!user) {
        return <Navigate to="/login" />;
    }
    
    if (requiredRole && !hasRole(requiredRole)) {
        if (user.type === 'prestataire' && user.status === 'pending') {
            return <Navigate to="/pending-approval" />;
        }
        return <Navigate to="/" />;
    }
    
    return children;
};

function App() {
    return (
        <AuthProvider>
            <BrowserRouter>
                <Routes>
                    {/* Routes publiques */}
                    <Route path="/" element={<MainLayout />}>
                        <Route index element={<Home />} />
                        <Route path="services" element={<ServiceList />} />
                        <Route path="services/:id" element={<ServiceDetails />} />
                        <Route path="login" element={<Login />} />
                        <Route path="register" element={<Register />} />
                        <Route path="prestataire/register" element={<PrestataireRegister />} />
                        <Route path="pending-approval" element={<PendingApproval />} />
                    </Route>
                    
                    {/* Routes prestataires */}
                    <Route path="/prestataire" element={
                        <ProtectedRoute requiredRole="prestataire">
                            <PrestataireLayout />
                        </ProtectedRoute>
                    }>
                        {/* Ajoutez ici vos routes prestataires */}
                    </Route>
                    
                    {/* Routes admin */}
                    <Route path="/admin" element={
                        <ProtectedRoute requiredRole="admin">
                            <AdminLayout />
                        </ProtectedRoute>
                    }>
                        {/* Ajoutez ici vos routes admin */}
                    </Route>
                    
                    {/* Route 404 */}
                    <Route path="*" element={<Navigate to="/" />} />
                </Routes>
            </BrowserRouter>
        </AuthProvider>
    );
}

const root = ReactDOM.createRoot(document.getElementById('app'));
root.render(<App />);