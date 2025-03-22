// resources/js/layouts/AdminLayout.js
import React, { useState, useEffect } from 'react';
import { Outlet, Link, useNavigate } from 'react-router-dom';
import { useAuth } from '../contexts/AuthContext';
import axios from 'axios';

const AdminLayout = () => {
    const { user, logout } = useAuth();
    const navigate = useNavigate();
    const [pendingVerifications, setPendingVerifications] = useState(0);

    useEffect(() => {
        // Récupérer le nombre de prestataires en attente de validation
        const fetchPendingVerifications = async () => {
            try {
                const response = await axios.get('/api/admin/pending-verifications-count');
                setPendingVerifications(response.data.count);
            } catch (error) {
                console.error('Erreur lors de la récupération des vérifications en attente:', error);
            }
        };

        fetchPendingVerifications();
        
        // Mettre à jour toutes les 5 minutes
        const interval = setInterval(fetchPendingVerifications, 5 * 60 * 1000);
        
        return () => clearInterval(interval);
    }, []);

    const handleLogout = async () => {
        const success = await logout();
        if (success) {
            navigate('/');
        }
    };

    return (
        <div className="d-flex flex-column min-vh-100">
            {/* Header */}
            <header className="bg-danger text-white py-3">
                <div className="container-fluid">
                    <div className="row align-items-center">
                        <div className="col-md-6">
                            <Link to="/admin" className="text-white text-decoration-none">
                                <h1 className="h4 mb-0">TourismConnect | Administration</h1>
                            </Link>
                        </div>
                        <div className="col-md-6">
                            <div className="d-flex justify-content-end">
                                <div className="dropdown">
                                    <button className="btn btn-outline-light dropdown-toggle" type="button" data-bs-toggle="dropdown">
                                        {user?.name || 'Administrateur'}
                                    </button>
                                    <ul className="dropdown-menu dropdown-menu-end">
                                        <li><Link className="dropdown-item" to="/admin/profile">Mon profil</Link></li>
                                        <li><hr className="dropdown-divider" /></li>
                                        <li><Link className="dropdown-item" to="/">Retour au site</Link></li>
                                        <li><button className="dropdown-item" onClick={handleLogout}>Déconnexion</button></li>
                                    </ul>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </header>

            {/* Contenu principal avec sidebar */}
            <div className="container-fluid flex-grow-1 py-4">
                <div className="row">
                    {/* Sidebar */}
                    <div className="col-md-3 col-lg-2">
                        <div className="list-group mb-4">
                            <Link to="/admin" className="list-group-item list-group-item-action" end="true">
                                <i className="fas fa-tachometer-alt me-2"></i> Tableau de bord
                            </Link>
                            <Link to="/admin/users" className="list-group-item list-group-item-action">
                                <i className="fas fa-users me-2"></i> Utilisateurs
                            </Link>
                            <Link to="/admin/services" className="list-group-item list-group-item-action">
                                <i className="fas fa-concierge-bell me-2"></i> Services
                            </Link>
                            <Link to="/admin/categories" className="list-group-item list-group-item-action">
                                <i className="fas fa-list me-2"></i> Catégories
                            </Link>
                            <Link to="/admin/cities" className="list-group-item list-group-item-action">
                                <i className="fas fa-city me-2"></i> Villes
                            </Link>
                            <Link to="/admin/verifications" className="list-group-item list-group-item-action d-flex justify-content-between align-items-center">
                                <span><i className="fas fa-user-check me-2"></i> Vérifications</span>
                                {pendingVerifications > 0 && (
                                    <span className="badge bg-danger rounded-pill">{pendingVerifications}</span>
                                )}
                            </Link>
                        </div>
                    </div>

                    {/* Contenu principal */}
                    <main className="col-md-9 col-lg-10">
                        <Outlet />
                    </main>
                </div>
            </div>

            {/* Footer */}
            <footer className="bg-dark text-white py-3 mt-auto">
                <div className="container">
                    <div className="small text-center">
                        &copy; {new Date().getFullYear()} TourismConnect. Tous droits réservés.
                    </div>
                </div>
            </footer>
        </div>
    );
};

export default AdminLayout;